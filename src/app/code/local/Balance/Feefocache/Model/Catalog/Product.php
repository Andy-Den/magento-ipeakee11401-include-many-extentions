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

/**
 * 
 */
class Balance_Feefocache_Model_Catalog_Product extends Mage_Catalog_Model_Product
{


    /**
     * Returns rating average from feefo
     *
     * @return mixed
     */
    public function getRatingSummary()
    {
        
        return $this->_getData('feefo_reviews_average');
    }

  
}
