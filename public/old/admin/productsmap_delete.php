<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  $id = $_GET["id"];

  $sql = "DELETE FROM `".$config_databaseTablePrefix."productsmap` WHERE id='".database_safe($id)."'";

  database_queryModify($sql,$insertId);

  header("Location: productsmap.php?enableDelete=1");

  exit();
?>