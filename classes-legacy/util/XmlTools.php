<?php

class util_XmlTools
{

    public static function formatXml($xmlString, $cp1251 = false)
    {
        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xmlString);
        $dom->formatOutput = true;
        return $dom->saveXML($dom->documentElement); // removing <xml version="1.0">
    }

    public static function insertXml(SimpleXMLElement $to, SimpleXMLElement $source)
    {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($source);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }

}