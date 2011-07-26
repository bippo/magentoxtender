<?php

class Bippo_MagentoXtender_Model_Product_Link_Api extends Mage_Catalog_Model_Product_Link_Api //Mage_Core_Model_Abstract
{
	/** OVERWRITE ARRAY IN BASE CLASS */
    protected $_typeMap = array(
    	'related'       => Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED,
        'up_sell'       => Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL,
        'cross_sell'    => Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL,
        'grouped'       => Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED
    );
    
    public function types()
    {
        $types = array_keys($this->_typeMap);
        $types[] = "configurable";
        
        return $types;
    }
    
    /*
    <param><value><string>ov23u8imhv18qu7vqejosjiha0</string></value></param>
    <param><value><string>catalog_product_link.list</string></value></param>
	<param>
		<value><array><data>
			<value><string>configurable</string></value>
			<value><i4>711</i4></value>
		</data></array></value>
	</param>
    
    */
    public function items($type, $productId)
    {
    	if(array_key_exists($type,$this->_typeMap))
    	{
    		return parent::items($type, $productId);
    	}
		
		if($type=="configurable") {
			$product = $this->_initProduct($productId);
			
			$childProducts=array();

			$collection = $product->getTypeInstance()->getUsedProducts();//getUsedProductCollection();
			
			foreach($collection as $simpleProduct)
			{
            	$row = $simpleProduct->toArray();
            	
            	// naming convention
            	$row["product_id"] = $row["entity_id"];
            	$row["type"] = $row["type_id"];
            	$row["set"] = $row["attribute_set_id"];
				$childProducts[] = $row;// $simpleProduct->toArray();
			}

			return $childProducts;
		}
		
		return array();
    }
    
    /*
    <param><value><string>hrrv96vm6pb3cqub8dt99o6k43</string></value></param>
    <param><value><string>catalog_product_link.assign</string></value></param>
	<param>
		<value><array><data>
			<value><string>configurable</string></value>
			<value><i4>711</i4></value>
			<value>
				<array><data>
					<value><i4>160</i4></value>
					<value><i4>161</i4></value>
					<value><i4>162</i4></value>
				</data></array>
			</value>
		</data></array></value>
	</param>
    
    */
    public function assign($type, $productId, $linkedProductIds, $data = array())
    {
    	if(array_key_exists($type,$this->_typeMap))
    	{
			if(!is_array($linkedProductIds)) 
			{
				$arrIDs = array();
				
				if(is_numeric($linkedProductIds))
				{
					$arrIDs[] = $linkedProductIds;
				}
			} else {
				$arrIDs = $linkedProductIds;
			}
			
			$result = true;
			
			foreach($arrIDs as $linkedProductID)
			{
				$result = $result && parent::assign($type,$productId, $linkedProductID, $data);
			}
    		return $result;
    	}
		
		if($type=="configurable") {
	        $product = $this->_initProduct($productId);
			
			$tmpProductIds = $product->getTypeInstance()->getUsedProductIds();
			
			$productIds = array();
			
			foreach($tmpProductIds as $key => $prodId)
	    	{	
	    		$productIds[$prodId] = $prodId;
			}
	
			if(is_array($linkedProductIds))	{
				foreach($linkedProductIds as $prodId)
				{
					if(!key_exists($prodId,$productIds)) {$productIds[$prodId] = $prodId;}
				}
			} elseif(is_numeric($linkedProductIds))	{
				if(!key_exists($linkedProductIds,$productIds)) {$productIds[$linkedProductIds] = $linkedProductIds;}
			} else {
				return false;
			}
			
			$product->setConfigurableProductsData($productIds);
			
			$product->save();
			
			return true;
	    }
	    
	    return false;	
    }
    
    /*
    <param><value><string>hrrv96vm6pb3cqub8dt99o6k43</string></value></param>
    <param><value><string>catalog_product_link.remove</string></value></param>
	<param>
		<value><array><data>
			<value><string>configurable</string></value>
			<value><i4>711</i4></value>
			<value><i4>160</i4></value>
		</data></array></value>
	</param>
    
    */
    public function remove($type, $productId, $linkedProductIds)
    {
    	if(array_key_exists($type,$this->_typeMap))
    	{
    		return parent::remove($type, $productId, $linkedProductIds);
    	}
    	
		if($type=="configurable") {
	    	$product = $this->_initProduct($productId);
	    	
	    	$tmpProductIds = $product->getTypeInstance()->getUsedProductIds();
	    	
			$productIds = array();

	    	foreach($tmpProductIds as $key => $prodId)
	    	{
	    		
				if(is_array($linkedProductIds))
	    		{
	    			if(!in_array($prodId, $linkedProductIds))
	    			{
	    				
	    				$productIds[$prodId] = $prodId;
	    			}
	    		} 
				elseif(is_numeric($linkedProductIds))
	    		{
	    			if($prodId!=$linkedProductIds)
					{
						$productIds[$prodId] = $prodId;;
					}	
	    		}
	    	}

	    	$product->setConfigurableProductsData($productIds);
			
			$product->save();
			
			return true;
 		}
 		
 		return false;
    }
    
    private function _getStores()
    {
        $stores = Mage::getModel('core/store')
            ->getResourceCollection()
            ->setLoadDefault(true)
            ->load();
        return $stores;
    }
    
