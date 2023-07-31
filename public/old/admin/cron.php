<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("admin_header.php");

  print "<h2>CRON</h2>";

  $installPath = "";

  if (isset($_SERVER["SCRIPT_FILENAME"]))
  {
    $installPath = $_SERVER["SCRIPT_FILENAME"];

    $installPath = str_replace("admin/cron.php","",$installPath);
  }

  $phpPath = "";

  foreach($config_CRONPrograms["php"] as $v)
  {
    if (file_exists($v))
    {
      $phpPath = $v;

      break;
    }
  }

  admin_tableBegin();

  if ($installPath && $phpPath)
  {
    admin_tableRow(translate("Option 1"),"cd ".$installPath."scripts;".$phpPath." cron.php","pta_pre");
  }
  else
  {
    admin_tableRow(translate("Option 1"),translate("Not Available"));
  }

  $wgetPath = "";

  foreach($config_CRONPrograms["wget"] as $v)
  {
    if (file_exists($v))
    {
      $wgetPath = $v;

      break;
    }
  }

  if ($wgetPath)
  {
    if (isset($_SERVER["HTTP_HOST"]))
    {
      $httpHost = $_SERVER["HTTP_HOST"];

      $httpHost = str_replace("admin/cron.php","",$httpHost);
    }
    else
    {
      $httpHost = "www.example.com";
    }

    if (isset($_SERVER["SCRIPT_NAME"]))
    {
      $httpPath = $_SERVER["SCRIPT_NAME"];

      $httpPath = str_replace("admin/cron.php","",$httpPath);
    }
    else
    {
      $httpPath = "/";
    }

    $cmd = $wgetPath." http://".$httpHost.$httpPath."scripts/cron.php";

    if ($config_adminPassword)
    {
      $cmd .= "?password=PASSWORD";
    }

    admin_tableRow(translate("Option 2"),$cmd,"pta_pre");
  }

  admin_tableEnd();

  require("admin_footer.php");
?>