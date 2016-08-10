<?php
/**
 * Wishlist sidebar block
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Balance_Allajax_Block_Wishlist_Customer_Sidebar extends Mage_Wishlist_Block_Customer_Sidebar
{
    /**
     * Retrieve wishlist helper
     *
     * @return Enterprise_Wishlist_Helper_Data
     */
    protected function _getHelper()
    {
        return $this->helper('enterprise_wishlist');
    }

    /**
     * Retrieve block title
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->_getHelper()->isMultipleEnabled()) {
            return $this->__('My Wishlists <small>(%d)</small>', $this->getItemCount());
        } else {
            return parent::getTitle();
        }
    }

    /**
     * Create wishlist item collection
     *
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createWishlistItemCollection()
    {
        return $this->_getHelper()->getWishlistItemCollection();
    }

    /**
     * Retrieve cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(
            parent::getCacheTags(),
            $this->getItemsTags($this->getWishlistItems())
        );
    }
}