    /*
    <param><value><string>e3hlh8cqi3ph74c0h90sqmt094</string></value></param>
    <param><value><string>catalog_product_link.listSuperAttributes</string></value></param>
	<param>
		<value><array><data>
			<value><i4>711</i4></value>
		</data></array></value>
	</param>
    
    */
    
    public function listSuperAttributes($productIdOrSku)
    {
   		// check if product Exists
		$product = $this->_initProduct($productIdOrSku);
		
		if($product->getTypeId()!="configurable") $this->_fault("not a configurable product");
		
		$productId = $product->getEntityId();
		
    	$stores = $this->_getStores();
    	
    	$superAttributes=array();
    	
    	foreach($stores as $store)
    	{
	    	$product = $product = Mage::getModel('catalog/product')->setStoreId($store->getStoreId());
			$product->load($productId);
			$attrs = $product->getTypeInstance()->getConfigurableAttributesAsArray();
			
			foreach($attrs as $key=>$attr)
			{
				if(!isset($superAttributes[$attr["id"]]))
				{
					$superAttributes["".$attr["id"].""] = $attr;
					$superAttributes["".$attr["id"].""]["product_id"] = $product->getEntityId();
					$superAttributes["".$attr["id"].""]["product_super_attribute_id"] = $attr["id"];
					unset($superAttributes["".$attr["id"].""]["id"]);
				}
				
				unset($superAttributes["".$attr["id"].""]["label"]);
				
				$superAttributes["".$attr["id"].""]["labels"][$store->getStoreId()] = $attr["label"];
			}
    	}
    	
		return array_values($superAttributes);	
    }
    
    /*
    <param><value><string>hrrv96vm6pb3cqub8dt99o6k43</string></value></param>
    <param><value><string>catalog_product_link.createSuperAttribute</string></value></param>
	<param>
		<value><array><data>
			<value><i4>711</i4></value>
			<value><i4>74</i4></value>
			<value><i4>0</i4></value>
			<value><string>Kroketkleur</string></value>
			<value>
				<array><data>
					<value>
						<struct>
							<member>
								<name>value_index</name>
								<value><string>7</string></value>
							</member>
							<member>
								<name>is_percent</name>
								<value><string>0</string></value>
							</member>
							<member>
								<name>pricing_value</name>
								<value><string>199.0000</string></value>
							</member>
						</struct>
					</value>
				</data></array>
			</value>
		</data></array></value>
	</param>
    */
    public function createSuperAttribute($productIDorSku, $attributeID, $position, array $labels, array $prices = null)
    {
    	$product = $this->_initProduct($productIDorSku);

		$superAttr = Mage::getModel("catalog/product_type_configurable_attribute");
    	
    	$superAttr->setProductId($product->getId());
    	$superAttr->setAttributeId($attributeID);
    	$superAttr->setPosition($position);

    	if(is_string($labels))
    	{
    		$superAttr->setStoreId(0);
    		$superAttr->setLabel($labels);
    		$superAttr->save();
    	} 
		elseif(is_array($labels))
    	{
	    	foreach($labels as $storeID => $label)
	    	{
	    		$superAttr->setStoreId($storeID);
	    		$superAttr->setLabel($label);
	    		$superAttr->save();
	    	}	
    	}
    	
    	if(is_array($prices))
    	{
    		$superAttr->setValues($prices);
    		$superAttr->save();
    	}
    	
    	return (int)$superAttr->getId();
    }
    
    /*
	<param><value><string>hrrv96vm6pb3cqub8dt99o6k43</string></value></param>
    <param><value><string>catalog_product_link.setSuperAttributeValues</string></value></param>
	<param>
		<value><array><data>
			<value><i4>71</i4></value>
			<value>
				<array><data>
					<value>
						<struct>
							<member>
								<name>value_index</name>
								<value><string>7</string></value>
							</member>
							<member>
								<name>is_percent</name>
								<value><string>0</string></value>
							</member>
							<member>
								<name>pricing_value</name>
								<value><nil/></value>
							</member>
						</struct>
					</value>
					<value>
						<struct>
							<member>
								<name>value_index</name>
								<value><string>8</string></value>
							</member>
							<member>
								<name>is_percent</name>
								<value><string>0</string></value>
							</member>
							<member>
								<name>pricing_value</name>
								<value><nil/></value>
							</member>
						</struct>
					</value>
				</data></array>
			</value>
		</data></array></value>
	</param>
	*/
    public function setSuperAttributeValues($superAttributeID, array $prices)
    {
    	$superAttr = Mage::getModel("catalog/product_type_configurable_attribute");
    	$superAttr->load($superAttributeID);
    	
    	$superAttr->setValues($prices);
    	$superAttr->save();
    	return true;
    }
    /*
	<param><value><string>hrrv96vm6pb3cqub8dt99o6k43</string></value></param>
    <param><value><string>catalog_product_link.removeSuperAttribute</string></value></param>
	<param>
		<value><array><data>
			<value><i4>65</i4></value>
		</data></array></value>
	</param>
	*/
    public function removeSuperAttribute($superAttributeID)
    {
    	$superAttr = Mage::getModel("catalog/product_type_configurable_attribute");
    	$superAttr->load($superAttributeID);
    	
    	$superAttr->delete();

    	return true;
    }
}

?>