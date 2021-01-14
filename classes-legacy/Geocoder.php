<?php

class Geocoder {
   const sputnikUrl='http://search.maps.sputnik.ru/search?q=%s';
   const yandexUrl='http://geocode-maps.yandex.ru/1.x/?geocode=%s&results=1';

   public static function yandex($search) {
      try {
         $resp=self::request(sprintf(self::yandexUrl,urlencode($search)));
         //header('Content-type: text/xml; charset=utd8');echo ($resp);exit;

         $xml = new SimpleXMLElement($resp);
         $xml->registerXPathNamespace('ns', 'http://maps.yandex.ru/ymaps/1.x');
         $xml->registerXPathNamespace('gml', 'http://www.opengis.net/gml');
         $xml->registerXPathNamespace('gc', 'http://maps.yandex.ru/geocoder/1.x');

         $found = self::getXmlValue($xml,'/ns:ymaps/ns:GeoObjectCollection/gml:metaDataProperty/gc:GeocoderResponseMetaData/gc:found');
         if ('0'==$found) {
            return null;
         }

         $point = self::getXmlValue($xml,'/ns:ymaps/ns:GeoObjectCollection/gml:featureMember/ns:GeoObject/gml:Point/gml:pos');
         list($lon,$lat)=$point ? explode(' ',$point) : array('','');

         $result=new stdClass();
         $result->type=self::getXmlValue($xml,'/ns:ymaps/ns:GeoObjectCollection/gml:featureMember/ns:GeoObject/gml:metaDataProperty/gc:GeocoderMetaData/gc:kind');
         $result->display_name=self::getXmlValue($xml,'/ns:ymaps/ns:GeoObjectCollection/gml:featureMember/ns:GeoObject/gml:metaDataProperty/gc:GeocoderMetaData/gc:text');
         $result->full_match=self::getXmlValue($xml,'/ns:ymaps/ns:GeoObjectCollection/gml:featureMember/ns:GeoObject/gml:metaDataProperty/gc:GeocoderMetaData/gc:precision') == 'exact' ? '1' : '0';
         $result->title=self::getXmlValue($xml,'/ns:ymaps/ns:GeoObjectCollection/gml:featureMember/ns:GeoObject/gml:name');
         $result->description=self::getXmlValue($xml,'/ns:ymaps/ns:GeoObjectCollection/gml:featureMember/ns:GeoObject/gml:description');
         $result->position=new stdClass();
         $result->position->lat=$lat;
         $result->position->lon=$lon;

         return $result;
      }
      catch (Exception $ex) {
         return null;
      }
   }

   protected static function getXmlValue($xml,$xpath) {
      $res=$xml->xpath($xpath);
      return isset($res[0]) ? (string)$res[0] : null;
   }

   public static function lookup($search) {
      try {
         $resp=self::request(sprintf(self::sputnikUrl,urlencode($search)));
         $data= json_decode($resp);
         return isset($data->result[0]) ? $data->result[0] : null;
      }
      catch (Exception $ex) {
         return null;
      }
   }

   private static function request($url) {
      $proxy=\Yii::$app->params['proxyUrl'] ?? null;
      if ($proxy) {
         $cxContext = stream_context_create(array(
            'http' => array(
               'proxy' => 'tcp://'.$proxy,
               'request_fulluri' => true,
            )
         ));
         $resp = file_get_contents($url, False, $cxContext);
      }
      else {
         $resp = file_get_contents($url);
      }
      return $resp;
   }

}
