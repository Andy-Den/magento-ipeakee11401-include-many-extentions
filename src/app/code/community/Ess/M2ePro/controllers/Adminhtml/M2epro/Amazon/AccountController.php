<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Adminhtml_M2epro_Amazon_AccountController extends Ess_M2ePro_Controller_Adminhtml_MainController
{
    //#############################################

    protected function _initAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('m2epro/configuration')
             ->_title(Mage::helper('M2ePro')->__('M2E Pro'))
             ->_title(Mage::helper('M2ePro')->__('Configuration'))
             ->_title(Mage::helper('M2ePro')->__('Amazon Accounts'));

        $this->getLayout()->getBlock('head')
             ->setCanLoadExtJs(true)
             ->addJs('M2ePro/Amazon/AccountHandler.js');

        return $this;
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('m2epro/configuration/account');
    }

    //#############################################

    public function indexAction()
    {
        return $this->_redirect('*/adminhtml_account/index');
    }

    //#############################################

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::helper('M2ePro/Component_Amazon')->getModel('Account')->load($id);

        if ($id && !$model->getId()) {
            $this->_getSession()->addError(Mage::helper('M2ePro')->__('Account does not exist.'));
            return $this->indexAction();
        }

        Mage::helper('M2ePro')->setGlobalValue('temp_data', $model);

        $this->_initAction()
             ->_addContent($this->getLayout()->createBlock('M2ePro/adminhtml_amazon_account_edit'))
             ->_addLeft($this->getLayout()->createBlock('M2ePro/adminhtml_amazon_account_edit_tabs'))
             ->renderLayout();
    }

    //#############################################

    public function saveAction()
    {
        if (!$post = $this->getRequest()->getPost()) {
            $this->_redirect('*/*/index');
        }

        $id = $this->getRequest()->getParam('id');

        // Base prepare
        //--------------------
        $data = array();
        //--------------------

        // tab: general
        //--------------------
        $keys = array(
            'title',
            'other_listings_synchronization',
            'other_listings_mapping_mode',
            'other_listings_move_mode'
        );
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = $post[$key];
            }
        }
        //--------------------

        // Mapping
        //--------------------
        $tempData = array();
        $keys = array(
            'mapping_general_id_mode',
            'mapping_general_id_priority',
            'mapping_general_id_attribute',

            'mapping_sku_mode',
            'mapping_sku_priority',
            'mapping_sku_attribute',

            'mapping_title_mode',
            'mapping_title_priority',
            'mapping_title_attribute'
        );
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $tempData[$key] = $post[$key];
            }
        }

        $mappingSettings = array();

        if (isset($tempData['mapping_general_id_mode']) &&
            $tempData['mapping_general_id_mode'] == Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_MAPPING_GENERAL_ID_MODE_CUSTOM_ATTRIBUTE) {
            $mappingSettings['general_id']['mode'] = (int)$tempData['mapping_general_id_mode'];
            $mappingSettings['general_id']['priority'] = (int)$tempData['mapping_general_id_priority'];
            $mappingSettings['general_id']['attribute'] = (string)$tempData['mapping_general_id_attribute'];
        }

        if (isset($tempData['mapping_sku_mode']) &&
            ($tempData['mapping_sku_mode'] == Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_MAPPING_SKU_MODE_DEFAULT || $tempData['mapping_sku_mode'] == Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_MAPPING_SKU_MODE_CUSTOM_ATTRIBUTE)) {
            $mappingSettings['sku']['mode'] = (int)$tempData['mapping_sku_mode'];
            $mappingSettings['sku']['priority'] = (int)$tempData['mapping_sku_priority'];
            $mappingSettings['sku']['attribute'] = (string)$tempData['mapping_sku_attribute'];
        }

        if (isset($tempData['mapping_title_mode']) &&
            ($tempData['mapping_title_mode'] == Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_MAPPING_TITLE_MODE_DEFAULT || $tempData['mapping_title_mode'] == Ess_M2ePro_Model_Amazon_Account::OTHER_LISTINGS_MAPPING_TITLE_MODE_CUSTOM_ATTRIBUTE)) {
            $mappingSettings['title']['mode'] = (int)$tempData['mapping_title_mode'];
            $mappingSettings['title']['priority'] = (int)$tempData['mapping_title_priority'];
            $mappingSettings['title']['attribute'] = (string)$tempData['mapping_title_attribute'];
        }

        $data['other_listings_mapping_settings'] = json_encode($mappingSettings);
        //--------------------

        // tab: orders
        //--------------------
        $keys = array(
            'orders_mode'
        );
        foreach ($keys as $key) {
            if (isset($post[$key])) {
                $data[$key] = $post[$key];
            }
        }

        $data['magento_orders_settings'] = array();

        // m2e orders settings
        //--------------------
        $tempKey = 'listing';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey]) ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'mode',
            'store_mode',
            'store_id'
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }
        //--------------------

        // 3rd party orders settings
        //--------------------
        $tempKey = 'listing_other';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey]) ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'mode',
            'product_mode',
            'store_id'
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }
        //--------------------

        // tax settings
        //--------------------
        $tempKey = 'tax';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey]) ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'mode'
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }
        //--------------------

        // customer settings
        //--------------------
        $tempKey = 'customer';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey]) ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'mode',
            'id',
            'website_id',
            'group_id',
            'subscription_mode'
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }

        $notificationsKeys = array(
            'customer_created',
            'order_created',
            'invoice_created'
        );
        $tempSettings = !empty($tempSettings['notifications']) ? $tempSettings['notifications'] : array();
        foreach ($notificationsKeys as $key) {
            if (in_array($key, $tempSettings)) {
                $data['magento_orders_settings'][$tempKey]['notifications'][$key] = true;
            }
        }
        //--------------------

        // status mapping settings
        //--------------------
        $tempKey = 'status_mapping';
        $tempSettings = !empty($post['magento_orders_settings'][$tempKey]) ? $post['magento_orders_settings'][$tempKey] : array();

        $keys = array(
            'mode',
            'new',
            'processing',
            'shipped'
        );
        foreach ($keys as $key) {
            if (isset($tempSettings[$key])) {
                $data['magento_orders_settings'][$tempKey][$key] = $tempSettings[$key];
            }
        }
        //--------------------

        // invoice/shipment settings
        //--------------------
        $data['magento_orders_settings']['invoice_mode'] = Ess_M2ePro_Model_Amazon_Account::MAGENTO_ORDERS_INVOICE_MODE_YES;
        $data['magento_orders_settings']['shipment_mode'] = Ess_M2ePro_Model_Amazon_Account::MAGENTO_ORDERS_SHIPMENT_MODE_YES;

        if (!empty($data['magento_orders_settings']['status_mapping']['mode']) &&
            $data['magento_orders_settings']['status_mapping']['mode'] == Ess_M2ePro_Model_Amazon_Account::MAGENTO_ORDERS_STATUS_MAPPING_MODE_CUSTOM) {

            !isset($post['magento_orders_settings']['invoice_mode']) && $data['magento_orders_settings']['invoice_mode'] = Ess_M2ePro_Model_Amazon_Account::MAGENTO_ORDERS_INVOICE_MODE_NO;
            !isset($post['magento_orders_settings']['shipment_mode']) && $data['magento_orders_settings']['shipment_mode'] = Ess_M2ePro_Model_Amazon_Account::MAGENTO_ORDERS_SHIPMENT_MODE_NO;
        }
        //--------------------

        //--------------------
        $data['magento_orders_settings'] = json_encode($data['magento_orders_settings']);
        //--------------------

        // Add or update model
        //--------------------
        $model = Mage::helper('M2ePro/Component_Amazon')->getModel('Account');
        is_null($id) && $model->setData($data);
        !is_null($id) && $model->loadInstance($id)->addData($data);
        $oldTitle = $model->getOrigData('title');
        $id = $model->save()->getId();
        //--------------------

        // Add or update server
        //--------------------

        /** @var $accountObj Ess_M2ePro_Model_Account */
        $accountObj = $model;

        /** @var $amazonAccountObj Ess_M2ePro_Model_Amazon_Account */
        $amazonAccountObj = $accountObj->getChildObject();

        if (!$accountObj->isLockedObject('server_synchronize')) {

            foreach (Mage::helper('M2ePro/Component_Amazon')->getCollection('Marketplace')->getItems() as $marketplaceObj) {

                /** @var $marketplaceObj Ess_M2ePro_Model_Marketplace */

                /** @var $amazonMarketplaceObj Ess_M2ePro_Model_Amazon_Marketplace */
                $amazonMarketplaceObj = $marketplaceObj->getChildObject();

                if (!$marketplaceObj->isStatusEnabled() || is_null($amazonMarketplaceObj->getDeveloperKey())) {
                    continue;
                }

                if (!isset($post['marketplace_mode_'.$marketplaceObj->getId()])) {
                    continue;
                }

                $tempMarketplaceData = $amazonAccountObj->getMarketplaceItem($marketplaceObj->getId());

                $dispatcherObject = Mage::getModel('M2ePro/Connector_Xfabric_Amazon_Dispatcher');

                if ((int)$post['marketplace_mode_'.$marketplaceObj->getId()] == 1) {

                    if (is_null($tempMarketplaceData)) {

                        $params = array(
                            'marketplace_id' => $marketplaceObj->getId(),
                            'user_merchant_id' => $post['marketplace_merchant_id_'.$marketplaceObj->getId()],
                            'user_marketplace_id' => $post['marketplace_marketplace_id_'.$marketplaceObj->getId()],
                            'related_store_id' => $post['marketplace_related_store_id_'.$marketplaceObj->getId()]
                        );
                        $dispatcherObject->processConnector('account', 'add' ,'entity', $params, NULL, $id);

                    } else {

                        $params = array(
                            'user_merchant_id' => $post['marketplace_merchant_id_'.$marketplaceObj->getId()],
                            'user_marketplace_id' => $post['marketplace_marketplace_id_'.$marketplaceObj->getId()],
                            'related_store_id' => $post['marketplace_related_store_id_'.$marketplaceObj->getId()],
                            'title' => $post['title']
                        );

                        if ($tempMarketplaceData['merchant_id'] != $params['user_merchant_id'] ||
                            $tempMarketplaceData['marketplace_id'] != $params['user_marketplace_id'] ||
                            $oldTitle != $params['title']) {

                            $dispatcherObject->processConnector('account', 'update' ,'entity', $params,
                                                                $marketplaceObj->getId(), $id);
                        } else {
                            $amazonAccountObj->updateMarketplaceItem($marketplaceObj->getId(),
                                                                     $params['user_merchant_id'],
                                                                     $params['user_marketplace_id'],
                                                                     $params['related_store_id']);
                        }
                    }

                } else {

                    if (!is_null($tempMarketplaceData)) {

                        $dispatcherObject->processConnector('account', 'delete' ,'entity', array(),
                                                            $marketplaceObj->getId(), $id);
                    }

                }
            }
        }
        //--------------------

        Mage::helper('M2ePro/Component_Amazon')->clearAllCache();
        $this->_getSession()->addSuccess(Mage::helper('M2ePro')->__('Account was successfully saved'));

        $this->_redirectUrl(Mage::helper('M2ePro')->getBackUrl('list',array(),array('edit'=>array('id'=>$id))));
    }

    public function deleteAction()
    {
        $this->_forward('delete','adminhtml_account');
    }
    
    //#############################################
}