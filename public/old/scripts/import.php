<?php
  set_time_limit(0);
  
  ini_set('max_execution_time', 0);
  
  ob_start();

  require("../includes/common.php");

  require("../includes/admin.php");

  require("../includes/filter.php");

  require("../includes/MagicParser.php");


  if ($_GET['password'] != '6205023ta')
  {
    print "Usage: import.php <filename>|@ALL\n"; exit;
  }

  $filename = $_GET['filename'];

  if (isset($argv[2]))
  {
    $limit = intval($argv[2]);
  }
  else
  {
    $limit = 0;
  }

  function callback($progress)
  {
    global $feed;

    print chr(13)."importing ".$feed["filename"]."...[".$progress."/".$feed["products"]."]";
	ob_flush(); flush();
	}

  function import()
  {
    global $feed;

    global $limit;

    print chr(13)."importing ".$feed["filename"]."...[0/".$feed["products"]."]";

    admin_import($feed["filename"],$limit,"callback");

    print chr(13)."importing ".$feed["filename"]."...[done]            \n";
	
	ob_flush(); flush();
	
  }

  if ($filename == "@ALL")
  {
    $admin_importAll = TRUE;

    $sql = "DROP TABLE IF EXISTS `".$config_databaseTablePrefix."products_import`";

    database_queryModify($sql,$result);

    $sql = "CREATE TABLE `".$config_databaseTablePrefix."products_import` LIKE `".$config_databaseTablePrefix."products`";

    database_queryModify($sql,$result);

    $sql = "ALTER TABLE `".$config_databaseTablePrefix."products_import` DISABLE KEYS";

    database_queryModify($sql,$result);

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds`";

    if (database_querySelect($sql,$rows))
    {
      foreach($rows as $feed)
      {
        if (file_exists($config_feedDirectory.$feed["filename"]))
        {
          import();
        }
      }
    }
    $sql = "ALTER TABLE `".$config_databaseTablePrefix."products_import` ENABLE KEYS";

    database_queryModify($sql,$result);

    $sql = "DROP TABLE `".$config_databaseTablePrefix."products`";

    database_queryModify($sql,$result);

    $sql = "RENAME TABLE `".$config_databaseTablePrefix."products_import` TO `".$config_databaseTablePrefix."products`";

    database_queryModify($sql,$result);
  }
  elseif($filename == "@MODIFIED")
  {
    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds`";

    if (database_querySelect($sql,$rows))
    {
      foreach($rows as $feed)
      {
        if (file_exists($config_feedDirectory.$feed["filename"]) && ($feed["imported"] < filemtime($config_feedDirectory.$feed["filename"])))
        {
          import();
        }
      }
    }
  }
  else
  {
    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE filename='".database_safe($filename)."'";
	
	  set_time_limit(0);

    if (database_querySelect($sql,$rows))
    {
      $feed = $rows[0];

      import();
    }
  }

  print chr(13)."backfilling reviews...                ";
  
  ob_flush(); flush();

  admin_importReviews();

  print chr(13)."backfilling reviews...[done]\n";
  
  ob_flush(); flush();
  
    include("/home/kooponline/domains/kooponline.com/public_html/scripts/uidmap.php");
    include("/home/kooponline/domains/kooponline.com/public_html/scripts/deleteinvalidproducts.php");
	
    $sql = "UPDATE `".$config_databaseTablePrefix."feeds` SET clicks=0";
  database_queryModify($sql,$result);

  exit();
?>