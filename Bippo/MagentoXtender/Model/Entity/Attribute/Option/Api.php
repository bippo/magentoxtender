<?php

class Bippo_MagentoXtender_Model_Entity_Attribute_Option_Api extends Mage_Catalog_Model_Api_Resource
{
    private function _getStores()
    {
        $stores = Mage::getModel('core/store')
            ->getResourceCollection()
            ->setLoadDefault(true)
            ->load();
        return $stores;
    }

    private function _getOptionValues(&$attributeObject)
    {
        $attributeType = $attributeObject->getFrontendInput();
        $defaultValues = $attributeObject->getDefaultValue();

        if ($attributeType == 'select' || $attributeType == 'multiselect') {
            $defaultValues = explode(',', $defaultValues);
        } else {
            $defaultValues = array();
        }

        $values = array();
        $optionCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($attributeObject->getId())
            ->setPositionOrder('desc')
            ->load();

        foreach ($optionCollection as $option) {
            $value = array();
            if (in_array($option->getId(), $defaultValues)) {
                $value['checked'] = '1';
            } else {
                $value['checked'] = '0';
            }

            //$value['frontend_input'] = $attributeType;
            $value['option_id'] = $option->getId();
            $value['sort_order'] = $option->getSortOrder();

            foreach ($this->_getStores() as $store) {
                $storeValues = $this->_getStoreOptionValues($attributeObject->getId(), $store->getId());

                if(!is_null($storeValues[$option->getId()]))
                {
                    $value['values'][] = $storeValues[$option->getId()];
                }
            }

            $values[] = $value;
        }

        return $values;
    }

    private function _getStoreOptionValues($attributeID, $storeId)
    {
        $storeValues = array();

        //if(!isset($storeValues[$storeId]))
        //{
            $storeValues[$storeId] = array();

            $valuesCollection = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($attributeID)
                ->setStoreFilter($storeId, false)
                ->load();

            foreach ($valuesCollection as $item) {
                $storeValues[$storeId][$item->getId()]['value'] = $item->getValue();
                $storeValues[$storeId][$item->getId()]['option_id'] = $item->getId();
                $storeValues[$storeId][$item->getId()]['store_id'] = $storeId;
            }
        //}

        return $storeValues[$storeId];
    }
    /*
    // kleur
    <param><value><string>skhhpc7cpqo7er704upith2e21</string></value></param>
	<param><value><string>eav_entity_attribute_option.list</string></value></param>
    <param><value>
        <array><data>
            <value><i4>74</i4></value> 
        </data></array>
    </value></param>
    */
    public function items($attributeID)
    {
        //return array();
		$attribute = Mage::getModel('eav/entity_attribute');
        $attribute->load($attributeID);

        // does it exists?
        if (!$attribute->getId()) {
            // it doesn't exist, so don't go any further
            $this->_fault('entity_attribute_not_exists');
        }

        $result = array();
        if ($attribute->usesSource()) {
            switch($attribute->getSourceModel())
            {         
                case "eav/entity_attribute_source_table":
                    $result = $this->_getOptionValues($attribute);
                break;
                default:
                    $result = $attribute->getSource()->getAllOptions();
                break;
            }
        }

        return $result;
    }
    /*
    <param><value><string>8vhkb6gdqoe4v619cihtajqui1</string></value></param>
	<param><value><string>eav_entity_attribute_option.create</string></value></param>
    <param><value>
        <array><data>
            <value><i4>458</i4></value><!--attributeID-->
            <value><i4>20</i4></value><!--SortOrder-->
            <value>
                <array><data>
                    <value>
                        <struct>
                            <member>
                                <name>store_id</name>
                                <value><i4>0</i4></value>
                            </member>
                            <member>
                                <name>value</name>
                                <value><string>Waarde 1</string></value>
                            </member>
                        </struct>
                    </value>
                </data></array>
            </value>
        </data></array>
    </value></param>
    */
    public function create($attributeID, $sortOrder, array $optionValues)
	{
        // check user input
        // 1. Is $attributeID an integer?
        if(!is_integer($attributeID))
        {
            $this->_fault('entity_attribute_not_exists');
        }

        // 2. Is $sortOrder an integer?
        if(!is_integer($sortOrder))
        {
            $this->_fault('invalid_sort_order');
        }

        // 3. Is $sourceValues an array and does it contain at least the value for the admin-store (store_id = 0)
        if(!is_array($optionValues))
        {
            $this->_fault('no_labels_provided');
        } else {
            $foundAdminStore = false;

            foreach($optionValues as $optionValue)
            {
                 if($optionValue['store_id'] == 0)
                 {
                     $foundAdminStore = true;
                     break;
                 }
            }

            if( $foundAdminStore == false)
            {
                $this->_fault('adminstore_label_required');
            }
        }

        // First check if the given attributeID exists
        $attribute = Mage::getModel('eav/entity_attribute');
        $attribute->load($attributeID);

        // does it exists?
        if (!$attribute->getId()) {
            // it doesn't exist, so don't go any further
            $this->_fault('entity_attribute_not_exists');
         }

        $oData = array();

        // to add an option
        $oData['order']["newOption"] = $sortOrder;

        foreach($optionValues as $optionValue)
        {
            $oData['value']["newOption"][$optionValue['store_id']] = $optionValue['value'];// = array("newOption" => $optionValues);
        }

        $oData['delete'] = array();

        $attribute->setOption($oData);

        $attribute->save();

        return true;
    }

