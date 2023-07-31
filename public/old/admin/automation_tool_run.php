<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/automation.php");

  $id = $_GET["id"];

  automation_run($id);

  header("Location: automation_tool.php");

  exit();
?>