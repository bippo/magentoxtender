<?php

class Bippo_MagentoXtender_Model_Product_Attribute_Api extends Mage_Catalog_Model_Product_Attribute_Api
{
	//protected $_setup; //Mage_Eav_Model_Entity_Setup
	
	//$this->isInSet($setId) && $this->getData('attribute_set_info/' . $setId . '/group_id')
	
	public function items($setId)
    {
        $attributes = Mage::getModel('catalog/product')->getResource()
                ->loadAllAttributes()
                ->getSortedAttributes($setId);

        $result = array();

        foreach ($attributes as $attribute) {
            $result[] = array(
                'attribute_id' 					=> $attribute->getId(),
                'entity_type_id'                => $attribute->getEntityTypeId(),
                'attribute_code'         					=> $attribute->getAttributeCode(),
                'frontend_input'         		=> $attribute->getFrontendInput(),
                'frontend_class'				=> $attribute->getFrontendClass(),
                'is_required'     				=> $attribute->getIsRequired(),
                'is_global'        				=> $attribute->getIsGlobal(),
                'set_id'	   					=> $setId,
                'group_id'     					=> $attribute->getData('attribute_set_info/' . $setId . '/group_id'),
                'is_user_defined' 					=> $attribute->getIsUserDefined(),
                'position'	   					=> $attribute->getPosition(),
                'frontend_label' 				=> $attribute->getFrontendLabel(),
                'is_visible' 						=> $attribute->getIsVisible(),
                'default_value' 				=> $attribute->getDefaultValue(),
                'is_searchable' 					=> $attribute->getIsSearchable(),
                'is_filterable' 					=> $attribute->getIsFilterable(),
                'is_comparable' 					=> $attribute->getIsComparable(),
                'is_visible_on_front' 				=> $attribute->getIsVisibleOnFront(),
                'is_unique' 						=> $attribute->getIsUnique(),
                'is_configurable' 				=> $attribute->getIsConfigurable(),
                'apply_to' 						=> $attribute->getApplyTo(),
                'note' 							=> $attribute->getNote(),
                'is_visible_in_advanced_search'	=> $attribute->getIsVisibleInAdvancedSearch(),
                'is_used_for_price_rules' 			=> $attribute->getIsUsedForPriceRules(),
                'backend_model'					=> $attribute->getBackendModel(),
                'backend_type'					=> $attribute->getBackendType(),
            );
        }

        return $result;
    }
	
	public function options($attributeId, $storeID = null)
    {
    	return Mage::getModel('magentoxtender/entity_attribute_option_api')->items($attributeId);
    }

    private function getProductEntityTypeId()
    {
    	// app/code/core/Mage/Adminhtml/controllers/Catalog/Product/AttributeController.php line 43
    	return Mage::getModel('eav/entity')->setType(Mage_Catalog_Model_Product::ENTITY)->getTypeId();
    }

