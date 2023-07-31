<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/filter.php");

  require("../includes/MagicParser.php");

  $filename = (isset($_GET["filename"])?$_GET["filename"]:"");

  if (isset($_GET["limit"]))
  {
    $limit = $_GET["limit"];
  }
  else
  {
    $limit = 0;
  }

  if ($filename)
  {
    admin_import($filename,$limit);
  }
  else
  {
    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds`";

    if (database_querySelect($sql,$rows))
    {
      foreach($rows as $feed)
      {
        if (file_exists($config_feedDirectory.$feed["filename"]))
        {
          admin_import($feed["filename"],0);
        }
      }
    }
  }

  admin_importReviews();
  
  header("Location: ".$config_baseHREF."admin/");

  exit();
?>