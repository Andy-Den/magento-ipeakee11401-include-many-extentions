<?php

/**
 * Warranty admin controller
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Balance_Warranty_Adminhtml_WarrantyController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Customers list action
     */
    public function indexAction()
    {
        $this->_title($this->__('Warranties'))->_title($this->__('View Registered Warranties'));
//
//        if ($this->getRequest()->getQuery('ajax')) {
//            $this->_forward('grid');
//            return;
//        }
        $this->loadLayout();

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('sales/warranty');
        /**
         * Append customers block to content
         */
        $this->_addContent(
            $this->getLayout()->createBlock('warranty/adminhtml_warranty')
        );

        /**
         * Add breadcrumb item
         */
        $this->_addBreadcrumb(Mage::helper('warranty')->__('Warranties'), Mage::helper('warranty')->__('Warranties'));
        $this->_addBreadcrumb(Mage::helper('warranty')->__('View Registered Warranties'), Mage::helper('warranty')->__('View Registered Warranties'));

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody($this->getLayout()->createBlock('warranty/adminhtml_warranty_grid')->toHtml());
    }
     
    /**
     * Export estimates grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'warranties-' . date("Y-m-d:His") . '.csv';
        $content    = $this->getLayout()->createBlock('warranty/adminhtml_warranty_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Export estimates grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'warranties-' . date("Y-m-d:His") . '.xml';
        $content    = $this->getLayout()->createBlock('warranty/adminhtml_warranty_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }
    
    /**
     * Prepare file download response
     *
     * @todo remove in 1.3
     * @deprecated please use $this->_prepareDownloadResponse()
     * @see Mage_Adminhtml_Controller_Action::_prepareDownloadResponse()
     * @param string $fileName
     * @param string $content
     * @param string $contentType
     */
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $this->_prepareDownloadResponse($fileName, $content, $contentType);
    }


    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/warranty');
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array
     * @return array
     */
    protected function _filterPostData($data)
    {
        $data['account'] = $this->_filterDates($data['account'], array('dob'));
        return $data;
    }
}