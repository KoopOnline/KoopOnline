<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $action = (isset($_GET["action"])?$_GET["action"]:"");

  $name = (isset($_GET["name"])?trim($_GET["name"]):"");

  $id = (isset($_GET["id"])?$_GET["id"]:"");

  $enableDelete = (isset($_GET["enableDelete"])?TRUE:FALSE);

  function getids($id)
  {
    global $config_databaseTablePrefix;

    global $ids;

    $ids[] = "'".$id."'";

    $sql = "SELECT id FROM `".$config_databaseTablePrefix."categories_hierarchy` WHERE parent='".database_safe($id)."'";

    if (database_querySelect($sql,$rows))
    {
      foreach($rows as $row)
      {
        getids($row["id"]);
      }
    }
  }

  if ($action)
  {
    switch($action)
    {
      case "add":

        $sql = "INSERT INTO `".$config_databaseTablePrefix."categories_hierarchy` SET name = '".database_safe(tapestry_normalise($name))."' , parent='".database_safe($id)."'";

        database_queryModify($sql,$result);

        break;

      case "rename":

        $sql = "UPDATE `".$config_databaseTablePrefix."categories_hierarchy` SET name = '".database_safe(tapestry_normalise($name))."' WHERE id = '".database_safe($id)."'";

        database_queryModify($sql,$result);

        break;

      case "delete":

        $ids = array();

        getids($id);

        $sql = "DELETE FROM `".$config_databaseTablePrefix."categories_hierarchy` WHERE id IN (".implode(",",$ids).")";

        database_queryModify($sql,$result);

        $enableDelete = TRUE;

        break;


    }

    header("Location: categories_hierarchy.php".($enableDelete?"?enableDelete=1":""));

    exit();
  }

  require("admin_header.php");

  print "<h2>".translate("Category Hierarchy Mapping")."</h2>";

  function node($node)
  {
    global $config_databaseTablePrefix;

    global $enableDelete;

    global $depth;

    global $counts;

    $depth++;

    $counts[$depth]--;

    print "<tr>";

    print "<th class='pta_key'>";

    print "<span style='color:#ccc;'>";

    for($i=1;$i<$depth;$i++)
    {
      print "│&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    }

    print ($counts[$depth]?"├":"└")."──&nbsp;&nbsp;";

    print "</span>";

    print $node["name"];

    print "</th>";

    print "<td class='pta_tools'>";

    admin_tool("New","JavaScript:ch_add_onClick(".$node["id"].")",TRUE,TRUE);

    admin_tool("Configure","categories_hierarchy_configure.php?id=".$node["id"],TRUE);

    admin_tool("Rename","JavaScript:ch_rename_onClick(".$node["id"].",\"".$node["name"]."\")",TRUE);

    if ($enableDelete) admin_tool("Delete","JavaScript:ch_delete_onClick(".$node["id"].")",TRUE,TRUE,"alert");

    print "</td>";

    print "</tr>";

    $sql = "SELECT id,name FROM `".$config_databaseTablePrefix."categories_hierarchy` WHERE parent='".$node["id"]."' ORDER BY name";

    if ($counts[($depth+1)] = database_querySelect($sql,$nodes))
    {
      foreach($nodes as $node)
      {
        node($node);
      }
    }

    $depth--;
  }

  print "<h3>".translate("Existing Categories")."</h3>";

  print "<table>";

  print "<tr>";

  print "<th class='pta_key'><span style='color:#ccc;'>┬</span></th>";

  print "<td class='pta_tools'>";

  admin_tool("New","JavaScript:ch_add_onClick(0)",TRUE,TRUE);

  print "</td>";

  print "</tr>";

  $depth = 0;

  $counts = array();

  $sql = "SELECT id,name FROM `".$config_databaseTablePrefix."categories_hierarchy` WHERE parent='0' ORDER BY name";

  if ($counts[($depth+1)] = database_querySelect($sql,$nodes))
  {
    foreach($nodes as $node)
    {
      node($node);
    }
  }

  print "</table>";

  print "<p class='pta_to'>";

  if (count_($nodes) && !$enableDelete) admin_tool("Enable Delete","categories_hierarchy.php?enableDelete=1",TRUE,FALSE);

  print "</p>";

  print "<h3>".translate("Reverse Mapping")."</h3>";

  widget_formBegin("GET","categories_hierarchy_reverse.php");

  $merchants = array();

  $merchants[""] = "All";

  $sql = "SELECT DISTINCT(merchant) FROM `".$config_databaseTablePrefix."products` ORDER BY merchant";

  if (database_querySelect($sql,$rows))
  {
    foreach($rows as $row)
    {
      $merchants[$row["merchant"]] = $row["merchant"];
    }
  }

  widget_selectArray("Merchant","merchant",FALSE,"",$merchants,$columns=3,$atts="");

  widget_formButtons(array("Reverse Mapping"=>TRUE));

  widget_formEnd();

  ?>
  <script type='text/JavaScript'>

  function ch_add_onClick(id)
  {
    if ((name = window.prompt("New category name")))
    {
      window.location = '?action=add&id='+id+"&name="+encodeURIComponent(name);
    }
  }

  function ch_rename_onClick(id,name)
  {
    if ((name = window.prompt("Rename category to",name)))
    {
      window.location = '?action=rename&id='+id+"&name="+encodeURIComponent(name);
    }
  }

  function ch_mapping_onClick(id)
  {
    window.location = 'categories_hierarchy_configure.php?id='+id;
  }

  function ch_delete_onClick(id)
  {
    if (window.confirm("Delete category and all subcategories?"))
    {
      window.location = '?action=delete&id='+id;
    }
  }

  </script>
  <?php
  require("admin_footer.php");
?>
