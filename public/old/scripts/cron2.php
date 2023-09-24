<?php
  set_time_limit(0);

  chdir('/var/www/laravelapp/KoopOnline/public/old/scripts/');

  require("../includes/common.php");

  print "Cron start at ". date("H:i:s")."\n";

  if (isset($_SERVER["REQUEST_METHOD"]))
  {
    $password = (isset($_GET["password"])?$_GET["password"]:"");

    if ($password != $config_adminPassword)
    {
      header('HTTP/1.0 401 Unauthorized');

      print "<h1>401 Unauthorized</h1>";

      exit();
    }

    header("Content-Type: text/plain");
  }

  require("../includes/admin.php");

  require("../includes/automation.php");

  require("../includes/filter.php");

  require("../includes/MagicParser.php");

  //$sql = "INSERT INTO `pt_products_import` (`id`, `merchant`, `filename`, `name`, `ean`, `description`, `image_url`, `buy_url`, `price`, `category`, `brand`, `rating`, `reviews`, `search_name`, `normalised_name`, `normalised_original_name`, `original_name`, `voucher_code`, `categoryid`, `dupe_hash`) VALUES (NULL, 'Amazon', 'Amazon_NL.xml', 'Animal Crossing: New Horizons', '0045496425456', '', 'https://images-na.ssl-images-amazon.com/images/I/812qolL6Y7L._AC_SL1500_.jpg', 'https://amzn.to/2StqMmE', '49.82', 'Games', 'Nintendo', '0', '0', 'Animal Crossing New Horizons ', 'Animal Crossing New Horizons ', 'Animal Crossing New Horizons ', 'Animal Crossing: New Horizons ', '', '0', '')";

  //database_queryModify($sql,$result);


  $sql = "ALTER TABLE `".$config_databaseTablePrefix."products_import` ENABLE KEYS";

  database_queryModify($sql,$result);

  include("/var/www/laravelapp/KoopOnline/public/old/scripts/deleteinvalidproducts_cron.php");
  include("/var/www/laravelapp/KoopOnline/public/old/scripts/uidmap_cron.php");

  /*$sql = "DELETE FROM `".$config_databaseTablePrefix."products_import` WHERE `filename` LIKE 'Coolblue_NL.xml'"; //Coolblue verwijderen

  database_queryModify($sql,$result); //Coolblue verwijderen uitvoeren */

  $sql = "OPTIMIZE TABLE `".$config_databaseTablePrefix."products_import`";

  database_queryModify($sql,$result);

  $sql = "DROP TABLE `".$config_databaseTablePrefix."products`";

  database_queryModify($sql,$result);

  $sql = "RENAME TABLE `".$config_databaseTablePrefix."products_import` TO `".$config_databaseTablePrefix."products`";

  database_queryModify($sql,$result);


  print chr(13)."backfilling reviews...                ";

  admin_importReviews();

  print chr(13)."backfilling reviews...[done]\n";


  $sql = "UPDATE `".$config_databaseTablePrefix."feeds` SET clicks=0";
  database_queryModify($sql,$result);

  exit();
?>
