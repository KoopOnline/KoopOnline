<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  $id = $_GET["id"];

  $then = (isset($_GET["then"])?base64_decode($_GET["then"]):"voucher_codes.php");

  $sql = "DELETE FROM `".$config_databaseTablePrefix."vouchers` WHERE id='".database_safe($id)."'";

  database_queryModify($sql,$insertId);

  header("Location: ".$then);

  exit();
?>