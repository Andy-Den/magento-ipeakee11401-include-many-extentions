<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Adminhtml_M2epro_AccountController extends Ess_M2ePro_Controller_Adminhtml_MainController
{
    //#############################################

    protected function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('m2epro/configuration')
             ->_title(Mage::helper('M2ePro')->__('M2E Pro'))
             ->_title(Mage::helper('M2ePro')->__('Configuration'))
             ->_title(Mage::helper('M2ePro')->__('Accounts'));

        $this->getLayout()->getBlock('head')->addJs('M2ePro/Plugin/DropDown.js')
                                            ->addCss('M2ePro/css/Plugin/DropDown.css');

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('m2epro/configuration/account');
    }

    //#############################################

    public function indexAction()
    {
        $this->_initAction()
             ->_addContent($this->getLayout()->createBlock('M2ePro/adminhtml_account'))
             ->renderLayout();
    }
    
    public function editAction()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $componentMode = Mage::getModel('M2ePro/Account')->load($id)->getComponentMode();
        return $this->_redirect('*/adminhtml_'.$componentMode.'_account/edit', array('id'=>$id));
    }

    public function accountGridAction()
    {
        $response = $this->loadLayout()->getLayout()->createBlock('M2ePro/adminhtml_account_grid')->toHtml();
        $this->getResponse()->setBody($response);
    }

    //#############################################

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $ids = $this->getRequest()->getParam('ids');

        if (is_null($id) && is_null($ids)) {
            $this->_getSession()->addError(Mage::helper('M2ePro')->__('Please select account(s) to remove'));
            return $this->_redirect('*/*/index');
        }

        $idsForDelete = array();
        !is_null($id) && $idsForDelete[] = (int)$id;
        !is_null($ids) && $idsForDelete = array_merge($idsForDelete,(array)$ids);

        $deleted = $locked = 0;
        foreach ($idsForDelete as $id) {

            /** @var $account Ess_M2ePro_Model_Account */
            $account = Mage::getModel('M2ePro/Account')->loadInstance($id);

            if ($account->isLocked()) {
                $locked++;
            } else {

                try {

                    Mage::getModel('M2ePro/Connector_Server_Ebay_Dispatcher')
                        ->processVirtual('account','delete','entity',
                                          array(), NULL,
                                          NULL,$account->getId(),NULL);

                } catch (Exception $e) {
                    $account->deleteObjectLocks();
                    $account->deleteInstance();
                    throw $e;
                }

                $account->deleteObjectLocks();
                $account->deleteInstance();
                $deleted++;
            }
        }

        $tempString = Mage::helper('M2ePro')->__('%count% record(s) were successfully deleted.');
        $deleted && $this->_getSession()->addSuccess(str_replace('%count%',$deleted,$tempString));

        $tempString = Mage::helper('M2ePro')->__('%count% record(s) are in use in General Template(s). Account must not be in use.');
        $locked && $this->_getSession()->addError(str_replace('%count%',$locked,$tempString));

        $this->_redirect('*/*/index');
    }
    
    //#############################################
}