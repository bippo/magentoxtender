<?php

class Bippo_MagentoXtender_Model_Product_Attribute_Set_Api extends Mage_Catalog_Model_Product_Attribute_Set_Api
{
	/*
	<param><value><string>40phfuctek2kf85n8ioha14al2</string></value></param>
	<param><value><string>catalog_product_attribute_set.list</string></value></param>
	*/
	public function items()
    {
        $entityType = Mage::getModel('catalog/product')->getResource()->getEntityType();
        $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')->setEntityTypeFilter($entityType->getId());

        $result = array();
        
        foreach ($collection as $attributeSet) {
            $result[] = array(
                'attribute_set_id' 		=> $attributeSet->getAttributeSetId(),
                'attribute_set_name'   	=> $attributeSet->getAttributeSetName(),
                'sort_order'   			=> $attributeSet->getSortOrder()
            );

        }

        return $result;
    }
    
    private function getProductEntityTypeId()
    {
        return Mage::getModel('catalog/product')->getResource()->getEntityType();
    }
    
    /*
	<param><value><string>cl19t0dqhmheafqc0ccdeejc76</string></value></param>
	<param><value><string>catalog_product_attribute_set.create</string></value></param>
	<param>
		<value>
			<array>
				<data>
					<value><i4>26</i4></value>
					<value>
						<struct>
							<member>
								<name>attribute_set_name</name>
								<value><string>Groepsnaam 1</string></value>
							</member>
							<member>
								<name>sort_order</name>
								<value><i4>0</i4></value>
							</member>
						</struct>
					</value>
				</data>
			</array>
		</value>
	</param>
	*/
	public function create($basedOnSetID, array $data)
	{
		//try
		//{
			// $attrOption = Mage_Eav_Model_Entity_Attribute_Set
			$attrSet = Mage::getModel("eav/entity_attribute_set");
			
			try
			{
				$attrSet->setData($data);
				$attrSet->setEntityTypeId($this->getProductEntityTypeId());
				$attrSet->save();
			} 
			catch(Exception $ex)
			{
				echo "Fout in Deel 1";	
			}
			
			try
			{
				$attrSet->initFromSkeleton($basedOnSetID);
				$attrSet->save();
			}
			catch(Exception $ex)
			{
				echo "Fout in Deel 2";	
			}
			
			return (int)$attrSet->getAttributeSetId();
		//} 
		//catch(Exception $ex)
		//{
		//	return $ex;
		//}
	}
	
	/*
	<param><value><string>cl19t0dqhmheafqc0ccdeejc76</string></value></param>
	<param><value><string>catalog_product_attribute_set.update</string></value></param>
	<param>
		<value>
			<array>
				<data>
					<value>
						<struct>
							<member>
								<name>attribute_set_id</name>
								<value><i4>381</i4></value>
							</member>
							<member>
								<name>attribute_set_name</name>
								<value><string>Groepsnaam 1 (gewijzigd)</string></value>
							</member>
							<member>
								<name>sort_order</name>
								<value><i4>8</i4></value>
							</member>
						</struct>
					</value>
				</data>
			</array>
		</value>
	</param>
	*/
	public function update(array $data)
	{
		try
		{
			// $attrOption = Mage_Eav_Model_Entity_Attribute_Set
			$attrSet = Mage::getModel("eav/entity_attribute_set");
			
			$attrSet->load($data["attribute_set_id"]);
			
			if(!$attrSet->getAttributeSetId())
			{
				$this->_fault("set_not_exists");
			}
			
			$attrSet->addData($data);
			
			$attrSet->save();
			
			return true;
		} 
		catch(Exception $ex)
		{
			return false;
		}
	}
	
	public function delete($attributeSetId)
	{
		try
		{
			// $attrOption = Mage_Eav_Model_Entity_Attribute_Set
			$attrSet = Mage::getModel("eav/entity_attribute_set");
			
			$attrSet->load($attributeSetId);
			
			$attrSet->delete();
			
			return true;
		} 
		catch(Exception $ex)
		{
			return false;
		}
	}
}

?>