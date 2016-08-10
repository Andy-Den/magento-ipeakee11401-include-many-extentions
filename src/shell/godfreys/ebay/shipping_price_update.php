<?php
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'abstract.php';
/**
 * Update Ebay Shipping price.
 *
 * PHP version 5
 *
 * @category  Godfreys
 * @package   Godfreys_Shell
 * @author    Balance Internet Team <dev@balanceinternet.com.au>
 * @copyright 2015 Balance
 */
class Godfreys_Shell_Ebay_Update_Shipping extends Mage_Shell_Abstract
{
    const SHIPPING_PRICE          = 7;
    const PRODUCT_PRICE_THRESHOLD = 99;

    protected $_ebayStoreIds = array(4, 9, 10, 13, 14, 15, 7, 12, 3, 2);

    /**
     * Run update ebay shipping price
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->_ebayStoreIds as $storeId) {
            Mage::app()->getStore()->setId($storeId);

            $action = Mage::getModel('catalog/resource_product_action');

            $products = Mage::getModel('catalog/product')
                ->getCollection()
                ->addFinalPrice();

            foreach ($products as $product) {
                if ($product->getFinalPrice() <= self::PRODUCT_PRICE_THRESHOLD) {
                    $action->updateAttributes(
                        array($product->getId()),
                        array(
                            'ebayshipping' => self::SHIPPING_PRICE
                        ),
                        $storeId
                    );
                }

                $message = 'SKU: ' . $product->getSku();
                $message .= ', Final Price: ' . $product->getFinalPrice();
                $message .= ', Ebay Shipping Price: ' . $product->getEbayshipping();
                $message .= PHP_EOL;
                echo $message;
                Mage::log($message, null, 'ebay_shipping_price_update.log');
            }
        }
    }
}
$shell = new Godfreys_Shell_Ebay_Update_Shipping();
$shell->run();
