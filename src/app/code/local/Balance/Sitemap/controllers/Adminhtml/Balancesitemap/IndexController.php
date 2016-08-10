<?php

class Balance_Sitemap_Adminhtml_Balancesitemap_IndexController extends Mage_Adminhtml_Controller_Action{
   
    public function preDispatch()
    {       
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, 1); 
        $this->setFlag('', self::FLAG_NO_COOKIES_REDIRECT, 0); 
        parent::preDispatch();
        return $this;
    }   
    
    public function filepathcheckAction() {
        if (!$this->getRequest()->isAjax())
            exit();
        $handler = Mage::getSingleton('balance_sitemap/sitemap_handler');
        $message = 'Invalid Path';
        /**
         * @var Balance_Sitemap_Model_Sitemap 
         */        
        $path = $this->getRequest()->getPost('file_path');
        $errorCode = 9999;//initialise with default unknown error
        if ($path){
         $errorCode = $handler->isSitemapPathWritable($path,true); 
         $message = $handler->getMessageForCode($errorCode);
        }
        $data = array('code'=> $errorCode,'message'=>$message);
        $this->getResponse()->setBody(json_encode($data)); 
   }
 
}