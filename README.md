MagentoXtender
==============
Project page: https://github.com/bippo/magentoxtender

Copyright (C) 2011 Bippo Indonesia, [Linux4ever](http://www.magentocommerce.com/magento-connect/Dieter21/extension/557/magentoxtender).

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
