<?php

class Balanceinternet_Shoppingdotcomfeed_Adminhtml_Shoppingdotcomfeed_CatalogController extends Mage_Adminhtml_Controller_action {

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

    /**
     * Save Product Ids from grid, update if the feed has been saved to otherwise insert.
     *
     * @param request params
     */
    public function massAddAction() {
        $productIds = $this->getRequest()->getParam('product_ids');
        $idFeed = $this->getRequest()->getParam('id_feed');

        // If this entry exists update, otherwise insert.
        if (Mage::getModel('shoppingdotcomfeed/feedproducts')->getResource()->checkProductsSaved($idFeed)) {
            Mage::getModel('shoppingdotcomfeed/feedproducts')->getResource()->updatedFeedProductsFromGrid($productIds, $idFeed);
            $this->_redirect("shoppingdotcomfeed/adminhtml_shoppingdotcomfeed/complete", array('id_feed' => $idFeed));
        } else {
            Mage::getModel('shoppingdotcomfeed/feedproducts')->getResource()->insertFeedProductsFromGrid($productIds, $idFeed);
            $this->_redirect("*/adminhtml_shoppingdotcomfeed/complete", array('id_feed' => $idFeed));
        }
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
            if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                $model->setCreatedTime(now())
                        ->setUpdateTime(now());
            } else {
                $model->setUpdateTime(now());
            }

            $model->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('shoppingdotcomfeed')->__('Feed was successfully saved'));
            Mage::getSingleton('adminhtml/session')->setFormData(false);

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
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
                $model = Mage::getModel('shoppingdotcomfeed/feed');

                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Feed was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('shoppingdotcomfeed/adminhtml_catalog_grid')->toHtml());
    }

}