	/*
    <param><value><string>299cferdt7a6of2bmkk26d5e32</string></value></param>
    <param><value><string>catalog_product_attribute.create</string></value></param>
	<param>
		<value><array><data>
			<value>
				<struct><member><name>is_global</name><value><string>2</string></value></member>
					<member><name>apply_to</name><value><string></string></value></member>
					<member><name>is_configurable</name><value><string>0</string></value></member>
					<member><name>is_filterable</name><value><string>0</string></value></member>
					<member><name>frontend_input</name><value><string>select</string></value></member>
					<member><name>is_unique</name><value><string>0</string></value></member>
					<member><name>is_visible_on_front</name><value><string>1</string></value></member>
					<member><name>frontend_label</name><value><string>New attribute</string></value></member>
					<member><name>note</name><value><string></string></value></member>
					<member><name>is_required</name><value><string>0</string></value></member>
					<member><name>default_value</name><value><string></string></value></member>
					<member><name>is_comparable</name><value><i4>0</i4></value></member>
					<member><name>is_searchable</name><value><string>1</string></value></member>
					<member><name>is_visible</name><value><i4>1</i4></value></member>
					<member><name>position</name><value><i4>0</i4></value></member>
					<member><name>is_visible_in_advanced_search</name><value><string>1</string></value></member>
					<member><name>attribute_code</name><value><string>attr3</string></value></member>
				</struct>
			</value>
			<value><array><data>
				<value>
					<struct>
						<member>
							<name>values</name>
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
											<value><string>testlabel</string></value>
										</member>
									</struct>
									</value>
								</data></array>
							</value>
						</member>
						<member>
							<name>sort_order</name>
							<value><i4>0</i4></value>
						</member>
					</struct>
				</value>
			</data></array></value>
		</data></array></value>
	</param>
    */
    public function create(array $data, array $options = null,$setID = null, $groupID=null)
	{
		Mage::log($data);
		
		/* @var $newAttribute Mage_Catalog_Model_Resource_Eav_Attribute */
//         $newAttribute = Mage::getModel("catalog/entity_attribute");	// Magento 1.5.x.x and older
        $newAttribute = Mage::getModel('catalog/resource_eav_attribute');	// Magento 1.6.0.0

        // 1. Check if attribute code is already used
        $newAttribute->loadByCode($this->getProductEntityTypeId(), $data['attribute_code']);

        if($newAttribute->getAttributeId())
        {
            $this->_fault("attribute_code_already_exists");
        }

		$newAttribute->setEntityTypeId($this->getProductEntityTypeId());
		
        $newAttribute->setAttributeCode($data['attribute_code']);
        $newAttribute->setIsUserDefined(1);
        $newAttribute->setFrontendInput($data['frontend_input']);
        $newAttribute->setBackendType($newAttribute->getBackendTypeByInput($data['frontend_input']));
        $newAttribute->setFrondendLabel($data['frontend_label']);
        $defaultValueField = $newAttribute->getDefaultValueByInput($data['frontend_input']);

		if ($defaultValueField) {
            $newAttribute->setDefaultValue($data[$defaultValueField]);
        }

        if ($frontendInput == 'multiselect') {
            $newAttribute->setBackendModel('eav/entity_attribute_backend_array');
        }

        $newAttribute->addData($data);

		if(!is_null($setID) && !is_null($groupID))
		{
			$newAttribute->setAttributeSetId($setID);
        	$newAttribute->setAttributeGroupId($groupID);
		}
        
        $newAttribute->save();
		Mage::log('got here');

        if($newAttribute->usesSource())
        {
            $optionsModel = Mage::getModel("magentoxtender/entity_attribute_option_api");

            foreach($options as $option)
            {
                $optionsModel->create((int)$newAttribute->getAttributeId(), (int)$option['sort_order'],$option['values']);
            }
        }

        return (int)$newAttribute->getAttributeId();
	}
	/*
    <param><value><string>299cferdt7a6of2bmkk26d5e32</string></value></param>
    <param><value><string>catalog_product_attribute.update</string></value></param>
	<param>
		<value><array><data>
			<value>
				<struct><member><name>is_global</name><value><string>2</string></value></member>
					<member><name>apply_to</name><value><string></string></value></member>
					<member><name>is_configurable</name><value><string>0</string></value></member>
					<member><name>is_filterable</name><value><string>0</string></value></member>
					<member><name>frontend_input</name><value><string>select</string></value></member>
					<member><name>is_unique</name><value><string>0</string></value></member>
					<member><name>is_visible_on_front</name><value><string>1</string></value></member>
					<member><name>frontend_label</name><value><string>New attribute</string></value></member>
					<member><name>note</name><value><string></string></value></member>
					<member><name>is_required</name><value><string>0</string></value></member>
					<member><name>default_value</name><value><string></string></value></member>
					<member><name>is_comparable</name><value><i4>0</i4></value></member>
					<member><name>is_searchable</name><value><string>1</string></value></member>
					<member><name>is_visible</name><value><i4>1</i4></value></member>
					<member><name>position</name><value><i4>0</i4></value></member>
					<member><name>is_visible_in_advanced_search</name><value><string>1</string></value></member>
					<member><name>attribute_code</name><value><string>attr3</string></value></member>
					<member><name>attribute_id</name><value><string>attr3</string></value></member>
				</struct>
			</value>
			<value>
				<i4>38</i4><!-- setID -->
			</value>
			<value>
				<i4>38</i4><!-- groupID -->
			</value>
		</data></array></value>
	</param>
	*/
    public function update(array $data)
    {
    	$newAttribute = Mage::getModel("catalog/entity_attribute");

        $newAttribute->load($data['attribute_id']);

		if(!$newAttribute->getAttributeId())
		{
			$this->_fault("not_exists");
		}
		
		$newAttribute->setData($data);
        
        $newAttribute->save();
        
        return true;
    }
    
