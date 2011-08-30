MagentoXtender
==============
Project page: https://github.com/bippo/magentoxtender

Copyright (C) 2011 [Bippo Indonesia](http://www.bippo.co.id/), [Linux4ever](http://www.linux4ever.be/dokuwiki/doku.php?id=magentoxtender).

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
 
1. List/Create/Update/Delete Attribute Set
2. List/Create/Update/Delete Attribute Group
3. List/Create/Update/Delete (Product) Attributes + EAV Options/Values + assignToGroup, deleteFromGroup
4. List/Create/Update/Delete Product (Simple/Configurable/Virtual/Grouped/Bundled) 
5. List/Update Inventory Product Stock
6. List Websites, StoreGroups &amp; Stores
7. List/Create/Update/Delete Product Links for Grouped+Configurable products
8. List/Create/Update/Delete/Assign Products/... for Product Categories
9. List Product Categorie Attributes+Options
10. List Product Types
11. List/Create/Update/Delete (+ Upload) Product Images
12. Get/Set Configuration Data
13. Reindex

TODO: 

* Create Wizard to easily import multiple products at once [Current Version works! High Priority]
* Implement API functions for: [Low Priority]
* Customers API
* Customer's Groups API
* Customer Address API
* Country API
* Region API
* Product Tier Price API
* Order API
* Shipment API
* Invoice API

Change Log
----------

2.0.0

* Renamed the module to avoid confusion with Linux4ever module
* Reversioned to 2.0.0
* Added detailed README illustrating SOAPv1 calls
* New API for Config get & save
* Ant script to symlink, deploy, remote flush, remote compile

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

core_config.get: Get configuration data
---------------------------------------

Request:

	<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:Magento"
		xmlns:ns2="http://schemas.xmlsoap.org/soap/encoding/">
	   <soapenv:Header/>
	   <soapenv:Body>
	      <urn:call soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	         <sessionId xsi:type="xsd:string">99e7b49bb137e0060654455bb43220fb</sessionId>
	         <resourcePath xsi:type="xsd:string">core_config.get</resourcePath>
	         <args xsi:type="ns2:Array" ns2:arrayType="xsd:string[1]">
			<item xsi:type="xsd:string">web/unsecure/base_url</item>
		</args>
	      </urn:call>
	   </soapenv:Body>
	</soapenv:Envelope>

Response:
	
	<SOAP-ENV:Envelope SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="urn:Magento" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/">
	   <SOAP-ENV:Body>
	      <ns1:callResponse>
	         <callReturn xsi:type="xsd:string">http://ceefour.annafi/rumahmufida.com/</callReturn>
	      </ns1:callResponse>
	   </SOAP-ENV:Body>
	</SOAP-ENV:Envelope>

Request:

	<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:Magento"
		xmlns:ns2="http://schemas.xmlsoap.org/soap/encoding/">
	   <soapenv:Header/>
	   <soapenv:Body>
	      <urn:call soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	         <sessionId xsi:type="xsd:string">99e7b49bb137e0060654455bb43220fb</sessionId>
	         <resourcePath xsi:type="xsd:string">core_config.get</resourcePath>
	         <args xsi:type="ns2:Array" ns2:arrayType="xsd:string[1]">
				<item xsi:type="xsd:string">web/unsecure</item>
			 </args>
	      </urn:call>
	   </soapenv:Body>
	</soapenv:Envelope>

Response:

	<SOAP-ENV:Envelope SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="urn:Magento" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:ns2="http://xml.apache.org/xml-soap" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/">
	   <SOAP-ENV:Body>
	      <ns1:callResponse>
	         <callReturn xsi:type="ns2:Map">
	            <item>
	               <key xsi:type="xsd:string">base_url</key>
	               <value xsi:type="xsd:string">http://ceefour.annafi/rumahmufida.com/</value>
	            </item>
	            <item>
	               <key xsi:type="xsd:string">base_web_url</key>
	               <value xsi:type="xsd:string">http://ceefour.annafi/rumahmufida.com/</value>
	            </item>
				...
	         </callReturn>
	      </ns1:callResponse>
	   </SOAP-ENV:Body>
	</SOAP-ENV:Envelope>

core_config.save : Set configuration data
-----------------------------------------

Request:

	<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:urn="urn:Magento"
		xmlns:ns2="http://schemas.xmlsoap.org/soap/encoding/">
	   <soapenv:Header/>
	   <soapenv:Body>
	      <urn:call soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
	         <sessionId xsi:type="xsd:string">99e7b49bb137e0060654455bb43220fb</sessionId>
	         <resourcePath xsi:type="xsd:string">core_config.save</resourcePath>
	         <args xsi:type="ns2:Array" ns2:arrayType="xsd:string[2]">
				<item xsi:type="xsd:string">design/head/title_suffix</item>
				<item xsi:type="xsd:string">| Wow Keren</item>
			 </args>
	      </urn:call>
	   </soapenv:Body>
	</soapenv:Envelope>

Response: (pretty much nothing)

	<SOAP-ENV:Envelope SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="urn:Magento" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/">
	   <SOAP-ENV:Body>
	      <ns1:callResponse>
	         <callReturn xsi:nil="true"/>
	      </ns1:callResponse>
	   </SOAP-ENV:Body>
	</SOAP-ENV:Envelope>
