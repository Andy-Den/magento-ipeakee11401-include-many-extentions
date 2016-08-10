<?php
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'abstract.php';

/**
 * Update Ebay Quantity.
 *
 * PHP version 5
 *
 * @category  Godfreys
 * @package   Godfreys_Shell
 * @author    Balance Internet Team <dev@balanceinternet.com.au>
 * @copyright 2016 Balance
 */
class Godfreys_Shell_Ebay_Update_Quantity extends Mage_Shell_Abstract
{
    const WAREHOUSE_ID_AU           = 1;
    const ENTITY_TYPE_ID_PRODUCT    = 4;
    const STOCK_ATTRIBUTE_CODE_EBAY = 'ebay_qty';

    /**
     * Run update ebay quantity
     *
     * @return void
     */
    public function run()
    {
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');


        $stockAttribute = Mage::getModel('eav/entity_attribute')->load(self::STOCK_ATTRIBUTE_CODE_EBAY, 'attribute_code');
        $attributeId    = $stockAttribute->getId();

        $query = '
          REPLACE INTO
            catalog_product_entity_varchar
                (
                    entity_type_id,
                    attribute_id,
                    store_id,
                    entity_id,
                    value
                )
                    SELECT ' .
                        self::ENTITY_TYPE_ID_PRODUCT . ', ' .
                        $attributeId . ' ,' .
                        '0'.' ,' .
                        'product_id ,
                        FLOOR(qty)
                    FROM cataloginventory_stock_item
                    WHERE stock_id=' . self::WAREHOUSE_ID_AU .';
        ';

        $writeConnection->query($query);

    }
}
$shell = new Godfreys_Shell_Ebay_Update_Quantity();
$shell->run();