    /*
    <param><value><string>h6d3o0e7b49t04duormek8ss65</string></value></param>
    <param><value><string>catalog_product_attribute.delete</string></value></param>
	<param>
		<value><array><data>
			<value>
				<i4>461</i4>
			</value>
		</data></array></value>
	</param>
    
    */
    public function delete($attributeID)
    {
    	$modelAttribute = Mage::getModel("catalog/entity_attribute");
    	
		$modelAttribute->load($attributeID);
    	
		if(!$modelAttribute->getAttributeId())
		{
			$this->_fault("not_exists");
		}
    	
		$modelAttribute->delete();
    	
		return true;
    }
    /*
    <param><value><string>sfl1p3v862db7q8gg8eje5fo14</string></value></param>
    <param><value><string>catalog_product_attribute.deleteFromGroup</string></value></param>
	<param>
		<value><array><data>
			<value><i4>451</i4></value>
			<value><i4>43</i4></value>
			<value><i4>112</i4></value>
		</data></array></value>
	</param>
    
    */
    public function deleteFromGroup($attributeId, $setId, $groupId)
	{
		$attrCollection = Mage::getModel("catalog/entity_attribute")->getCollection();
		
		$attributes = $attrCollection->setAttributeSetFilter($setId)
									 ->setAttributeGroupFilter($groupId)
									 ->addFieldToFilter("entity_attribute.attribute_id",$attributeId)
									 ->load();

		if(!$attributes)
		{
			$this->_fault("not_exists");
		}
		
		$attribute = $attributes->getFirstItem();
		
		if(!$attribute)
		{
			$this->_fault("not_exists");
		}
		
		$attribute->deleteEntity();
		
		return true;
	}
	
	public function addToGroup($attributeId, $setId, $groupId)
	{
		$attribute = Mage::getModel("catalog/entity_attribute");
    	
		$attribute->load($attributeId);
		
		if(!$attribute)
		{
			$this->_fault("not_exists");
		}
		
		$attribute->setAttributeSetId($setId);
		$attribute->setAttributeGroupId($groupId);
		
		$attribute->save();
		
		return true;
	}
    
