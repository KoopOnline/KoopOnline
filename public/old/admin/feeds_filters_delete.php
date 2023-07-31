<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  $filename = $_GET["filename"];

  $id = $_GET["id"];

  $sql = "DELETE FROM `".$config_databaseTablePrefix."filters` WHERE id='".database_safe($id)."'";

  database_queryModify($sql,$insertId);

  header("Location: feeds_filters.php?filename=".urlencode($filename));

  exit();
?>