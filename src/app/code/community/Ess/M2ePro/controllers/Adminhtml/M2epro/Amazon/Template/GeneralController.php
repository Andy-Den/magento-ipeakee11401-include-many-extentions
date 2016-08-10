<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Adminhtml_M2epro_Amazon_Template_GeneralController extends Ess_M2ePro_Controller_Adminhtml_MainController
{
    //#############################################

    protected function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('m2epro/templates')
             ->_title(Mage::helper('M2ePro')->__('M2E Pro'))
             ->_title(Mage::helper('M2ePro')->__('Templates'))
             ->_title(Mage::helper('M2ePro')->__('General Templates'));

        $this->getLayout()->getBlock('head')
             ->addJs('M2ePro/Template/AttributeSetHandler.js')
             ->addJs('M2ePro/Amazon/Template/GeneralHandler.js');

        return $this;
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('m2epro/templates/general');
    }

    //#############################################

    public function indexAction()
    {
        $this->_redirect('*/adminhtml_template_general/index');
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        // Check Exist Marketplaces
        //-------------------------
        if (Mage::helper('M2ePro/Component_Amazon')->getCollection('Marketplace')->addFieldToFilter('status',1)->getSize() == 0) {
            $this->_getSession()->addError(Mage::helper('M2ePro')->__('Please select and update Amazon marketplaces before adding new General Templates.'));
            return $this->_redirect('*/adminhtml_template_general/index');
        }
        //-------------------------

        // Check Exist Accounts
        //-------------------------
        if (Mage::getModel('M2ePro/Amazon_Account')->getCollection()->getSize() == 0) {
            $this->_getSession()->addError(Mage::helper('M2ePro')->__('Please add Amazon accounts before adding new General Templates.'));
            return $this->_redirect('*/adminhtml_template_general/index');
        }
        //-------------------------

        $id    = $this->getRequest()->getParam('id');
        $model = Mage::helper('M2ePro/Component_Amazon')->getModel('Template_General')->load($id);

        if ($id && !$model->getId()) {
            $this->_getSession()->addError(Mage::helper('M2ePro')->__('Template does not exist.'));
            return $this->_redirect('*/adminhtml_template_general/index');
        }

        //$data = array();
        //$model->addData($data);

        $templateAttributeSetsCollection = Mage::getModel('M2ePro/AttributeSet')->getCollection();
        $templateAttributeSetsCollection->addFieldToFilter('object_id', $id)
                                        ->addFieldToFilter('object_type', Ess_M2ePro_Model_AttributeSet::OBJECT_TYPE_TEMPLATE_GENERAL);

        $templateAttributeSetsCollection->getSelect()->reset(Zend_Db_Select::COLUMNS)
                                                     ->columns('attribute_set_id');

        $model->setData('attribute_sets', $templateAttributeSetsCollection->getColumnValues('attribute_set_id'));

        Mage::helper('M2ePro')->setGlobalValue('temp_data', $model);

        $this->_initAction()
             ->_addContent($this->getLayout()->createBlock('M2ePro/adminhtml_amazon_template_general_edit'))
             ->renderLayout();
    }

    //#############################################

    public function saveAction()
    {
        if (!$post = $this->getRequest()->getPost()) {
            return $this->_redirect('*/adminhtml_template_general/index');
        }

        $id = $this->getRequest()->getParam('id');
        $coreRes = Mage::getSingleton('core/resource');
        $connWrite = $coreRes->getConnection('core_write');

        // Base prepare
        //--------------------
        $data = array();
        //--------------------

        // tab: general
        //--------------------
        $keys = array(
            'title',
            'account_id',
            'marketplace_id'
        );

        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = $post[$key];
            }
        }

        $data['title'] = strip_tags($data['title']);
        //--------------------

        // Add or update model
        //--------------------
        $model = Mage::helper('M2ePro/Component_Amazon')->getModel('Template_General');
        is_null($id) && $model->setData($data);
        !is_null($id) && $model->load($id)->addData($data);
        $id = $model->save()->getId();
        //--------------------

        // Attribute sets
        //--------------------
        $oldAttributeSets = Mage::getModel('M2ePro/AttributeSet')
                                    ->getCollection()
                                    ->addFieldToFilter('object_type',Ess_M2ePro_Model_AttributeSet::OBJECT_TYPE_TEMPLATE_GENERAL)
                                    ->addFieldToFilter('object_id',(int)$id)
                                    ->getItems();
        foreach ($oldAttributeSets as $oldAttributeSet) {
            /** @var $oldAttributeSet Ess_M2ePro_Model_AttributeSet */
            $oldAttributeSet->deleteInstance();
        }

        if (!is_array($post['attribute_sets'])) {
            $post['attribute_sets'] = explode(',', $post['attribute_sets']);
        }
        foreach ($post['attribute_sets'] as $newAttributeSet) {
            $dataForAdd = array(
                'object_type' => Ess_M2ePro_Model_AttributeSet::OBJECT_TYPE_TEMPLATE_GENERAL,
                'object_id' => (int)$id,
                'attribute_set_id' => (int)$newAttributeSet
            );
            Mage::getModel('M2ePro/AttributeSet')->setData($dataForAdd)->save();
        }
        //--------------------

        $this->_getSession()->addSuccess(Mage::helper('M2ePro')->__('Template was successfully saved'));
        $this->_redirectUrl(Mage::helper('M2ePro')->getBackUrl('list',array(),array('edit'=>array('id'=>$id))));
    }

    //#############################################

    public function getMarketplacesForAccountAction()
    {
        $accountId = $this->getRequest()->getParam('account_id');

        if (is_null($accountId)) {
            exit(json_encode(array()));
        }

        /** @var $account Ess_M2ePro_Model_Amazon_Account */
        $account = Mage::getModel('M2ePro/Amazon_Account')->loadInstance($accountId);

        $marketplacesCollection = Mage::helper('M2ePro/Component_Amazon')->getCollection('Marketplace')
                                                                  ->addFieldToFilter('status', Ess_M2ePro_Model_Marketplace::STATUS_ENABLE)
                                                                  ->setOrder('title', 'ASC');

        if ($marketplacesCollection->getSize() <= 0) {
            exit(json_encode(array()));
        }

        $marketplaces = array();
        foreach ($marketplacesCollection->getItems() as $marketplace) {
            if ($account->isExistMarketplaceItem($marketplace->getId())) {
                $marketplaces[] = array(
                    'code'  => $marketplace->getId(),
                    'label' => $marketplace->getTitle()
                );
            }
        }

        exit(json_encode($marketplaces));
    }

    //#############################################
}