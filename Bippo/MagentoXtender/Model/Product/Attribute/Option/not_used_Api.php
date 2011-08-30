<?php

/**
 * @deprecated Use {@link Bippo_MagentoXtender_Model_Entity_Attribute_Option_Api}.
 * Enter description here ...
 * @author ceefour
 *
 */
class Bippo_MagentoXtender_Model_Product_Attribute_Option_Api extends Mage_Catalog_Model_Api_Resource
{
	protected $_setup; //Mage_Eav_Model_Entity_Setup
	
	protected $optionTable;
	protected $optionValueTable;
	
	/*
	<param><value><string>73m90kvitr8s2rffidbulg8946</string></value></param>
	<param><value><string>catalog_product_attribute_option.list</string></value></param>
	<param><value><array><data><value><i4>458</i4></value></data></array></value></param>
	*/
	public function items($attributeID=null, $optionID = null, $storeID=null)
	{
		$w = Mage::getSingleton('core/resource')->getConnection('core_read');
        
        $this->optionTable        = $this->getSetup()->getTable('eav/attribute_option');
        $this->optionValueTable   = $this->getSetup()->getTable('eav/attribute_option_value');
        
		$whereAttrID=null;
		$whereStoreID=null;
		$whereOptionID=null;
		
		if(is_integer($attributeID) && (int)$attributeID > 0)
		{
			$whereAttrID = $w->quoteInto('eao.attribute_id = ?',(int)$attributeID);
		}
		
		if(is_integer($optionID) && (int)$optionID > 0)
		{
			$whereOptionID = $w->quoteInto('eao.option_id = ?',(int)$optionID);
		}
		
		if(is_integer($storeID) && (int)$storeID >= 0)
		{
			$whereStoreID = $w->quoteInto('eaov.store_id = ?',(int)$storeID);
		}
		
		$where = ((is_null($whereAttrID) && is_null($whereOptionID) && is_null($whereStoreID)) ? "" : "WHERE ");
		$where .= (!is_null($whereAttrID) ? $whereAttrID : "");
		$where .= (strlen($where)>6 && !is_null($whereOptionID) ? " AND " : "");
		$where .= (!is_null($whereOptionID) ? $whereOptionID : "");
		$where .= (strlen($where)>6 && !is_null($whereStoreID) ? " AND " : "");
		$where .= (!is_null($whereStoreID) ? $whereStoreID : "");

		$result = $w->query('SELECT eao.attribute_id, eao.option_id, eao.sort_order, eaov.store_id, eaov.value_id, eaov.value FROM '.$this->optionTable.' AS eao LEFT JOIN '.$this->optionValueTable.' AS eaov ON eao.option_id = eaov.option_id '.$where.' ORDER BY eao.sort_order ASC, eao.option_id ASC, eaov.store_id ASC');
		
		if(!$result)
		{
			return false;
		}
		
		return $result->fetchAll(PDO::FETCH_ASSOC);
	}
	
	private function _optionValueExists($optionID, $value, $store_id)
	{
		$options = $this->items(null,$optionID,$store_id);
		
		foreach($options as $option)
		{
			if($option['value']==$value)
			{
				return $option['value_id'];
			}	
		}
		
		return 0;
	} 
	
	private function _optionValueIdExists($value_id)
	{
		$w = Mage::getSingleton('core/resource')->getConnection('core_read');
		$result = $w->query("SELECT value_id FROM ".$this->optionValueTable." WHERE value_id=?",array($value_id));
		if(!$result) return false;
		$tmpValue = $result->fetch(PDO::FETCH_ASSOC);
		if(!$tmpValue) return false;
		return ($value_id == $tmpValue["value_id"]);
	}
	
	/*
	$option = array(
					'value' 	=> array( $optionId => array( 0 => "valueLabel default store" 
															  1 => "..."
															)
																),
					'delete' 	=> array( $optionId => '1'),
					
					'attribute_id' => integer,
					'order'			=> array( $optionId => [getal])
					
					
					)
	*/
	public function create($attributeID, $sortOrder = 0, array $storeViewValues = array())
	{
		$option = array();
		
		$option['attribute_id'] = $attributeID;
		$option['order'] = $sortOrder;
		//$option['value']['newOption'] = array(0 => $baseStoreValue);
		
		if(is_array($storeViewValues))
		{
			foreach($storeViewValues as $storeId => $storeValue)
			{
				$option['value']['newOption'][$storeId] = $storeValue;
			}	
		}
		
		$this->getSetup()->addAttributeOption($option);
		
		return (int)$this->getSetup()->getConnection()->lastInsertId();
	}
	
