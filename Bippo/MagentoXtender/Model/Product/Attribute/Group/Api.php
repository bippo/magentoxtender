<?php

class Bippo_MagentoXtender_Model_Product_Attribute_Group_Api extends Mage_Catalog_Model_Api_Resource
{	
	/*
	<param><value><string>0ae334s1jc0lcgks9us19u1247</string></value></param>
	<param><value><string>catalog_product_attribute_group.list</string></value></param>
	<param><value>
	<array>
		<data>
			<value><i4>31</i4></value>
		</data>
	</array>
	</value></param>
	*/
	public function items($setId = null)
	{
		$groups = Mage::getModel('eav/entity_attribute_group')->getResourceCollection();
		
		if(!is_null($setId) && !empty($setId) && is_numeric($setId))
		{
			$groups->setAttributeSetFilter($setId);
		}
		
		$groups->load();

		$arrGroups = array();
		
		foreach($groups as $group)
		{
			$arrGroups[] = array(
								'attribute_group_id' 	=> $group->getAttributeGroupId(),
								'attribute_set_id' 		=> $group->getAttributeSetId(),
								'attribute_group_name' 	=> $group->getAttributeGroupName(),
								'sort_order' 			=> $group->getSortOrder(),
								'default_id' 			=> $group->getDefaultId()
								);
		}
		
		return $arrGroups;
	}
	
	/*
	<param><value><string>cl19t0dqhmheafqc0ccdeejc76</string></value></param>
	<param><value><string>catalog_product_attribute_group.create</string></value></param>
	<param>
		<value>
			<array>
				<data>
					<value><i4>26</i4></value>
					<value><string>Leonelle</string></value>
				</data>
			</array>
		</value>
	</param>
	*/
	public function create($setId, array $data)
	{
		try
		{
			// $attrOption = Mage_Eav_Model_Entity_Attribute_Group
			$attrOption = Mage::getModel("eav/entity_attribute_group");
			
			$attrOption->addData($data);
			
			// check if there already exists a group with the give groupname
			if($attrOption->itemExists())
			{
				$this->_fault("group_already_exists");
			}
			
			$attrOption->save();
			
			return (int)$attrOption->getAttributeGroupId();
		} 
		catch(Exception $ex)
		{
			return false;
		}
	}
	/*
	<param><value><string>cl19t0dqhmheafqc0ccdeejc76</string></value></param>
	<param><value><string>catalog_product_attribute_group.update</string></value></param>
	<param>
		<value>
			<array>
				<data>
					<value><i4>85</i4></value>
					<value><string>Leonelle2</string></value>
					<value><i4>85</i4></value>
					<value><i4>85</i4></value>
				</data>
			</array>
		</value>
	</param>
	*/
	public function update(array $data)
	{
		try
		{
			// $attrOption = Mage_Eav_Model_Entity_Attribute_Group
			$attrOption = Mage::getModel("eav/entity_attribute_group");
			
			$attrOption->load($data["attribute_group_id"]);
			
			// check if the requested group exists...
			if(!$attrOption->getAttributeGroupId())
			{
				$this->_fault("group_not_exists");
			}
			
			$attrOption->addData($data);
			
			$attrOption->save();
			
			return true;
		} 
		catch(Exception $ex)
		{
			return false;
		}
	}
	
	/*
	<param><value><string>cl19t0dqhmheafqc0ccdeejc76</string></value></param>
	<param><value><string>catalog_product_attribute_group.delete</string></value></param>
	<param>
		<value>
			<array>
				<data>
					<value><i4>85</i4></value>
				</data>
			</array>
		</value>
	</param>
	*/
	public function delete($groupId)
	{
		try
		{
			// $attrOption = Mage_Eav_Model_Entity_Attribute_Group
			$attrOption = Mage::getModel("eav/entity_attribute_group");
			
			$attrOption->load($groupId);
			
			// check if the requested group exists...
			if(!$attrOption->getAttributeGroupId())
			{
				$this->_fault("group_not_exists");
			}
			
			// save data
			$attrOption->delete();
			
			return true;
		} 
		catch(Exception $ex)
		{
			return false;
		}
	}
	
	/*public function addAttribute($groupID, $attributeID)
	{
		$linkAttrSetGroup = Mage::getResourceSingleton("core/resource")->getConnection("core_write");
		
		
		if ($setId && $groupId && $object->getEntityTypeId()) {
            $write = $this->_getWriteAdapter();
            $table = $this->getTable('entity_attribute');


            $data = array(
                'entity_type_id' => $object->getEntityTypeId(),
                'attribute_set_id' => $setId,
                'attribute_group_id' => $groupId,
                'attribute_id' => $attrId,
                'sort_order' => (($object->getSortOrder()) ? $object->getSortOrder() : $this->_getMaxSortOrder($object) + 1),
            );

            $condition = "$table.attribute_id = '$attrId'
                AND $table.attribute_set_id = '$setId'";
            $write->delete($table, $condition);
            $write->insert($table, $data);

        }
	}*/
	
	
	/*private function getSetup() 
	{
		if(!isset($this->_setup))
		{
			$this->_setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		}
		
		return $this->_setup;
	}*/
}

?>