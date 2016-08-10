<?php

class Balanceinternet_Shoppingdotcomfeed_Adminhtml_Shoppingdotcomfeed_ShoppingdotcomfeedController extends Mage_Adminhtml_Controller_action {

    private $xml;

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('shoppingdotcomfeed/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Feeds Manager'), Mage::helper('adminhtml')->__('Feeds Manager'));
        return $this;
    }

    public function selectAction() {
        $this->_title($this->__('Select Products for: '))
                ->_title($this->__('Select Products'));

        $this->loadLayout();
        $this->renderLayout();
    }

    public function completeAction() {

        $this->_title($this->__('Catalog'))
                ->_title($this->__('Manage Products'));

        $this->loadLayout();
        $this->_setActiveMenu('shoppingdotcomfeed/items');

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Feed Manager'), Mage::helper('adminhtml')->__('Feed Manager'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Feed Managemet'), Mage::helper('adminhtml')->__('Feed Managemet'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('shoppingdotcomfeed/adminhtml_shoppingdotcomfeed_complete'));
        
        $this->renderLayout();
    }

    public function supportAction() {

        $this->_title($this->__('Catalog'))
                ->_title($this->__('Manage Products'));

        $this->loadLayout();
        $this->_setActiveMenu('shoppingdotcomfeed/items');

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Feed Manager'), Mage::helper('adminhtml')->__('Feed Manager'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Feed Managemet'), Mage::helper('adminhtml')->__('Feed Managemet'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('shoppingdotcomfeed/adminhtml_shoppingdotcomfeed_support'))
                ->_addLeft($this->getLayout()->createBlock('shoppingdotcomfeed/adminhtml_shoppingdotcomfeed_support_tabs'));

        $this->renderLayout();
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('shoppingdotcomfeed/shoppingdotcomfeed')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('shoppingdotcomfeed_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('shoppingdotcomfeed/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Feed Manager'), Mage::helper('adminhtml')->__('Feed Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Feed Managemet'), Mage::helper('adminhtml')->__('Feed Managemet'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('shoppingdotcomfeed/adminhtml_shoppingdotcomfeed_edit_help'));

            $this->_addContent($this->getLayout()->createBlock('shoppingdotcomfeed/adminhtml_shoppingdotcomfeed_edit'))
                    ->_addLeft($this->getLayout()->createBlock('shoppingdotcomfeed/adminhtml_shoppingdotcomfeed_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shoppingdotcomfeed')->__("Feeds don't exist"));
            $this->_redirect('*/*/');
        }
    }

    public function indexAction() {
        $this->_title($this->__('Catalog'))
                ->_title($this->__('Manage Products'));

        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        $data = $this->getRequest()->getPost();
        $model = Mage::getModel('shoppingdotcomfeed/shoppingdotcomfeed');
        $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

        try {
            if ($model->getCreatedAt() == NULL || $model->getUpdateAt() == NULL) {
                $model->setCreatedAt(now())
                        ->setUpdateAt(now());
            } else {
                $model->setUpdateAt(now());
            }

            $model->save();

            // Save filename - Only save the filename when you set up the module as it needs to be consistently the same. to upload to shopping.com
            if ($model->isObjectNew()) {
                $filename = 'sdc' . $model->getId() . '-magentofeed-au' . '-' . Mage::app()->getStore($model->getIdStore())->getCode() . $timeStamp = Mage::getModel('core/date')->timestamp(time()) . '.xml';
                $model->setFilename($filename);
                $model->save();
            }

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('shoppingdotcomfeed')->__('Feed was successfully saved!  Please select your products in your feed.'));
            Mage::getSingleton('adminhtml/session')->setFormData(false);

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }

            if ($model->isObjectNew())
                $this->_redirect("*/adminhtml_catalog/index", array('id_feed' => $model->getId(), 'store' => $model->getIdStore()));
            else
                $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {

            try {
                Mage::getModel('shoppingdotcomfeed/feed')->deleteFeed($this->getRequest()->getParam('id'));
                Mage::getModel('shoppingdotcomfeed/feedproducts')->deleteProductsFromFeed($this->getRequest()->getParam('id'));                               
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Feed was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

}