<?php

  include("../../old/includes/common.php");
  
  //header("Content-Type: text/plain");
  
  error_reporting('E_ALL');
  
  $ean = $_GET['ean'];
  
		if (!is_numeric($ean))
			{
				echo "{\"error\":\"Geen geldige barcode...\"}";
				exit();
			}
		else if (strlen($ean) == 11) //Als het geen EAN-13 barcode is nullen toevoegen
			{
				$ean = "00".$ean;
			}
		else if (strlen($ean) == 12) //Als het geen EAN-13 barcode is nullen toevoegen
			{
				$ean = "0".$ean;
			}
		else if (strlen($ean) != 13) //Als het nu nog geen EAN-13 barcode is kappen
			{
				echo "{\"error\":\"Geen geldige barcode...\"}";
				exit();
			}
  
  //echo $ean;
  
          $sql = "SELECT * FROM `".$config_databaseTablePrefix."products` WHERE ean = '".database_safe($ean)."' LIMIT 1";
        if (database_querySelect($sql,$rows))
        {
          echo "{\"url\":\"https://www.kooponline.com".tapestry_productHREF($rows[0])."\"}";
          exit();
        }
        else
        {
			echo "{\"url\":\"https://partner.bol.com/click/click?p=2&t=url&s=50541&f=TXL&url=https%3A%2F%2Fwww.bol.com%2Fnl%2Fs%2F". urlencode($ean)."%2F\"}";
          //echo "{\"error\":\"Product niet gevonden...\"}";
		  exit();
        }
  
  ?>
