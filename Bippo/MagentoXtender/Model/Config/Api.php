<?php
/**
 * @author hendy@bippo.co.id 
 * @copyright 2011 Bippo Indonesia. All Rights Reserved.
 */

class Bippo_MagentoXtender_Model_Config_Api extends Mage_Catalog_Model_Api_Resource
{
	public function get($configPath, $storeCode = null)
	{
		try {
			$value = Mage::getStoreConfig($configPath, $storeCode);
			return $value;
		} catch (Mage_Core_Exception $e) {
			$this->_fault('general_error');
		}		
	}
	
	/**
	 * Named 'save' because the underlying API is {@link Mage_Core_Model_Config#saveConfig()}.
	 * 
	 * Enter description here ...
	 * @param string $configPath
	 * @param string $value
	 * @param string $scope
	 * @param int $scopeId
	 */
	public function save($configPath, $value, $scope = 'default', $scopeId = 0)
	{
		try {
			$config = Mage::getConfig();
			// this will take effect at *next* invocation
			$config->saveConfig($configPath, $value, $scope, $scopeId);
		} catch (Mage_Core_Exception $e) {
			$this->_fault('general_error');
		}
	}
	
}