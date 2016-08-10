<?php
class Balance_Datafeed_Adminhtml_Datafeed_DatafeedController extends Mage_Adminhtml_Controller_Action {

    protected function check_activation() {



        $activation_key = Mage::getStoreConfig("datafeed/license/activation_key");
        $get_online_license = Mage::getStoreConfig("datafeed/license/get_online_license");
        $activation_code = Mage::getStoreConfig("datafeed/license/activation_code");
        $base_url = Mage::getStoreConfig("web/secure/base_url");

        $registered_version = Mage::getStoreConfig("datafeed/license/version");
        $current_version = Mage::getConfig()->getNode("modules/Balance_Datafeed")->version;


        $license_request = "&rv=" . $registered_version . "&cv=" . $current_version . "&activation_key=" . $activation_key . "&domain=" . $base_url . "&store_code=" . Mage::app()->getStore()->getCode();
        if ($registered_version != $current_version && ($activation_code || !empty($activation_code))) {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("datafeed")->__("<u>Extension upgrade from v" . $registered_version . " to v" . $current_version . "</u>.<br> Your license must be updated.<br>Please, reload this page."));
            Mage::getConfig()->saveConfig("datafeed/license/activation_code", "", "default", "0");
        } elseif (!$activation_key) {

            Mage::getConfig()->saveConfig("datafeed/license/activation_code", "", "default", "0");
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("datafeed")->__("Your activation key is not yet registered.<br>
										<a href='" . $this->getUrl("adminhtml/system_config/edit/section/datafeed/") . "'>Go to system > configuration > Balance > Data Feed Manager</a>."));

            Mage::getConfig()->saveConfig("datafeed/license/activation_code", "", "default", "0");
        } elseif ($activation_key && (!$activation_code || empty($activation_code)) && !$get_online_license) {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("datafeed")->__("Your license is not yet activated.<br>
										<a target='_blank' href='http://www.balanceinternet.com.au/license_activation/?method=post" . $license_request . "'>Go to http://www.balanceinternet.com.au/license_activation/</a>"));

            Mage::getConfig()->saveConfig("datafeed/license/activation_code", "", "default", "0");
        } elseif ($activation_key && (!$activation_code || empty($activation_code)) && $get_online_license) {

            try {
                //$activation_code = file_get_contents("http://www.balanceinternet.com.au/license_activation/index.php?method=get&rv=" . $license_request);
                
            	$activation_code = '{"status":"success", "version":"0.0.1", "activation":"balance"}';

                $result = json_decode($activation_code);
                switch ($result->status) {
                    case "success" :
                        Mage::getConfig()->saveConfig("datafeed/license/version", $result->version, "default", "0");
                        Mage::getConfig()->saveConfig("datafeed/license/activation_code", $result->activation, "default", "0");
                        Mage::getConfig()->cleanCache();
                        Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("datafeed")->__($result->message));

                        break;
                    case "error" :
                        Mage::getSingleton("adminhtml/session")->addError(Mage::helper("datafeed")->__($result->message));
                        Mage::getConfig()->saveConfig("datafeed/license/activation_code", "", "default", "0");
                        Mage::getConfig()->cleanCache();
                        break;
                    default :
                        Mage::getSingleton("adminhtml/session")->addError(Mage::helper("datafeed")->__("An error occurs while connecting Balance Internet license server (500).<br>
							<a target='_blank' href='http://www.balanceinternet.com.au/license_activation/?method=post" . $license_request . "'>Go to http://www.balanceinternet.com.au/license_activation/</a>"));
                        Mage::getConfig()->saveConfig("datafeed/license/activation_code", "", "default", "0");
                        Mage::getConfig()->cleanCache();
                        break;
                }
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError(Mage::helper("datafeed")->__("An error occurs while connecting wyomind license server (404).<br>
										<a target='_blank' href='http://www.balanceinternet.com.au/license_activation/?method=post" . $license_request . "'>Go to http://www.balanceinternet.com.au/license_activation/</a>"));
                Mage::getConfig()->saveConfig("datafeed/license/activation_code", "", "default", "0");
                Mage::getConfig()->cleanCache();
                return;
            }
        }
    }

    protected function _initAction() {

        $this->loadLayout()
                ->_setActiveMenu('catalog/datafeed')
                ->_addBreadcrumb($this->__('Balance Data feed'), ('Balance Data feed'));

        return $this;
    }

    public function indexAction() {
        $this->check_activation();
        $this->_initAction()
                ->renderLayout();
    }

    public function editAction() {
        $this->check_activation();
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('datafeed/datafeed')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('datafeed_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('catalog/datafeed')->_addBreadcrumb(Mage::helper('datafeed')->__('Data Feed Manager'), ('Data Feed Manager'));
            $this->_addBreadcrumb(Mage::helper('datafeed')->__('Data Feed Manager'), ('Data Feed Manager'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()
                            ->createBlock('datafeed/adminhtml_datafeed_edit'))
                    ->_addLeft($this->getLayout()
                            ->createBlock('datafeed/adminhtml_datafeed_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('datafeed')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->check_activation();
        $this->_forward('edit');
    }

    public function saveAction() {
        $this->check_activation();
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {



            // init model and set data
            $model = Mage::getModel('datafeed/datafeed');

            if ($this->getRequest()->getParam('id')) {
                $model->load($this->getRequest()->getParam('id'));

            }


            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('datafeed')->__('The data feed configuration has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('continue')) {
                    $this->getRequest()->setParam('id', $model->getId());
                    $this->_forward('edit');
                    return;
                }


                // go to grid or forward to generate action
                if ($this->getRequest()->getParam('generate')) {
                    $this->getRequest()->setParam('feed_id', $model->getId());
                    $this->_forward('generate');
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                // save data in session
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                // redirect to edit form
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction() {
        $this->check_activation();
        // check if we know what should be deleted
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                // init model and delete
                $model = Mage::getModel('datafeed/datafeed');
                $model->setId($id);
                // init and load datafeed model


                $model->load($id);
                // delete file
                if ($model->getFeedName() && file_exists($model->getPreparedFilename())) {
                    unlink($model->getPreparedFilename());
                }
                $model->delete();
                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('datafeed')->__('The data feed configuration has been deleted.'));
                // go to grid
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                // display error message
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());

                $this->_redirect('*/*/');
                return;
            }
        }
        // display error message
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('datafeed')->__('Unable to find the data feed configuration to delete.'));
        // go to grid
        $this->_redirect('*/*/');
    }

    public function sampleAction() {

        // init and load datafeed model
        $id = $this->getRequest()->getParam('feed_id');
        

        $datafeed = Mage::getModel('datafeed/datafeed');
        $datafeed->setId($id);
        $datafeed->_limit = Mage::getStoreConfig("datafeed/system/preview");

        $datafeed->_display = true;

        // if datafeed record exists
        if ($datafeed->load($id)) {

            try {
                $content = $datafeed->generateFile();
                if ($datafeed->_demo) {
                    $this->_getSession()->addError(Mage::helper('datafeed')->__("Invalid license."));
                    Mage::getConfig()->saveConfig('datafeed/license/activation_code', '', 'default', '0');
                    Mage::getConfig()->cleanCache();
                    $this->_redirect('*/*/');
                }
                else
                    print($content);
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_redirect('*/*/');
            } catch (Exception $e) {

                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->addException($e, Mage::helper('datafeed')->__('Unable to generate the data feed.'));
                $this->_redirect('*/*/');
            }
        } else {
            $this->_getSession()->addError(Mage::helper('datafeed')->__('Unable to find a data feed to generate.'));
        }
    }

    public function generateAction() {

        // init and load datafeed model
        $id = $this->getRequest()->getParam('feed_id');

        $datafeed = Mage::getModel('datafeed/datafeed');
        $datafeed->setId($id);
        $limit = $this->getRequest()->getParam('limit');
        $datafeed->_limit = $limit;


        // if datafeed record exists
        if ($datafeed->load($id)) {


            try {
                $datafeed->generateFile();
                $ext = array(1 => 'xml', 2 => 'txt', 3 => 'csv');
                if ($datafeed->_demo) {
                    $this->_getSession()->addError(Mage::helper('datafeed')->__("Invalid license."));
                    Mage::getConfig()->saveConfig('datafeed/license/activation_code', '', 'default', '0');
                    Mage::getConfig()->cleanCache();
                }
                else
                    $this->_getSession()->addSuccess(Mage::helper('datafeed')->__('The data feed "%s" has been generated.', $datafeed->getFeedName() . '.' . $ext[$datafeed->getFeedType()]));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->addException($e, Mage::helper('datafeed')->__('Unable to generate the data feed.'));
            }
        } else {
            $this->_getSession()->addError(Mage::helper('datafeed')->__('Unable to find a data feed to generate.'));
        }

        // go to grid
        $this->_redirect('*/*/');
    }

}
