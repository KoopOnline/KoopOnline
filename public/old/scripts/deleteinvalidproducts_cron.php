<?php

  set_time_limit(0);
  
  ini_set('max_execution_time', 0);

  header("Content-Type: text/plain;charset=utf-8");

  print "Working...\n";

  require_once("../includes/common.php");

  if (!$config_uidField) die("No \$config_uidField set in config.advanced.php");

  $link1 = mysqli_connect($config_databaseServer,$config_databaseUsername,$config_databasePassword,$config_databaseName);
  
  mysqli_set_charset($link1,"utf8");

  $sql1 = "DELETE FROM `pt_products_import` WHERE `price` = 0.00";

  $result1 = mysqli_query($link1,$sql1);

  print "Deleted 0.0 prices at ". date("H:i:s")."\n";
  	  //ob_flush(); flush();
   
  $sql1 = "DELETE FROM `pt_products_import` WHERE `EAN` NOT REGEXP '^[0-9]+$'";

  $result1 = mysqli_query($link1,$sql1);

  print "Deleted non numeric EAN codes at ". date("H:i:s")."\n"; 
   
   	  //ob_flush(); flush();
   
  //$sql1 = "OPTIMIZE TABLE `pt_products`";

  //$result1 = mysqli_query($link1,$sql1);
  
  print "Optimised table at ". date("H:i:s")."\n"; 
?>