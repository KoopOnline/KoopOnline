<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  require("../includes/filter.php");

  $filename = $_GET["filename"];

  $id = $_GET["id"];

  if ($filename)
  {
    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE filename='".database_safe($filename)."'";

    database_querySelect($sql,$rows);

    $feed = $rows[0];

    $fields = array();

    foreach($config_fieldSet as $field => $caption)
    {
      $fields[$field] = $caption . " (".$feed["field_".$field].")";
    }
  }
  else
  {
    foreach($config_fieldSet as $field => $caption)
    {
      $fields[$field] = $caption;
    }
  }

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."filters` WHERE id='".database_safe($id)."'";

  database_querySelect($sql,$rows);

  $filter = $rows[0];

  $filter["data"] = unserialize($filter["data"]);

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  if ($submit == "Cancel")
  {
    header("Location: feeds_filters.php?filename=".urlencode($filename));

    exit();
  }

  if ($submit == "Save")
  {
    unset($_POST["submit"]);

    foreach($_POST as $k => $v)
    {
      $filter["data"][$k] = $v;
    }

    $validateFunction = "filter_".$filter["name"]."Validate";

    $validateFunction($filter["data"]);

    if (!widget_errorCount())
    {
      $sql = "UPDATE `".$config_databaseTablePrefix."filters` SET data = '".database_safe(serialize($filter["data"]))."' WHERE id='".database_safe($id)."'";

      database_queryModify($sql,$insertId);

      header("Location: feeds_filters.php?filename=".urlencode($filename));

      exit();
    }
  }

  require("admin_header.php");

  print "<h2>".translate("Admin")."</h2>";

  print "<h3>".translate("Configure Filter")." (".($filename?$filename:"Global").")</h3>";

  print "<table>";

  print "<tr>";

  print "<td>&nbsp;</td>";

  print "<th>Field</th>";

  print "</tr>";

  print "<tr>";

  print "<th class='pta_key'>".$filter_names[$filter["name"]]."</th>";

  print "<td>".$fields[$filter["field"]]."</td>";

  print "</tr>";

  print "</table>";

  widget_formBegin();

  $configureFunction = "filter_".$filter["name"]."Configure";

  $configureFunction($filter["data"]);

  widget_formButtons(array("Save"=>TRUE),"feeds_filters.php".($filename?"?filename=".urlencode($filename):""));

  widget_formEnd();

  require("admin_footer.php");
?>