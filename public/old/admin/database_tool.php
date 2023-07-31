<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  require("../includes/MagicParser.php");

  function database_toolPack($text)
  {
    return bin2hex($text);
  }

  function database_toolUnpack($hex)
  {
    return pack("H*",trim($hex));
  }

  function database_toolDumpRecord($table,$record)
  {
    $xml = "";

    $xml .= "<".strtoupper($table)."_R>\n";

    foreach($record as $field => $value)
    {
      $xml .= "<".strtoupper($field).">".database_toolPack($value)."</".strtoupper($field).">\n";
    }

    $xml .= "</".strtoupper($table)."_R>\n";

    return $xml;
  }

  function database_toolRestoreRecord($table,$record)
  {
    global $config_databaseTablePrefix;

    $sql = "INSERT INTO `".$config_databaseTablePrefix.$table."` SET ";

    foreach($record as $field => $value)
    {
      if ($field == strtoupper($table)."_R") continue;

      if (isset($record[strtoupper($field)]))
      {
        $sql .= "`".$field."` = '".database_safe(database_toolUnpack($record[$field]))."',";
      }
    }

    $sql = trim($sql,",");

    database_queryModify($sql,$result);
  }

  function database_toolDumpTable($table)
  {
    global $config_databaseTablePrefix;

    $sql = "SELECT * FROM `".$config_databaseTablePrefix.$table."`";

    $xml = "";

    if (database_querySelect($sql,$records))
    {
      $xml = "<".strtoupper($table).">\n";

      foreach($records as $record)
      {
        $xml .= database_toolDumpRecord($table,$record);
      }

      $xml .= "</".strtoupper($table).">\n";
    }

    return $xml;
  }

  $tableNames = array();

  $tableNames["brands"] = translate("Brand Mapping");

  $tableNames["categories"] = translate("Category Mapping");

  $tableNames["categories_hierarchy"] = translate("Category Hierarchy Mapping");

  $tableNames["featured"] = translate("Featured Products");

  $tableNames["feeds"] = translate("Feed Registration");

  $tableNames["filters"] = translate("Filters");

  $tableNames["jobs"] = translate("Automation Tool");

  $tableNames["productsmap"] = translate("Product Mapping");

  $tableNames["productsmap_regexp"] = translate("Product Mapping RegExp");

  $tableNames["reviews"] = translate("Reviews");

  $tableNames["voucherfeeds"] = translate("Voucher Code Feed Management");

  $tableNames["vouchers"] = translate("Voucher Codes");

  $tables = array();

  $sql = "SHOW TABLES LIKE '".$config_databaseTablePrefix."%'";

  database_querySelect($sql,$rows);

  foreach($rows as $row)
  {
    $table = array_pop($row);

    if ($table == $config_databaseTablePrefix."products") continue;

    $table = substr($table,strlen($config_databaseTablePrefix));

    if (!isset($tableNames[$table])) continue;

    $tables[] = $table;
  }

  if (!isset($_POST["action"])) $_POST["action"] = "";

  if ($_POST["action"] == "backup")
  {
    $xml = "<BACKUP>\n";

    foreach($tables as $table)
    {
      if (isset($_POST[$table]))
      {
        $xml .= database_toolDumpTable($table);
      }
    }
    $xml .= "</BACKUP>";

    header("Content-Type: application/octet-stream");

    header("Content-Disposition: attachment; filename=Backup".date("Ymd").".xml");

    print $xml;

    exit();
  }

  function myRecordHandler($record)
  {
    global $config_databaseTablePrefix;

    global $table;

    global $first;

    if ($first)
    {
      $sql = "TRUNCATE `".$config_databaseTablePrefix.$table."`";

      database_queryModify($sql,$result);
    }

    database_toolRestoreRecord($table,$record);

    $first = FALSE;
  }

  if ($_POST["action"] == "restore")
  {
    if (is_uploaded_file($_FILES['backup']['tmp_name']))
    {
      $xml = file_get_contents($_FILES['backup']['tmp_name']);

      foreach($tables as $table)
      {
        $first = TRUE;

        MagicParser_parse("string://".$xml,"myRecordHandler","xml|BACKUP/".strtoupper($table)."/".strtoupper($table)."_R/");
      }

      header("Location: ".$config_baseHREF."admin/");

      exit();
    }
    else
    {
      widget_errorSet("backup","file upload failed");
    }
  }

  require("admin_header.php");

  print "<h2>".translate("Database Tool")."</h2>";

  print "<div class='row'>";

  print "<div class='medium-6 columns'>";

  print "<h3>".translate("Backup")."</h3>";

  widget_formBegin();

  print "<label>Select Tables</label><br />";

  foreach($tables as $table)
  {
    widget_checkBox($table." (".$tableNames[$table].")",$table,FALSE,TRUE,12);
  }

  widget_formHidden("action","backup");

  widget_formButtons(array("Backup"=>TRUE));

  widget_formEnd();

  print "</div>";

  print "<div class='medium-6 columns'>";

  print "<h3>".translate("Restore")."</h3>";

  print "<form method='post' enctype='multipart/form-data'>";

  widget_file("Backup Filename","backup",TRUE);

  widget_formHidden("action","restore");

  widget_formButtons(array("Restore"=>TRUE));

  print "</form>";

  print "</div>";

  print "</div>";

  require("admin_footer.php");
?>