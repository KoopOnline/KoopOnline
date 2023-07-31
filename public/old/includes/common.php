<?php
  if (file_exists("config.php"))
  {
    require("config.php");

    require("config.advanced.php");

    $common_path = "includes/";
  }
  else if (file_exists("../config.php"))
  {
    require("../config.php");

    require("../config.advanced.php");

    $common_path = "../includes/";
  }
  else
  {
    require("../../config.php");

    require("../../config.advanced.php");

    $common_path = "../../includes/";
  }

  require($common_path."javascript.php");

  require($common_path."tapestry.php");

  require($common_path."translate.php");

  require($common_path."database.php");

  date_default_timezone_set($config_timezone);
?>