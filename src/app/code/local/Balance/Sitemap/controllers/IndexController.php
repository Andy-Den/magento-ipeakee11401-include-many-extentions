<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Balance_Sitemap_IndexController extends Mage_Core_Controller_Front_Action {
        
    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_START_SESSION, 1); // Do not start standart session
        $this->setFlag('', self::FLAG_NO_POST_DISPATCH, 1); 
        $this->setFlag('', self::FLAG_NO_COOKIES_REDIRECT, 0); 
        parent::preDispatch();
        return $this;
    }
    
    public function sitemapAction() 
    {        
       try 
       {             
            $handler = Mage::getSingleton('balance_sitemap/sitemap_handler');
            $storeId = Mage::app()->getStore()->getId();
            //headers
            $this->getResponse()->setHeader('content-type', 'text/xml');
            $this->getResponse()->setHeader('cache-control', 'no-cache, must-revalidate');
            $this->getResponse()->setHeader('pragma', 'no-cache');                 
            $this->getResponse()->sendHeaders();                     
            //send output
            $handler->outputXml($storeId);
            return;
            
        }catch(Exception $ex){
            Mage::logException($ex);
            $this->getResponse()->setHttpResponseCode(500);
            $this->getResponse()->sendHeaders();
            return;
        }                
    }
    
    
    public function robotsAction() 
    {        
       try 
       {             
            $handler = Mage::getSingleton('balance_sitemap/sitemap_handler');
            $storeId = Mage::app()->getStore()->getId();
            //headers
            $this->getResponse()->setHeader('content-type', 'text/plain');
            $this->getResponse()->setHeader('cache-control', 'no-cache, must-revalidate');
            $this->getResponse()->setHeader('pragma', 'no-cache');                 
            $this->getResponse()->sendHeaders();                     
            //send output
            $handler->outputRobotsTxt($storeId);
            return;    
        }catch(Exception $ex){
            Mage::logException($ex);
            $this->getResponse()->setHttpResponseCode(500);
            $this->getResponse()->sendHeaders();
            return;
        }
               
    }
   
}