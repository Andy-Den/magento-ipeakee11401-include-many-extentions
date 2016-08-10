<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $status = $row->getData($this->getColumn()->getIndex());
        switch ($status) {
            case 0:
                $statusLabel = "Disabled";
                break;
            case 1:
                $statusLabel = "Enabled";
                break;
            default:
                $statusLabel = "None";
                break;
        }
        return $statusLabel;
    }

}

