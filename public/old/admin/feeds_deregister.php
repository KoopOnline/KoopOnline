<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  $filename = $_GET["filename"];

  admin_deregister($filename);

  header("Location: ".$config_baseHREF."admin/");

  exit();
?>