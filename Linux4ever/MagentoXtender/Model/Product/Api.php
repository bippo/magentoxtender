<?php

class Linux4ever_MagentoXtender_Model_Product_Api extends Mage_Catalog_Model_Product_Api
{
	public function items($filters = null, $store = null)
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->setStoreId($this->_getStoreId($store))
            ->addAttributeToSelect('*');

        if (is_array($filters)) {
            try {
                foreach ($filters as $field => $value) {
                    if (isset($this->_filtersMap[$field])) {
                        $field = $this->_filtersMap[$field];
                    }

                    $collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }

        $result = array();

        foreach ($collection as $product) {
            $arrProduct = array( // Basic product data
                'product_id' => $product->getId(),
                'sku'        => $product->getSku(),
                'name'       => $product->getName(),
                'set'        => $product->getAttributeSetId(),
                'type'       => $product->getTypeId(),
                'categories' => $product->getCategoryIds(),
            	'websites'   => $product->getWebsiteIds()
            );
            
            foreach ($product->getTypeInstance()->getEditableAttributes() as $attribute) {
	            if ($this->_isAllowedAttribute($attribute, $attributes)) {
	                $arrProduct[$attribute->getAttributeCode()] = $product->getData($attribute->getAttributeCode());
	            }
	        }
	        $result[] = $arrProduct;
        }

        return $result;
    }

    /**
     * Retrieve product info
     *
     * @param int|string $productId
     * @param string|int $store
     * @param array $attributes
     * @return array
     */
    public function info($productId, $store = null, $attributes = null)
    {
        $product = $this->_getProduct($productId, $store);

        if (!$product->getId()) {
            $this->_fault('not_exists');
        }

        $result = array( // Basic product data
            'product_id' => $product->getId(),
            'sku'        => $product->getSku(),
            'set'        => $product->getAttributeSetId(),
            'type'       => $product->getTypeId(),
            'categories' => $product->getCategoryIds(),
            'websites'   => $product->getWebsiteIds()
        );

        foreach ($product->getTypeInstance()->getEditableAttributes() as $attribute) {
            if ($this->_isAllowedAttribute($attribute, $attributes)) {
                $result[$attribute->getAttributeCode()] = $product->getData(
                                                                $attribute->getAttributeCode());
            }
        }

        return $result;
    }
	
	/*public function create($type, $set, $sku, $productData)
	{
		$productID = parent::create($type,$set,$sku,$productData);
		
		if($productID > 0)
		{
			$product = Mage::getModel('catalog/product');
	        // @var $product Mage_Catalog_Model_Product 
	        $product->setStoreId($this->_getStoreId($store))
	            ->setAttributeSetId($set)
	            ->setTypeId($type)
	            ->setSku($sku);
		}
	}*/
}

?>