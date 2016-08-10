<?php
/**
 * Warranty Registration block
 *
 * @category   Balance
 * @package    Balance_Warranty
 * @author     Carey Sizer <carey@balanceinternet.com.au>
 */
class Balance_Warranty_Block_Registration extends Balance_Warranty_Block_Registration_Abstract
{
    public function getSteps()
    {
        $steps = array();

        if (!$this->isCustomerLoggedIn()) {
            $steps['login'] = $this->getRegistration()->getStepData('login');
        }

        $stepCodes = array('product', 'contact', 'confirmation');
        
        foreach ($stepCodes as $step) {
            $steps[$step] = $this->getRegistration()->getStepData($step);
        }
        return $steps;
    }

    public function getActiveStep()
    {
        return $this->isCustomerLoggedIn() ? 'product' : 'login';
    }

}