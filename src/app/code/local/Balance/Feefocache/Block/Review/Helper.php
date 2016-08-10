<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Balance
 * @package    Feefocache
 * @copyright  Copyright (c) 2011 Balance
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class Balance_Feefocache_Block_Review_Helper extends Mage_Review_Block_Helper
{
    protected $_availableTemplates = array(
        'default' => 'feefocache/helper/summary.phtml',
        'short'   => 'feefocache/helper/summary_short.phtml'
    );
    
    public function getSummaryHtml($product, $templateType, $displayIfNoReviews)
    {
        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = 'default';
        }
        $this->setTemplate($this->_availableTemplates[$templateType]);
        
       // $this->setTemplate('feefocache/helper/summary_short.phtml');
        $this->setProduct($product);
        $this->setDisplayIfEmpty($displayIfNoReviews);
        return $this->toHtml();

    }
    
    public function getRatingSummary()
    {
        return 2;
    }


    
}