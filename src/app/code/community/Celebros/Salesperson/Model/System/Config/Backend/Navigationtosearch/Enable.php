<?php
/**
 * Celebros Qwiser - Magento Extension
 *
 * @category    Celebros
 * @package     Celebros_Salesperson
 * @author		Omniscience Co. - Dan Aharon-Shalom (email: dan@omniscience.co.il)
 *
 */
class Celebros_Salesperson_Model_System_Config_Backend_Navigationtosearch_Enable extends Mage_Core_Model_Config_Data
{
	/**
	 * 
	 *
	 * @return Celebros_Salesperson_Model_System_Config_Backend_Navigationtosearch_Enable
	 */
	protected function _afterSave()
	{
		$store_code=(Mage::app()->getRequest()->getParam('store')); // Current store scope

		
		//var_dump($store_code);
		if (!(isset($store_code)))
		{
			$websites=Mage::app()->getWebsites();
			$website=$websites['1'];
			$store_code=$website->getDefaultStore()->getCode();
		}
		//var_dump($store_code);
		//die;
		
		$currentStore=Mage::getModel( "core/store" )->load($store_code);
		$store_id=$currentStore->getId();
		$storeGroupId=$currentStore->getGroupId();


		
		
		$categories = Mage::getModel('catalog/category')->getCollection()
		->addAttributeToSelect(array('salesperson_use_in_nav'))
		->setStoreId($store_id)
		->load();
		
		$isNavAttrib=((bool) ($categories->getResource()->getAttribute('salesperson_use_in_nav')));

		if (!$isNavAttrib)
		{
			$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

			$setup->addAttribute('catalog_category', 'salesperson_use_in_nav',  array(
				'type'     => 'int',
				'label'    => 'Salesperson- Use category in nav2search',
				'input'    => 'select',
				'source'   => 'eav/entity_attribute_source_boolean',
				'global'   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
				'required' => false,
				'default'  => 1,
				'visible'       => 1,
				"visible_on_front"  => true,
				'group'    => 'General Information',
				'user_defined'  => 1
			));
			
			foreach($categories as $category)
			{
				$category->salesperson_use_in_nav=1;
				$category->save();
			}
		}
		
		$mageVersionInfo=Mage::getVersionInfo();
		
       	$isNewMethod=( ($mageVersionInfo['major']==1) && ($mageVersionInfo['minor']>=13) ) ? true : false;		
       	if ($isNewMethod)
       		return;
		if (($this->getData('groups/nav_to_search_settings/fields/nav_to_search/value') === "0") &&
				Mage::getStoreConfigFlag('salesperson/nav_to_search_settings/nav_to_search',$store_id))
		{
			/*if ($isNewMethod) {
				Mage::helper('salesperson')->updateCategoriesUrlRewrites($store_id, false);
				$stores = Mage::getModel('core/store')->getCollection()->addFieldToFilter('group_id',$storeGroupId);
				foreach($stores as $store)
				{
					if ($store_id!=$store->getId()) {
						$groups_value = array();
						$groups_value['nav_to_search_settings']['fields']['nav_to_search']['value'] = false; 
						Mage::getModel('adminhtml/config_data')
						    ->setSection('salesperson')
						    ->setWebsite(null)
						    ->setStore($store->getCode())
						    ->setGroups($groups_value)
						    ->save();   
					}
				}
			}
			else {*/
			$rewrites = Mage::helper('salesperson')->getCategoriesRewrites($store_id);
			foreach($rewrites as $rewrite)
			{
				$rewrite->delete();
				$rewrite->getResource()->commit();
			}
		
			$model = Mage::getModel('catalog/url');
		
			$store = Mage::app()->getStore($store_id);
			$model->refreshCategoryRewrite($store->getRootCategoryId(), $store_id, false);
			//}
		} // Check if activated
		elseif ($this->getData('groups/nav_to_search_settings/fields/nav_to_search/value') === "1")  /*||
				(Mage::getStoreConfigFlag('salesperson/nav_to_search_settings/nav_to_search',$store_id)))*/
		{
			/*if ($isNewMethod) {
				Mage::helper('salesperson')->updateCategoriesUrlRewrites($store_id, true);
				$searchBy=$this->getData('groups/nav_to_search_settings/fields/nav_to_search_use_full_category_path/value');
				$stores = Mage::getModel('core/store')->getCollection()->addFieldToFilter('group_id',$storeGroupId);
				foreach($stores as $store)
				{
					if ($store_id!=$store->getId()) {
						$groups_value = array();
						$groups_value['nav_to_search_settings']['fields']['nav_to_search']['value'] = true; 
						Mage::getModel('adminhtml/config_data')
						    ->setSection('salesperson')
						    ->setWebsite(null)
						    ->setStore($store->getCode())
						    ->setGroups($groups_value)
						    ->save();   
						$groups_value['nav_to_search_settings']['fields']['nav_to_search_use_full_category_path']['value'] = $searchBy; 
						Mage::getModel('adminhtml/config_data')
						    ->setSection('salesperson')
						    ->setWebsite(null)
						    ->setStore($store->getCode())
						    ->setGroups($groups_value)
						    ->save();   
					}
				}
			}
			else*/
				Mage::helper('salesperson')->updateCategoriesUrlRewrites($store_id);
		}
	}
}
