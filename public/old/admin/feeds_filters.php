<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  require("../includes/filter.php");

  $filename = (isset($_GET["filename"])?$_GET["filename"]:"");

  if (isset($_GET["resequence"]))
  {
    $sql = "SELECT id,created FROM `".$config_databaseTablePrefix."filters` WHERE filename='".database_safe($filename)."' ORDER BY created";

    if (database_querySelect($sql,$filters))
    {
      $sql = "UPDATE `".$config_databaseTablePrefix."filters` SET created = '".database_safe($_GET["to"])."' WHERE id = '".database_safe($_GET["resequence"])."'";

      database_queryModify($sql,$result);

      $created = 0;

      foreach($filters as $filter)
      {
        if ($filter["id"]==$_GET["resequence"])
        {
          continue;
        }

        if ($created==$_GET["to"])
        {
          $created++;
        }

        $sql = "UPDATE `".$config_databaseTablePrefix."filters` SET created = '".$created."' WHERE id = '".$filter["id"]."'";

        database_queryModify($sql,$result);

        $created++;
      }
    }

    header("Location: feeds_filters.php?filename=".urlencode($filename));

    exit();
  }

  if (isset($_POST["submit"]))
  {
    widget_required("name");

    widget_required("field");

    if (!widget_errorcount_())
    {
      $sql = sprintf("INSERT INTO `".$config_databaseTablePrefix."filters` SET
                      filename = '%s',
                      field = '%s',
                      name = '%s',
                      created = '%s'
                      ",
                      database_safe($filename),
                      database_safe($_POST["field"]),
                      database_safe($_POST["name"]),
                      time()
                      );

      Database_queryModify($sql,$insertId);

      header("Location: feeds_filters_configure.php?filename=".urlencode($filename)."&id=".$insertId);

      exit();
    }

  }

  require("admin_header.php");

  if ($filename)
  {
    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE filename='".database_safe($filename)."'";

    database_querySelect($sql,$rows);

    $feed = $rows[0];

    $fields = array();

    foreach($config_fieldSet as $field => $caption)
    {
      $fields[$field] = $caption . ($feed["field_".$field]?" (".$feed["field_".$field].")":"");
    }
  }
  else
  {
    foreach($config_fieldSet as $field => $caption)
    {
      $fields[$field] = $caption;
    }
  }

  print "<h2>".translate("Admin")."</h2>";

  print "<h3>".translate("Filters")." (".($filename?$filename:"Global").")</h3>";

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."filters` WHERE filename='".database_safe($filename)."' ORDER BY created";

  if (database_querySelect($sql,$rows))
  {
    $first_created = $rows[0]["created"];

    $last_created = $rows[count_($rows)-1]["created"];

    print "<table>";

    print "<tr>";

    print "<td>&nbsp;</td>";

    print "<th></th>";

    print "<th>".translate("Field")."</th>";

    print "</tr>";

    foreach($rows as $k=> $filter)
    {
      print "<tr>";

      print "<th class='pta_key'>".$filter_names[$filter["name"]]."</th>";

      print "<td>";

      admin_tool("&#8593;","?filename=".urlencode($filename)."&resequence=".$filter["id"]."&to=".($k-1),($filter["created"]>$first_created),TRUE);

      admin_tool("&#8595;","?filename=".urlencode($filename)."&resequence=".$filter["id"]."&to=".($k+1),($filter["created"]<$last_created),TRUE);

      admin_tool("Configure","feeds_filters_configure.php?filename=".urlencode($filename)."&amp;id=".$filter["id"],TRUE,FALSE);

      admin_tool("Delete","feeds_filters_delete.php?filename=".urlencode($filename)."&amp;id=".$filter["id"],TRUE,FALSE);

      print "</td>";

      print "<td>".$fields[$filter["field"]]."</td>";

      print "</tr>";
    }

    print "</table>";
  }
  else
  {
    print "<p>".translate("There are no filters to display.")."</p>";
  }

  print "<h3>".translate("New Filter")."</h3>";

  widget_formBegin();

  $default = (isset($_POST["name"]) ? $_POST["name"] : "");

  widget_selectArray("Filter Type","name",TRUE,$default,array_merge(array(""=>"Select..."),$filter_names));

  $default = (isset($_POST["field"]) ? $_POST["field"] : "");

  widget_selectArray("Field","field",TRUE,$default,array_merge(array(""=>"Select..."),$fields));

  widget_formButtons(array("Add"=>TRUE));

  widget_formEnd();

  require("admin_footer.php");
?>