    /*
    <param><value><string>1su97lolaq48pc6i8e72hs0ir0</string></value></param>
	<param><value><string>eav_entity_attribute_option.update</string></value></param>
    <param><value>
        <array><data>
            <value><string>369</string></value><!-- optionID -->
            <value><i4>1</i4></value>
            <value>
                <array><data>
                    <value>
                        <struct>
                            <member>
                                <name>store_id</name>
                                <value><string>0</string></value>
                            </member>
                            <member>
                                <name>value</name>
                                <value><string>1</string></value>
                            </member>
                            <member>
                                <name>option_id</name>
                                <value><string>369</string></value>
                            </member>
                        </struct>
                    </value>
                    <value>
                        <struct>
                            <member>
                                <name>store_id</name>
                                <value><string>1</string></value>
                            </member>
                            <member>
                                <name>value</name>
                                <value><string>Waarde StoreID 1</string></value>
                            </member>
                            <member>
                                <name>option_id</name>
                                <value><string>369</string></value>
                            </member>
                        </struct>
                    </value>
                </data></array>
            </value>
        </data></array>
    </value></param>
    
    MULTICALL
    <param><value><string>1su97lolaq48pc6i8e72hs0ir0</string></value></param>
    <param>
		<value><array><data>
			<value><array><data>
				<value><string>eav_entity_attribute_option.update</string></value>
				<value>
	                <array><data>
					    <value><string>369</string></value>
			            <value><i4>1</i4></value>
			            <value>
			                <array><data>
			                    <value>
			                        <struct>
			                            <member>
			                                <name>store_id</name>
			                                <value><string>0</string></value>
			                            </member>
			                            <member>
			                                <name>value</name>
			                                <value><string>1</string></value>
			                            </member>
			                            <member>
			                                <name>option_id</name>
			                                <value><string>369</string></value>
			                            </member>
			                        </struct>
			                    </value>
			                    <value>
			                        <struct>
			                            <member>
			                                <name>store_id</name>
			                                <value><string>1</string></value>
			                            </member>
			                            <member>
			                                <name>value</name>
			                                <value><string>Waarde StoreID 1</string></value>
			                            </member>
			                            <member>
			                                <name>option_id</name>
			                                <value><string>369</string></value>
			                            </member>
			                        </struct>
			                    </value>
			                </data></array>
		            	</value>
					</data></array>
				</value>
			</data></array></value>
		</data></array></value>
	</param>
    */
    public function update($optionID, $sortOrder, $optionValues)
	{
		//var_dump($optionValues);
        // check user input
        // 1. Is $attributeID an integer?
        if(!is_numeric($optionID))
        {

            return false;
        }

        // 2. Is $sortOrder an integer?
        if(!is_numeric($sortOrder))
        {
            return false;
        }

        // 3. Is $sourceValues an array and does it contain at least the value for the admin-store (store_id = 0)
        if(!is_array($optionValues))
        {
            return false;
        }

        $attributeID = $this->_getAttributeID($optionID);

        $attribute = Mage::getModel('eav/entity_attribute');
        $attribute->load($attributeID);

        // does it exists?
        if (!$attribute->getId()) {
            // it doesn't exist, so don't go any further
            $this->_fault('entity_attribute_not_exists');
        }

        $oData = array();

        // to add an option
        $oData['order']["newOption"] = $sortOrder;

        foreach($optionValues as $optionIndex => $optionValue)
        {
            $oData['value'][$optionID][$optionValue['store_id']] = $optionValue['value'];// = array("newOption" => $optionValues);
            $oData['order'][$optionID] = $sortOrder;
        }

        $attribute->setOption($oData);

        $attribute->save();

        return true;
    }

    /*
    <param><value><string>8vhkb6gdqoe4v619cihtajqui1</string></value></param>
	<param><value><string>eav_entity_attribute_option.delete</string></value></param>
    <param><value>
        <array><data>
            <value><i4>458</i4></value><!--optionID-->
        </data></array>
    </value></param>
    */
    public function delete($optionID)
    {
        // check user input
        // 1. Is $attributeID an integer?
        if(!is_integer($optionID))
        {
            return false;
        }

        $attributeID = $this->_getAttributeID($optionID);

        $attribute = Mage::getModel('eav/entity_attribute');
        $attribute->load($attributeID);

        // does it exists?
        if (!$attribute->getId()) {
            // it doesn't exist, so don't go any further
            $this->_fault('entity_attribute_not_exists');
        }

        $oData = array();
        $oData['value'][$optionID] = true;
        $oData['delete'][$optionID] = true;

        $attribute->setOption($oData);

        $attribute->save();

        return true;
    }

    protected function _getAttributeID($optionID)
    {
        $attribute_option = Mage::getModel("eav/entity_attribute_option")->load($optionID);

        // does the Entity Attribute Option exists?
        if(!$attribute_option->getOptionId())
        {
            // it doesn't exist
            $this->_fault('entity_attribute_option_not_exists');
        }

        return $attribute_option->getAttributeID();
    }
}

?>