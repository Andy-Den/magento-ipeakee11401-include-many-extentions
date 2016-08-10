<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento ENTERPRISE edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento ENTERPRISE edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Mobile
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 */
/**
 * AW Mobile Navigation Menu
 */
class AW_Mobile_Block_Catalog_Navigation extends Mage_Catalog_Block_Navigation
{

    /**
     * Navigation Frames Path
     */
    const NAVIGATION_FRAMES = 'navigation_frames';

    /**
     * Is Navigation Page Path
     */
    const IS_NAVIGATION_PAGE = 'is_navigate';

    /**
     * Navigation frames data
     * @var array
     */
    protected $_frames = array();

    /**
     * Class constructor
     * @return AW_Mobile_Block_Catalog_Navigation
     */
    protected function _construct()
    {
        Mage::register(self::IS_NAVIGATION_PAGE, true);
        $return = parent::_construct();
    }

    protected function  _prepareLayout()
    {
        $this->_prepareFrames();
        parent::_prepareLayout();
    }

    /**
     * Retrives active categories count
     * @param Varien_Object| $object
     * @return integer
     */
    public function __getChildrenCount($object)
    {        
        if ($object->getChildrenCount()){
            $count = 0;
            foreach($object->getChildren() as $child){
                if ($child->getIsActive()){
                    $count++;
                }
            }
            return $count;
        }

        return 0; 
    }

    /**
     * Retrives frame object
     * @param Varien_Object $object Category Tree Node
     * @return Varien_Object
     */
    protected function _getFrame(Varien_Object $object, $level = 1)
    {                
        $frame = new Varien_Object();
        $frame->setFrameId('category'.$object->getId());
        $frame->setFrameCategoryId($object->getId());
        $frame->setHeader($object->getName());
        $frame->setLevel($level + 1);
        $frame->setChildren($object->getChildren());
        $frame->setChildrenCount( $this->__getChildrenCount($object));

        if ( is_array($frame->getChildren()) || (is_object($frame->getChildren()) && (get_class($frame->getChildren()) == 'Varien_Data_Tree_Node_Collection') ) ){
            foreach ($frame->getChildren() as $child){
                $this->_frames[] = $this->_getFrame($child, ($level + 1));
            }
        }
        return $frame;
    }
    
    /**
     * Retrives is Prent Flag for category
     * @param Varien_Object $category
     * @return boolean
     */
    public function isParent($category)
    {        
        if (!$category){
            return false;
        }
        
        $max_depth = Mage::getStoreConfig('catalog/navigation/max_depth');
        
        if ($max_depth){
            return ( ($this->__getChildrenCount($category) > 0) && ($category->getLevel() <= $max_depth) );
        } else {
            return ($this->__getChildrenCount($category) > 0);
        }
        
        
        return true;
    }
    
    /**
     * Prepare navigation frames for Navigation page
     */
    protected function _prepareFrames()
    {
        if (count($this->getStoreCategories())){
            foreach ($this->getStoreCategories() as $category){
                $this->_frames[] = $this->_getFrame($category);
            }
        }
        Mage::register(self::NAVIGATION_FRAMES, $this->_frames);
    }

    /**
     * Retrives home page category Id
     * Required for history backup
     * 
     * @return integer
     */
    public function getHomeId()
    {
        return $this->getCurrentCategory()->getId();
    }
}
