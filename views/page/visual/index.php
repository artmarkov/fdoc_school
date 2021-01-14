<?php

use yii\helpers\Url;

/* @var $data array */
/* @var $found string */
?>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.6.0/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.6.0/dist/leaflet.css"/>
<script src="https://leaflet.github.io/Leaflet.markercluster/dist/leaflet.markercluster-src.js"></script>
<link rel="stylesheet" href="https://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.css"/>
<link rel="stylesheet" href="https://leaflet.github.io/Leaflet.markercluster/dist/MarkerCluster.Default.css"/>

<style>
    input:checked + label {
        color: green;
        border-radius: 5px;
        background-color: white;
        border: 1px solid grey;
    }

    label {
        padding: 10px 15px;
    }

    .datepicker-dropdown {
        z-index: 9999 !important;
    }

    /* on selector per rule as explained here : http://www.sitepoint.com/html5-full-screen-api/ */
    #map:-webkit-full-screen {
        width: 100% !important;
        height: 100% !important;
        z-index: 99999;
    }

    #map:-moz-full-screen {
        width: 100% !important;
        height: 100% !important;
        z-index: 99999;
    }
</style>
<script type="text/javascript">
   $("#checkall").click(function () {
      if ($('#checkall').is(':checked')) {
         for (var i = 0; i < 12; i++) {
            $('#check' + i).prop('checked', true);
         }
      } else {
         for (var i = 0; i < 12; i++) {
            $('#check' + i).prop('checked', false);
         }
      }
   });

   $('input[type="checkbox"]').on('change', function () {
      $('input[name="' + this.name + '"]').not(this).prop('checked', false);
   });
   $("#nav li a").click(function () {
      $("#nav li").removeClass('selected');
      $(this).parent().addClass('selected');
   });
   $("#check_act").click(function () {
      if ($('#check_forb').is(':checked')) {
         $('#check_forb').prop('checked', false);
      }
   });
   $("#check_forb").click(function () {
      if ($('#check_act').is(':checked')) {
         $('#check_act').prop('checked', false);
      }
   });
</script>

<div id="map" style="height:85vh;"></div>

<script type='text/javascript'>
   var base = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 18,
      minZoom: 3,
   });

   var map = new L.Map('map', {
      layers: [base],
      center: new L.LatLng(54.270747, 69.429503),
      zoom: 5,
   });

   var LeafIcon = L.Icon.extend({
      options: {
         iconSize: [27, 35],
      }
   });

   //Слой маркеров для юр адресов клиентов
   var markers_contr = new L.MarkerClusterGroup({
      iconCreateFunction: function (cluster) {
         return L.icon({
            iconUrl: '<?= Url::to('@web/img/marker.png') ?>',
            iconSize: [40, 51], // size of the icon
         });
      }
   });
   var Icon_contr = new LeafIcon({iconUrl: '<?= Url::to('@web/img/marker.png') ?>'});
   <?php foreach ($data as $vald) {
   if ((isset($vald['lat'])) and ($vald['lat'] != "")) {  if (isset($vald['link']) and $vald['link'] != '') { ?>
   var title = '<a href="<?=  Url::to(['client/edit', 'id' => $vald['link']])  ?>" target="_blank"><?= $vald['name']; ?></a>'; <?php } else { ?>
   var title = '<?= $vald['name']; ?>'; <?php } ?>
   var marker = new L.Marker(new L.LatLng(<?= $vald['lat'];?>, <?= $vald['lon']; ?>), {icon: Icon_contr, title: title});
   marker.bindPopup(title);
   markers_contr.addLayer(marker);
   <?php }
   } ?>
   map.addLayer(markers_contr);
   ////////////////////////////////////
</script>

(Найдено <?= $found ?>)