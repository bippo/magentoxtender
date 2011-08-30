<?php
/**
 * @author hendy@bippo.co.id 
 * @copyright 2011 Bippo Indonesia. All Rights Reserved.
 */

class Bippo_MagentoXtender_Model_Indexer_Api extends Mage_Catalog_Model_Api_Resource
{
	/**
	 * Reindex all indexes.
	 * @return array Names of rebuilt indexes.
	 */
	public function reindexAll()
	{
		$indexer = Mage::getSingleton('index/indexer');
		// get index processes
		$processes = array();
		$collection = $indexer->getProcessesCollection();
		foreach ($collection as $process) {
			$processes[] = $process;
		}
		// perform reindex
		$rebuilt = array();
        foreach ($processes as $process) {
            /* @var $process Mage_Index_Model_Process */
         	try {
                $process->reindexEverything();
                $rebuilt[] = $process->getIndexer()->getName();
                Mage::log( $process->getIndexer()->getName() . " index was rebuilt successfully");
            } catch (Mage_Core_Exception $e) {
                Mage::log( $e->getMessage() );
                $this->_fault('general_error');
            } catch (Exception $e) {
                Mage::log( $process->getIndexer()->getName() . " index process unknown error:");
                Mage::log( $e );
                $this->_fault('general_error');
            }
        }
        return $rebuilt;
	}
	
}