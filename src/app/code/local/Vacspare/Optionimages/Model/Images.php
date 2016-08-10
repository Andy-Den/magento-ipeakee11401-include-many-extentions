<?php
/**
 * Vacspare_Optionimages Extension
 *
 * @category    Local
 * @package     Vacspare_Optionimages
 * @author      dungnv (dungnv@arrowhitech.com)
 * @copyright   Copyright(c) 2011 Arrowhitech Inc. (http://www.arrowhitech.com)
 *
 */

/**
 *
 * @category   Local
 * @package    Vacspare_Optionimages
 * @author     dungnv <dungnv@arrowhitech.com>
 */
class Vacspare_Optionimages_Model_Images extends Mage_Core_Model_Abstract
{
    public function _construct() {
        parent::_construct();
        $this->_init('optionimages/images');
    }

    public function getFileNameImageImages($optionId)
    {
        $filename = $this->_getResource()->getFileNameImageImages($optionId);
        return $filename;
    }
}
