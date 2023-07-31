<?php
  header("Content-Type: text/plain;charset=utf-8");

  print "Working...\n";

	set_time_limit(0);

  require_once("../includes/common.php");

  if (!$config_uidField) die("No \$config_uidField set in config.advanced.php");

  $link1 = mysqli_connect($config_databaseServer,$config_databaseUsername,$config_databasePassword,$config_databaseName);
  
  mysqli_set_charset($link1,"utf8");

  $link2 = mysqli_connect($config_databaseServer,$config_databaseUsername,$config_databasePassword,$config_databaseName);

  mysqli_set_charset($link2,"utf8");

  $sql1 = "TRUNCATE `".$config_databaseTablePrefix."uidfix`";

  $result1 = mysqli_query($link1,$sql1);

  $sql1 = "SELECT DISTINCT(".$config_uidField.") FROM `".$config_databaseTablePrefix."products`";

  print "Phase 0: Start at ". date("H:i:s")."\n";
  
  mysqli_real_query($link1,$sql1);

  if ($result1 = mysqli_use_result($link1))
  {
    print "Phase 0: initial query complete\n";
	print "Phase 1: Start at ". date("H:i:s")."\n";
	
	//ob_flush(); flush();
	
    while($product = mysqli_fetch_assoc($result1))
    {
      if (trim($product[$config_uidField])=="") continue;

      if (!$product[$config_uidField]) continue;

	  //Custom brand aanpassing hieronder
      $sql2 = "SELECT SQL_CALC_FOUND_ROWS name,CASE merchant
          WHEN 'CoolBlue' THEN '1'
          WHEN 'MediaMarkt' THEN '2'
          WHEN 'BCC' THEN '3'
		  WHEN 'Vivolanda' THEN '4'
		  WHEN 'Bart Smit' THEN '5'
		  WHEN 'Intertoys' THEN '6'
		  WHEN 'Wehkamp' THEN '7'
		  WHEN 'Megekko' THEN '7'
		  WHEN 'Mobiel.nl' THEN '9'
        ELSE '999' END AS priority, brand FROM `".$config_databaseTablePrefix."products` WHERE ".$config_uidField."='".database_safe($product[$config_uidField])."' ORDER BY priority LIMIT 1";

      $result2 = mysqli_query($link2,$sql2);

      $product2 = mysqli_fetch_array($result2,MYSQLI_ASSOC);

      $sql2 = "SELECT FOUND_ROWS() as resultcount";

      $result2 = mysqli_query($link2,$sql2);

      $row2 = mysqli_fetch_array($result2,MYSQLI_ASSOC);

      if ($row2["resultcount"] > 1)
      {
        $product["name"] = $product2["name"];
        $product["brand"] = $product2["brand"];
		
        //print "Phase 1:".$product2["name"]." (".$row2["resultcount"].")\n";flush();

        $sql2 = "INSERT INTO `".$config_databaseTablePrefix."uidfix` SET uid='".database_safe($product[$config_uidField])."',name='".database_safe($product["name"])."',brand='".database_safe($product["brand"])."'"; //Custom brand aanpassing

        mysqli_query($link2,$sql2);
      }
    }
  }

  $sql1 = "SELECT * FROM `".$config_databaseTablePrefix."uidfix`";

  mysqli_real_query($link1,$sql1);

    print "Phase 1: initial query complete\n";
	print "Phase 2: Start at ". date("H:i:s")."\n";
	
	//ob_flush(); flush();
  
  if ($result1 = mysqli_use_result($link1))
  {
    while($product = mysqli_fetch_assoc($result1))
    {
      //print "Phase 2:".$product["name"]."\n";flush();

      $normalisedName = tapestry_normalise($product["name"]);
      $normalisedBrand = tapestry_normalise($product["brand"]);  //Custom brand aanpassing

      $searchName = tapestry_search($normalisedName);
	  
		if ($normalisedBrand != '') { //Als Brand niet leeg is, neem dan de brand name over
		
			$sql2 = "UPDATE `".$config_databaseTablePrefix."products` SET name='".database_safe($product["name"])."',normalised_name='".database_safe($normalisedName)."',search_name='".database_safe($searchName)."',brand='".database_safe($normalisedBrand)."' WHERE ".$config_uidField."='".database_safe($product["uid"])."'";
			
	} else {
		
		  $sql2 = "UPDATE `".$config_databaseTablePrefix."products` SET name='".database_safe($product["name"])."',normalised_name='".database_safe($normalisedName)."',search_name='".database_safe($searchName)."' WHERE ".$config_uidField."='".database_safe($product["uid"])."'";
		  
		}

	  
      mysqli_query($link2,$sql2);
    }
  }

  $sql1 = "TRUNCATE `".$config_databaseTablePrefix."uidfix`";

  $result1 = mysqli_query($link1,$sql1);
  
  print "Phase 2: Done at ". date("H:i:s")."\n";
  
  //ob_flush(); flush();
?>