    /*
    <param><value><string>sfl1p3v862db7q8gg8eje5fo14</string></value></param>
    <param><value><string>catalog_product_attribute.form</string></value></param>
    */
	public function form()
	{
		/********************MAIN *************************/
		$fieldsMain = array();
		
		$fieldsMain['legend'] = Mage::helper('catalog')->__('Attribute Properties');
		
		$yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes')
            ));

        $fieldsMain['fields'][] = array('attribute_code', 'text', array(
            'name'  => 'attribute_code',
            'label' => Mage::helper('catalog')->__('Attribute Code'),
            'title' => Mage::helper('catalog')->__('Attribute Code'),
            'note'  => Mage::helper('catalog')->__('For internal use. Must be unique with no spaces'),
            'class' => 'validate-code',
            'required' => true,
        ));

        $scopes = array(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('catalog')->__('Store View'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('catalog')->__('Website'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('catalog')->__('Global'),
        );

        $fieldsMain['fields'][] = array('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('catalog')->__('Scope'),
            'title' => Mage::helper('catalog')->__('Scope'),
            'note'  => Mage::helper('catalog')->__('Declare attribute value saving scope'),
            'values'=> $scopes
        ));

        $fieldsMain['fields'][] = array('frontend_input', 'select', array(
            'name' => 'frontend_input',
            'label' => Mage::helper('catalog')->__('Catalog Input Type for Store Owner'),
            'title' => Mage::helper('catalog')->__('Catalog Input Type for Store Owner'),
            'value' => 'text',
            'values'=>  array(
                array(
                    'value' => 'text',
                    'label' => Mage::helper('catalog')->__('Text Field')
                ),
                array(
                    'value' => 'textarea',
                    'label' => Mage::helper('catalog')->__('Text Area')
                ),
                array(
                    'value' => 'date',
                    'label' => Mage::helper('catalog')->__('Date')
                ),
                array(
                    'value' => 'boolean',
                    'label' => Mage::helper('catalog')->__('Yes/No')
                ),
                array(
                    'value' => 'multiselect',
                    'label' => Mage::helper('catalog')->__('Multiple Select')
                ),
                array(
                    'value' => 'select',
                    'label' => Mage::helper('catalog')->__('Dropdown')
                ),
                array(
                    'value' => 'price',
                    'label' => Mage::helper('catalog')->__('Price')
                ),
                array(
                    'value' => 'gallery',
                    'label' => Mage::helper('catalog')->__('Gallery')
                ),
                array(
                    'value' => 'media_image',
                    'label' => Mage::helper('catalog')->__('Media Image')
                ),
            )
        ));

        $fieldsMain['fields'][] = array('default_value_text', 'text', array(
            'name' => 'default_value_text',
            'label' => Mage::helper('catalog')->__('Default value'),
            'title' => Mage::helper('catalog')->__('Default value'),
            'value' => '' //$model->getDefaultValue(),
        ));

        $fieldsMain['fields'][] = array('default_value_yesno', 'select', array(
            'name' => 'default_value_yesno',
            'label' => Mage::helper('catalog')->__('Default value'),
            'title' => Mage::helper('catalog')->__('Default value'),
            'values' => $yesno,
            'value' => 0//$model->getDefaultValue(),
        ));

        $fieldsMain['fields'][] = array('default_value_date', 'date', array(
            'name'  => 'default_value_date',
            'label' => Mage::helper('catalog')->__('Default value'),
            'title' => Mage::helper('catalog')->__('Default value'),
            //'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'value' => 0//$model->getDefaultValue(),
        ));

        $fieldsMain['fields'][] = array('default_value_textarea', 'textarea', array(
            'name' => 'default_value_textarea',
            'label' => Mage::helper('catalog')->__('Default value'),
            'title' => Mage::helper('catalog')->__('Default value'),
            'value' => 0//$model->getDefaultValue(),
        ));
        
        $fieldsMain['fields'][] = array('frontend_label', 'text', array(
            'name' => 'frontend_label',
            'label' => Mage::helper('catalog')->__('Label'),
            'title' => Mage::helper('catalog')->__('Label'),
            'value' => ""//$model->getDefaultValue(),
        ));

        $fieldsMain['fields'][] = array('is_unique', 'select', array(
            'name' => 'is_unique',
            'label' => Mage::helper('catalog')->__('Unique Value'),
            'title' => Mage::helper('catalog')->__('Unique Value (not shared with other products)'),
            'note'  => Mage::helper('catalog')->__('Not shared with other products'),
            'values' => $yesno,
        ));

        $fieldsMain['fields'][] = array('is_required', 'select', array(
            'name' => 'is_required',
            'label' => Mage::helper('catalog')->__('Values Required'),
            'title' => Mage::helper('catalog')->__('Values Required'),
            'values' => $yesno,
        ));

        $fieldsMain['fields'][] = array('frontend_class', 'select', array(
            'name'  => 'frontend_class',
            'label' => Mage::helper('catalog')->__('Input Validation for Store Owner'),
            'title' => Mage::helper('catalog')->__('Input Validation for Store Owner'),
            'values'=>  array(
                array(
                    'value' => '',
                    'label' => Mage::helper('catalog')->__('None')
                ),
                array(
                    'value' => 'validate-number',
                    'label' => Mage::helper('catalog')->__('Decimal Number')
                ),
                array(
                    'value' => 'validate-digits',
                    'label' => Mage::helper('catalog')->__('Integer Number')
                ),
                array(
                    'value' => 'validate-email',
                    'label' => Mage::helper('catalog')->__('Email')
                ),
                array(
                    'value' => 'validate-url',
                    'label' => Mage::helper('catalog')->__('Url')
                ),
                array(
                    'value' => 'validate-alpha',
                    'label' => Mage::helper('catalog')->__('Letters')
                ),
                array(
                    'value' => 'validate-alphanum',
                    'label' => Mage::helper('catalog')->__('Letters(a-zA-Z) or Numbers(0-9)')
                ),
            )
        ));

        $fieldsMain['fields'][] = array('apply_to', 'apply', array(
            'name'        => 'apply_to',
            'label'       => Mage::helper('catalog')->__('Apply To'),
            'values'      => Mage_Catalog_Model_Product_Type::getOptions(),
            'mode_labels' => array(
                'all'     => Mage::helper('catalog')->__('All Product Types'),
                'custom'  => Mage::helper('catalog')->__('Selected Product Types')
            ),
            'required'    => true
        ));

        $fieldsMain['fields'][] = array('is_configurable', 'select', array(
            'name' => 'is_configurable',
            'label' => Mage::helper('catalog')->__('Use To Create Configurable Product'),
            'values' => $yesno,
        ));

		/****************** FRONTEND **********************/
		$fieldsFrontend = array();

		$fieldsFrontend['legend'] = Mage::helper('catalog')->__('Frontend Properties');

        $fieldsFrontend['fields'][] = array('is_searchable', 'select', array(
            'name' => 'is_searchable',
            'label' => Mage::helper('catalog')->__('Use in quick search'),
            'title' => Mage::helper('catalog')->__('Use in quick search'),
            'values' => $yesno,
        ));

        $fieldsFrontend['fields'][] = array('is_visible_in_advanced_search', 'select', array(
            'name' => 'is_visible_in_advanced_search',
            'label' => Mage::helper('catalog')->__('Use in advanced search'),
            'title' => Mage::helper('catalog')->__('Use in advanced search'),
            'values' => $yesno,
        ));

        $fieldsFrontend['fields'][] = array('is_comparable', 'select', array(
            'name' => 'is_comparable',
            'label' => Mage::helper('catalog')->__('Comparable on Front-end'),
            'title' => Mage::helper('catalog')->__('Comparable on Front-end'),
            'values' => $yesno,
        ));


        $fieldsFrontend['fields'][] = array('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => Mage::helper('catalog')->__("Use In Layered Navigation<br/>(Can be used only with catalog input type 'Dropdown')"),
            'title' => Mage::helper('catalog')->__('Can be used only with catalog input type Dropdown'),
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('catalog')->__('No')),
                array('value' => '1', 'label' => Mage::helper('catalog')->__('Filterable (with results)')),
                array('value' => '2', 'label' => Mage::helper('catalog')->__('Filterable (no results)')),
            ),
        ));

        $fieldsFrontend['fields'][] = array('is_visible_on_front', 'select', array(
            'name' => 'is_visible_on_front',
            'label' => Mage::helper('catalog')->__('Visible on Catalog Pages on Front-end'),
            'title' => Mage::helper('catalog')->__('Visible on Catalog Pages on Front-end'),
            'values' => $yesno,
        ));
        
        return array("main" => $fieldsMain, "frontend" =>  $fieldsFrontend);
	}
}

?>