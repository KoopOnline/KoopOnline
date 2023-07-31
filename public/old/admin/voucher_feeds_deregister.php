<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  $filename = $_GET["filename"];

  $sql = "DELETE FROM `".$config_databaseTablePrefix."voucherfeeds` WHERE filename='".database_safe($filename)."'";

  database_queryModify($sql,$result);

  header("Location: voucher_codes.php");

  exit();
?>