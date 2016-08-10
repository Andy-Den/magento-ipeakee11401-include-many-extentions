<?php
class Itoris_Producttabsslider_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function checkConfiguration()
    {
        try {
            $db     = Mage::getSingleton('core/resource')->getConnection('core_write');
            $query  = 'show tables';
            $data   = $db->query($query)->fetchAll(Zend_Db::FETCH_ASSOC);
            $tables = array();
            foreach ($data as $arrtable) {
                foreach ($arrtable as $table)
                    $tables[] = $table;
            }
            $settingsTableName = Mage::getSingleton('core/resource')->getTableName('itoris_producttabsslider_settings');
            if (in_array($settingsTableName, $tables) === false) {
                $query = 'CREATE TABLE `' . $settingsTableName . '` (								`id` INT NOT NULL AUTO_INCREMENT ,								`enabled` INT NOT NULL DEFAULT \'1\',								`behavior` VARCHAR( 255 ) NOT NULL DEFAULT \'up\',								`theme` VARCHAR( 255 ) NOT NULL DEFAULT \'default\',								PRIMARY KEY ( `id` )								) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci';
                $db->query($query);
                $defaults = Mage::getModel('itoris_producttabsslider/settings')->getDefaults();
                $query    = 'insert into `' . $settingsTableName . '` values(1, ' . $defaults['enabled'] . ', \'' . $defaults['behavior'] . '\', \'' . $defaults['theme'] . '\' )';
                $db->query($query);
            }
            $tabsTableName = Mage::getSingleton('core/resource')->getTableName('itoris_producttabsslider_tabs');
            if (in_array($tabsTableName, $tables) === false) {
                $query = 'CREATE TABLE `' . $tabsTableName . '` (						`id` INT NOT NULL AUTO_INCREMENT ,						`alias` VARCHAR( 255 ) NOT NULL ,						`title` TEXT NOT NULL ,						`order` INT NOT NULL,						`text` TEXT NOT NULL,						PRIMARY KEY ( `id` )						) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;';
                $db->query($query);
            }
            $query = "show columns from " . $tabsTableName;
            if (count($db->query($query)->fetchAll(Zend_Db::FETCH_ASSOC)) == 5) {
                $query = "ALTER TABLE  `$tabsTableName` 								ADD  `pid` INT NOT NULL AFTER  `id` ,								ADD  `sid` INT NOT NULL AFTER  `pid` ,								ADD  `parent` INT NOT NULL AFTER  `sid` ,								ADD  `default` INT NOT NULL AFTER `parent`,								ADD  `enabled` INT NOT NULL AFTER  `default`,								ADD  `inh` INT NOT NULL AFTER `enabled`,								ADD  `mod` INT NOT NULL AFTER `inh` ;";
                $db->query($query);
            }
        }
        catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }
    public function isRegistered($writeErrors = false)
    {
        try {
            return Itoris_Installer_Client::isAdminRegistered($this->alias);
        }
        catch (Exception $e) {
            Mage::logException($e);
            if ($writeErrors) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            return false;
        }
    }
    public function getRegisterMessage()
    {
        return '<b style="color:red">' . 'Your copy of the component is not registered! All functions are disabled. Please register. ' . 'Enter your S/N to register:' . ' 						<input type="text" id="serial_num" style="width:200px" /> <button class="scalable" onclick="if (document.getElementById(\'serial_num\').value==\'\') return; register(document.getElementById(\'serial_num\').value); " >' . 'Register' . '</button>' . ' </b>						<form name="rcotRegister" action="" method="post">				<input id="sn" type="hidden" name="sn"  value="" />				<input id="fk" type="hidden" name="form_key" value="" />				<input type="hidden" name="registration" value="true" />			</form>			<script>				function register(sn){					document.getElementById(\'sn\').value = sn;					document.getElementById(\'fk\').value = FORM_KEY;					//document.rcotRegister.action = window.location.href					document.rcotRegister.submit();				}			</script>			';
    }
    public function tryRegister($action)
    {
        if ($action->getRequest()->isPost() && $action->getRequest()->getPost('registration', null) == 'true') {
            $sn = $action->getRequest()->getPost('sn', null);
            if ($sn == null) {
                Mage::getSingleton('adminhtml/session')->addError('Invalid serial number.');
                return false;
            }
            $sn = trim($sn);
            try {
                $response = Itoris_Installer_Client::registerCurrentStoreHost($this->alias, $sn);
                if ($response == 0) {
                    Mage::getSingleton('adminhtml/session')->addSuccess('The component has been registered!');
                } else {
                    Mage::getSingleton('adminhtml/session')->addError('Invalid serial number!');
                }
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
    }
    public function isRegisteredAutonomous()
    {
        return Itoris_Installer_Client::isRegisteredAutonomous($this->alias);
    }
    public function getNotRegisteredMessage()
    {
        return '<span style="color:red;">IToris Product Tabs Slider extension is not registered.</span>';
    }
    public function setCount($count)
    {
        $this->tabsCount = $count;
    }
    /**		 * Enter description here ...		 * @param Mage_Core_Block_Abstract $block		 * @param string		 * @return bool true if block output must be substituted by tabs html		 */
    public function addTabHtm($alias, $html)
    {
    }
    public function addTab(Mage_Core_Block_Abstract $block, $html, $name, $fillMode = false)
    {
        if (!$this->isActive()) {
            return $html;
        }
        if (!$this->isTab($name)) {
            return $html;
        }
        if (trim($html) == '') {
            return '';
        }
        if (isset($this->tabs[$name])) {
            return '';
        }
        $this->tabs[$name] = $block;
        $this->currentlyOutputted++;
        /*if($this->isMustBeSubstitutedNow()){				$this->deactivate();				$tabs = Mage::app()->getLayout()->createBlock('itoris_producttabsslider/tabs');				$tabs->setData('blocks', $this->tabs);				return $tabs->toHtml();			}*/
        return '';
    }
    public function getTabOutput()
    {
        if (!$this->isActive())
            return '';
        $this->deactivate();
        if (count($this->tabs) == 0)
            return '';
        $tabs = Mage::app()->getLayout()->createBlock('itoris_producttabsslider/tabs');
        $tabs->setData('blocks', $this->tabs);
        return $tabs->toHtml();
    }
    public function isTab($name)
    {
        if ($name == '')
            return false;
        $tab = Mage::getModel('itoris_producttabsslider/tab')->load($name, 'alias');
        return !($tab->getId() == null);
    }
    public function incTabsCount()
    {
        $this->tabsCount++;
    }
    public function isMustBeSubstitutedNow()
    {
        return $this->tabsCount == $this->currentlyOutputted;
    }
    public function activate()
    {
        $this->isActive = true;
    }
    public function deactivate()
    {
        $this->isActive = false;
    }
    public function isActive()
    {
        return $this->isActive;
    }
    private $tabsCount = 0;
    private $currentlyOutputted = 0;
    private $tabs = array();
    private $isActive = false;
    private $alias = 'tab_slider';
}
?>