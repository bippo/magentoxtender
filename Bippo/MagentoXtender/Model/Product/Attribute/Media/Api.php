<?php

class Bippo_MagentoXtender_Model_Product_Attribute_Media_Api extends Mage_Catalog_Model_Product_Attribute_Media_Api
{
	/**
     * MODIFIED so you can provide a custom filename instead of the default image.jpg...
     *
     * @param int|string $productId
     * @param array $data
     * @param string|int $store
     * @return string
     */
    public function create($productId, $data, $store = null)
    {
        $product = $this->_initProduct($productId, $store);

        $gallery = $this->_getGalleryAttribute($product);

        if (!isset($data['file']) || !isset($data['file']['mime']) || !isset($data['file']['content'])) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('Image not specified.'));
        }

        if (!isset($this->_mimeTypes[$data['file']['mime']])) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid image type.'));
        }

        $fileContent = @base64_decode($data['file']['content'], true);
        if (!$fileContent) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('Image content is not valid base64 data.'));
        }

        unset($data['file']['content']);

        $tmpDirectory = Mage::getBaseDir('var') . DS . 'api' . DS . $this->_getSession()->getSessionId();

        $filename = preg_replace('/[^0-9a-z\.\_\-]/i','',$product->getSku());

        if(isset($data["file"]["name"]) && !empty($data["file"]["name"]))
        {
        	$filename = preg_replace('/[^0-9a-z\.\_\-]/i','',$data["file"]["name"]);
        }
        
        if(empty($filename)) {$filename="image";}
        
        $fileName  = $filename. '.' . $this->_mimeTypes[$data['file']['mime']];
        $ioAdapter = new Varien_Io_File();
        try {
            // Create temporary directory for api
            $ioAdapter->checkAndCreateFolder($tmpDirectory);
            $ioAdapter->open(array('path'=>$tmpDirectory));
            // Write image file
            $ioAdapter->write($fileName, $fileContent, 0666);
            unset($fileContent);

            // Adding image to gallery
            $file = $gallery->getBackend()->addImage(
                $product,
                $tmpDirectory . DS . $fileName,
                null,
                true
            );

            // Remove temporary directory
            $ioAdapter->rmdir($tmpDirectory, true);

            $gallery->getBackend()->updateImage($product, $file, $data);

            if (isset($data['types'])) {
                $gallery->getBackend()->setMediaAttribute($product, $data['types'], $file);
            }

            $product->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('not_created', $e->getMessage());
        } catch (Exception $e) {
            $this->_fault('not_created', Mage::helper('catalog')->__('Can\'t create image.'));
        }

        return $gallery->getBackend()->getRenamedImage($file);
    }
}

?>