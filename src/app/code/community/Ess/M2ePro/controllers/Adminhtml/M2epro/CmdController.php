<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Adminhtml_M2epro_CmdController extends Ess_M2ePro_Controller_Adminhtml_CmdController
{
    //#############################################

    public function indexAction()
    {
        $this->printCommandsList();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('m2epro');
    }

    //#############################################

    /**
     * @title "Test"
     * @description "Command for quick development"
     * @new_line
     */
    public function testAction()
    {
        $this->printBack();
    }

    //#############################################
    
    /**
     * @title "PHP Info"
     * @description "View server phpinfo() information"
     */
    public function phpInfoAction()
    {
        if ($this->getRequest()->getParam('frame')) {
            phpinfo();
            return;
        }

        $this->printBack();
        $urlPhpInfo = $this->getUrl('*/*/*', array('frame' => 'yes'));
        echo '<iframe src="' . $urlPhpInfo . '" style="width:100%; height:90%;" frameborder="no"></iframe>';
    }

    /**
     * @title "ESS Configuration"
     * @description "Go to ess configuration edit page"
     */
    public function goToEditEssConfigAction()
    {
        $this->_redirect('*/adminhtml_config/ess');
    }

    /**
     * @title "M2ePro Configuration"
     * @description "Go to m2epro configuration edit page"
     * @new_line
     */
    public function goToEditM2eProConfigAction()
    {
        $this->_redirect('*/adminhtml_config/m2epro');
    }

    //#############################################

    /**
     * @title "Update License"
     * @description "Send update license request to server"
     */
    public function licenseUpdateAction()
    {
        Mage::getModel('M2ePro/License_Server')->updateStatus(true);
        Mage::getModel('M2ePro/License_Server')->updateLock(true);
        Mage::getModel('M2ePro/License_Server')->updateMessages(true);

        $this->_redirectUrl($this->getUrl('*/adminhtml_cmd/index'));
    }

    /**
     * @title "Server Connection"
     * @description "Send test request to server and check connection"
     * @new_line
     */
    public function serverCheckConnectionAction()
    {
        $this->printBack();

        $curlObject = curl_init();

        //set the server we are using
        $serverUrl = Mage::helper('M2ePro/Module')->getServerScriptsPath().'index.php';
        curl_setopt($curlObject, CURLOPT_URL, $serverUrl);

        // stop CURL from verifying the peer's certificate
        curl_setopt($curlObject, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlObject, CURLOPT_SSL_VERIFYHOST, false);

        // disable http headers
        curl_setopt($curlObject, CURLOPT_HEADER, false);

        // set the data body of the request
        curl_setopt($curlObject, CURLOPT_POST, true);
        curl_setopt($curlObject, CURLOPT_POSTFIELDS, http_build_query(array(),'','&'));

        // set it to return the transfer as a string from curl_exec
        curl_setopt($curlObject, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlObject, CURLOPT_CONNECTTIMEOUT, 300);

        $response = curl_exec($curlObject);

        echo '<h1>Response</h1><pre>';
        print_r($response);
        echo '</pre><h1>Report</h1><pre>';
        print_r(curl_getinfo($curlObject));
        echo '</pre>';

        echo '<h2 style="color:red;">Errors</h2>';
        echo curl_errno($curlObject) . ' ' . curl_error($curlObject) . '<br><br>';

        curl_close($curlObject);
    }

    //#############################################

    /**
     * @title "Clear COOKIES"
     * @description "Clear all current cookies"
     * @confirm "Are you sure?"
     */
    public function clearCookiesAction()
    {
        foreach ($_COOKIE as $name => $value) {
            setcookie($name, '', 0, '/');
        }
        $this->_redirectUrl($this->getUrl('*/adminhtml_cmd/index'));
    }

    /**
     * @title "Clear Extension Cache"
     * @description "Clear extension cache"
     * @confirm "Are you sure?"
     */
    public function clearExtensionCacheAction()
    {
        Mage::helper('M2ePro')->removeAllCacheValues();
        $this->_redirectUrl($this->getUrl('*/adminhtml_cmd/index'));
    }

    /**
     * @title "Clear Magento Cache"
     * @description "Clear magento cache"
     * @confirm "Are you sure?"
     */
    public function clearMagentoCacheAction()
    {
        Mage::helper('M2ePro/Magento')->clearCache();
        $this->_redirectUrl($this->getUrl('*/adminhtml_cmd/index'));
    }

    /**
     * @title "Check Upgrade to 3.2.0"
     * @description "Check extension installation"
     * @confirm "Are you sure?"
     * @new_line
     */
    public function checkInstallationCacheAction()
    {
        /** @var $installerInstance Ess_M2ePro_Model_Upgrade_MySqlSetup */
        $installerInstance = new Ess_M2ePro_Model_Upgrade_MySqlSetup('M2ePro_setup');

        /** @var $migrationInstance Ess_M2ePro_Model_Upgrade_Migration_ToVersion4 */
        $migrationInstance = Mage::getModel('M2ePro/Upgrade_Migration_ToVersion4');
        $migrationInstance->setInstaller($installerInstance);

        $migrationInstance->startSetup();
        $migrationInstance->migrate();
        $migrationInstance->endSetup();

        $this->_redirectUrl($this->getUrl('*/adminhtml_cmd/index'));
    }

    //#############################################

    private function processSynchTasks($tasks)
    {
        $shutdownFunctionCode = '';
        $configProfiler = Mage::helper('M2ePro/Module')->getConfig()->getAllGroupValues('/synchronization/profiler/');
        foreach ($configProfiler as $key => $value) {
            $shutdownFunctionCode .= "Mage::helper('M2ePro/Module')->getConfig()->setGroupValue('/synchronization/profiler/', '{$key}', '{$value}');";
        }
        $shutdownFunctionInstance = create_function('', $shutdownFunctionCode);
        register_shutdown_function($shutdownFunctionInstance);

        Mage::helper('M2ePro/Module')->getConfig()->setGroupValue('/synchronization/profiler/', 'mode', '3');
        Mage::helper('M2ePro/Module')->getConfig()->setGroupValue('/synchronization/profiler/', 'delete_resources', '0');
        Mage::helper('M2ePro/Module')->getConfig()->setGroupValue('/synchronization/profiler/', 'print_type', '2');

        session_write_close();

        /** @var $synchDispatcher Ess_M2ePro_Model_Synchronization_Dispatcher */
        $synchDispatcher = Mage::getModel('M2ePro/Synchronization_Dispatcher');
        $synchDispatcher->setInitiator(Ess_M2ePro_Model_Synchronization_Run::INITIATOR_DEVELOPER);
        $synchDispatcher->setComponents(array(
            Ess_M2ePro_Helper_Component_Ebay::NICK,
            Ess_M2ePro_Helper_Component_Amazon::NICK
        ));
        $synchDispatcher->setTasks($tasks);
        $synchDispatcher->setParams(array());
        $synchDispatcher->process();
    }

    //---------------------------------------------

    /**
     * @title "Synch Cron Tasks"
     * @description "Run all cron synchronization tasks as developer mode"
     * @confirm "Are you sure?"
     */
    public function synchCronTasksAction()
    {
        $this->printBack();
        $this->processSynchTasks(array(
              Ess_M2ePro_Model_Synchronization_Tasks::DEFAULTS,
              Ess_M2ePro_Model_Synchronization_Tasks::TEMPLATES,
              Ess_M2ePro_Model_Synchronization_Tasks::ORDERS,
              Ess_M2ePro_Model_Synchronization_Tasks::FEEDBACKS,
              Ess_M2ePro_Model_Synchronization_Tasks::MESSAGES,
              Ess_M2ePro_Model_Synchronization_Tasks::OTHER_LISTINGS
         ));
    }

    /**
     * @title "Synch Defaults"
     * @description "Run only defaults synchronization as developer mode"
     * @confirm "Are you sure?"
     */
    public function synchDefaultsAction()
    {
        $this->printBack();
        $this->processSynchTasks(array(
              Ess_M2ePro_Model_Synchronization_Tasks::DEFAULTS
         ));
    }

    /**
     * @title "Synch Templates"
     * @description "Run only templates synchronization as developer mode"
     * @confirm "Are you sure?"
     */
    public function synchTemplatesAction()
    {
        $this->printBack();
        $this->processSynchTasks(array(
              Ess_M2ePro_Model_Synchronization_Tasks::TEMPLATES
         ));
    }

    /**
     * @title "Synch Orders"
     * @description "Run only orders synchronization as developer mode"
     * @confirm "Are you sure?"
     */
    public function synchOrdersAction()
    {
        $this->printBack();
        $this->processSynchTasks(array(
              Ess_M2ePro_Model_Synchronization_Tasks::ORDERS
         ));
    }

    /**
     * @title "Synch Feedbacks"
     * @description "Run only feedbacks synchronization as developer mode"
     * @confirm "Are you sure?"
     */
    public function synchFeedbacksAction()
    {
        $this->printBack();
        $this->processSynchTasks(array(
              Ess_M2ePro_Model_Synchronization_Tasks::FEEDBACKS
         ));
    }

     /**
     * @title "Synch Messages"
     * @description "Run only messages synchronization as developer mode"
     * @confirm "Are you sure?"
     */
    public function synchMessagesAction()
    {
        $this->printBack();
        $this->processSynchTasks(array(
              Ess_M2ePro_Model_Synchronization_Tasks::MESSAGES
         ));
    }

    /**
     * @title "Synch Marketplaces"
     * @description "Run only marketplaces synchronization as developer mode"
     * @confirm "Are you sure?"
     */
    public function synchMarketplacesAction()
    {
        $this->printBack();
        $this->processSynchTasks(array(
              Ess_M2ePro_Model_Synchronization_Tasks::MARKETPLACES
         ));
    }

    /**
     * @title "Synch 3rd Party Listings"
     * @description "Run only 3rd party listings synchronization as developer mode"
     * @confirm "Are you sure?"
     * @new_line
     */
    public function synchOtherListingsAction()
    {
        $this->printBack();
        $this->processSynchTasks(array(
              Ess_M2ePro_Model_Synchronization_Tasks::OTHER_LISTINGS
         ));
    }

    //#############################################

    

    

    //#############################################
}