	public function update($optionID, array $values=null, $sortOrder=-1)
	{
		// $w = Varien_Db_Adapter_Pdo_Mysql
		$w = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$this->optionTable        = $this->getSetup()->getTable('eav/attribute_option');
        $this->optionValueTable   = $this->getSetup()->getTable('eav/attribute_option_value');
		
		if(is_integer($sortOrder))
		{
			if((int)$sortOrder >= 0 )
			{
				$data = array('sort_order' => $w->quote($sortOrder));
		
				$result = $w->update($this->optionTable,$data,'option_id = '.$w->quote($optionID));
			}
		}

		if(is_array($values))
		{
			foreach($values as $optValue)
			{
				$store_id = $optValue['store_id'];
				$value_id = $optValue['value_id'];
				$value    = $optValue['value'];
				
				if(is_numeric($value_id) && $this->_optionValueIdExists((int)$value_id))//($tmpValueID = $this->_optionValueExists($optionID,$value, $store_id)) > 0)
				{
					$data = array('value' => $value);
					$result = $w->update($this->optionValueTable,$data,'value_id = '.$w->quote($value_id));
				} else {
					$data = array('option_id' => $optionID,'store_id' => $store_id, 'value' => $value);
					$result = $w->insert($this->optionValueTable,$data);
				}
			}	
		}
		
		return true;
	}
	
	public function delete($option_id)
	{
		$w = Mage::getSingleton('core/resource')->getConnection('core_write');
		$condition = $this->_conn->quoteInto('option_id=?', (int)$option_id);
        return $w->delete($this->optionTable, $condition);
	}
	
	public function deleteValue($value_id)
	{
		$w = Mage::getSingleton('core/resource')->getConnection('core_write');
		$condition = $this->_conn->quoteInto('value_id=?', (int)$value_id);
        return $w->delete($this->optionValueTable, $condition);
	}
    /*
    <param><value><string>8vhkb6gdqoe4v619cihtajqui1</string></value></param>
	<param><value><string>catalog_product_attribute_option.test</string></value></param>
	<param><value><array><data><value><i4>458</i4></value></data></array></value></param>
    *//*
	public function update($optionID, $sortOrder)
	{
        $attribute_option = Mage::getModel("eav/entity_attribute_option")->load( $optionID);

        // does the Entity Attribute Option exists?
        if(!$attribute_option->getOptionId())
        {
            // it doesn't exist
            $this->_fault('entity_attribute_option_not_exists');
        }
        else
        {
            // it exists, lets update the sort_order field
            $attribute_option->addData(array('sort_order' => $sortOrder));
            // save the changes to database
            $attribute_option->save();
        }

        return true;
    }   */
        //var_dump($attribute_option);
//        echo get_class($attribute_option);
		/*$productAttributes = Mage::getModel("catalog/product")->getCollection();
		$productAttributes->addAttributeToFilter('entity_id', 9);
		$productAttributes->addAttributeToSelect("*");
		$productAttributes->load();
		*/
		/*foreach($productAttributes as $prodAttr)
		{
			echo get_class($prodAttr);
		}*/
		
		/*$productAttributes = Mage::getModel("catalog/product")->getResource()->loadAllAttributes()->getSortedAttributes();;

		foreach($productAttributes as $attrCode => $attribute)
		{
			if($attribute->usesSource())
			{
				echo $attrCode . ": "."\r\n";
				
				// $source = Mage_Eav_Model_Entity_Attribute_Source_Table
				$source = $attribute->getSource();
				
				$options = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attribute->getId())
                //->setStoreFilter($attribute->getStoreId())
                ->setPositionOrder('asc')
                ->load();
                foreach($options as $optionID => $option)
                {
                	// $option = Mage_Eav_Model_Entity_Attribute_Option
					echo "\t".$optionID . "-".get_class($option)."\r\n";
				}
				
				echo "\r\n";
			}
			//echo $attrID ." - " . get_class($attribute)."\r\n";
		}     */
		
		/*$products = Mage::getResourceModel('catalog/product_collection')
                ->setStoreId($this->getSelectedStoreId())
                ->addIdFilter($productsIds);*/
//echo get_class(Mage::getResourceModel('catalog/product_collection'));

		//return false;
		
	//}
	
	private function getSetup() 
	{
		if(!isset($this->_setup))
		{
			$this->_setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		}
		
		return $this->_setup;
	}
	
	private function getProductEntityTypeId()
	{
		return $this->getSetup()->getEntityTypeId('catalog_product');
	}
}

?>