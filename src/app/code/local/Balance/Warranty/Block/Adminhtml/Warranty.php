<?php
/**
 * Warranty adminhtml list block
 *
 * @category   Balance
 * @package    Balance_Warranty
 * @author     Carey Sizer <carey@balanceinternet.com.au>
 */

class Balance_Warranty_Block_Adminhtml_Warranty extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'warranty';
        $this->_headerText = Mage::helper('warranty')->__('View Warranties');
        parent::__construct();
        $this->_removeButton('add'); // it gets added by the parent
    }
    
    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock( 'warranty/adminhtml_warranty_grid',
            $this->_controller . '.grid')->setSaveParametersInSession(true) );
        
        
    }

}