<?php
/**
 * Contact information to be added to customer
 *
 * @category   Balance
 * @package    Balance_Warranty
 * @author     Carey Sizer <carey@balanceinternet.com.au>
 */
class Balance_Warranty_Block_Registration_Contact extends Balance_Warranty_Block_Registration_Abstract
{
    protected function _construct()
    {
        $this->getRegistration()->setStepData('contact', array(
            'label'     => Mage::helper('warranty')->__('Contact Information'),
            'is_show'   => $this->isShow()
        ));
        parent::_construct();
    }
    
    public function getNextStep()
    {
        return 'confirmation';
    }
    
    public function getPreviousStep()
    {
        return 'product';
    }
}