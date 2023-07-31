<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../revision.php");

  require("admin_header.php");

  print "<h2>".translate("Support Info")."</h2>";

  print "<h3>".translate("Software")."</h3>";

  admin_tableBegin();

  admin_tableRow("Revision",$revision);

  admin_tableRow("PHP Version",phpversion());

  $link = mysqli_connect($config_databaseServer, $config_databaseUsername, $config_databasePassword);

  admin_tableRow("MySQL Version",mysqli_get_server_info($link));

  if (isset($_SERVER["HTTP_HOST"]))
  {
    $baseHREF = "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];

    $baseHREF = str_replace("admin/support.php","",$baseHREF);
  }
  else
  {
    $baseHREF = "[NOT AVAILABLE]";
  }

  admin_tableRow("Base HREF",$baseHREF,"pta_pre");

  if (isset($_SERVER["SCRIPT_FILENAME"]))
  {
    $installPath = $_SERVER["SCRIPT_FILENAME"];

    $installPath = str_replace("admin/support.php","",$installPath);
    
    $installPath = str_replace("admin\support.php","",$installPath);    
  }
  else
  {
    $installPath = "[NOT AVAILABLE]";
  }

  admin_tableRow("Install Path",$installPath,"pta_pre");

  admin_tableEnd();

  require("admin_footer.php");
?>