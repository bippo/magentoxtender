MagentoXtender
==============
Project page: https://github.com/bippo/magentoxtender

Copyright (C) 2011 Bippo Indonesia, [Linux4ever](http://www.linux4ever.be/dokuwiki/doku.php?id=magentoxtender).

Original extension: [Linux4ever_MagentoXtender](http://www.magentocommerce.com/magento-connect/Dieter21/extension/557/magentoxtender)

What is MagentoXtender?
-----------------------
Extra API functionality for Magento SOAP/REST web services API.

THIS MODULE IS IN EARLY DEVELOPMENT - USE AT YOUR OWN RISK!

This module makes extra API functions available for SOAP and XML-RPC webservice. It should be used in combination with the VB.NET Library I am currently developping. The VB.NET program also contains a wizard to import multiple products at once. 
Most important functionality is probably the possibility to link simple products to a configurable product.

Please note that this module will only install the extra API functions on your magento installation. You still need to download the VB.NET program at the following website:

http://www.linux4ever.be/dokuwiki/doku.php?id=magentoxtender

Important: If the new API functions are not available after you&apos;ve installed the module than you most certainly have to disable or refresh the Cache in the admin-pages:  System &gt; Cache Management

If you have problems with certain attributes not showing any options (like color or manufacturer) , then make sure you execute the following query : 

	UPDATE eav_attribute SET `source_model` = 'eav/entity_attribute_source_table' WHERE attribute_code IN ('manufacturer','color')

Current features:
 
* List/Create/Update/Delete Attribute Set
* List/Create/Update/Delete Attribute Group
* List/Create/Update/Delete (Product) Attributes + EAV Options/Values + assignToGroup, deleteFromGroup
* List/Create/Update/Delete Product (Simple/Configurable/Virtual/Grouped/Bundled) 
* List/Update Inventory Product Stock
* List Websites, StoreGroups &amp; Stores
* List/Create/Update/Delete Product Links for Grouped+Configurable products
* List/Create/Update/Delete/Assign Products/… for Product Categories
* List Product Categorie Attributes+Options
* List Product Types
* List/Create/Update/Delete (+ Upload) Product Images

Todo: 

* Create Wizard to easily import multiple products at once [Current Version works! High Priority]
* Implement API functions for: [Low Priority]
* Customers API
* Customer&apos;s Groups API
* Customer Address API
* Country API
* Region API
* Product Tier Price API
* Order API
* Shipment API
* Invoice API

Login
-----

	<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:Magento">
	   <soapenv:Header/>
	   <soapenv:Body>
	      <urn:login soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	         <username xsi:type="xsd:string">admin</username>
	         <apiKey xsi:type="xsd:string">password123</apiKey>
	      </urn:login>
	   </soapenv:Body>
	</soapenv:Envelope>

core_store.websites: List all websites
---------------------------------------

	<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:Magento">
	   <soapenv:Header/>
	   <soapenv:Body>
	      <urn:call soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	         <sessionId xsi:type="xsd:string">63dc358c3152376514cee9fb1e3627eb</sessionId>
	         <resourcePath xsi:type="xsd:string">core_store.websites</resourcePath>
	         <args xsi:type="xsd:anyType"></args>
	      </urn:call>
	   </soapenv:Body>
	</soapenv:Envelope>
