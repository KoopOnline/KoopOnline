<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  $id = $_GET["id"];

  $sql = "DELETE FROM `".$config_databaseTablePrefix."jobs` WHERE id='".database_safe($id)."'";

  database_queryModify($sql,$insertId);

  header("Location: automation_tool.php");

  exit();
?>