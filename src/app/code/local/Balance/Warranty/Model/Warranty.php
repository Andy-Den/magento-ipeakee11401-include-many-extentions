<?php
/**
 * A warranty
 */
class Balance_Warranty_Model_Warranty extends Mage_Core_Model_Abstract
{
    /**
     * Initialize the resource model
     */
    protected function _construct()
    {
        $this->_init('warranty/warranty');
        parent::_construct();
    }
    
    /**
     * Get the time left on the warranty (in months)
     * @return int
     */
    public function getTimeLeft()
    {
        if(!$this->getDateOfPurchase() || !$this->getTerm()){
            return 'Unknown';
        }
        $purchaseDate = new DateTime($this->getDateOfPurchase());
        $expiryDate = new DateTime($this->getDateOfPurchase());
        $expiryDate->add(new DateInterval('P' . $this->getTerm() . 'Y'));
        $now = new DateTime();
        $difference = $now->diff($expiryDate);
        return $this->_convertDifferenceToMonths($difference);
    }
    
    /**
     * Calculate the number of total months for a given date interval
     * @param DateInvertal $interval
     * @return int
     */
    protected function _convertDifferenceToMonths(DateInterval $interval){
        
        $totalMonths = 0;
        $totalMonths += $interval->y * 12;
        $totalMonths += $interval->m;
        return $totalMonths;
    }
}