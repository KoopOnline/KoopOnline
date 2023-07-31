<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $unmapped = (isset($_GET["unmapped"])?TRUE:FALSE);

  $filter = (isset($_GET["filter"])?$_GET["filter"]:"");

  $merchant = (isset($_GET["merchant"])?$_GET["merchant"]:"");

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  if ($submit == "Save")
  {
    $cat = (isset($_POST["cat"])?$_POST["cat"]:array());

    $def = (isset($_POST["def"])?$_POST["def"]:array());

    foreach($cat as $k => $v)
    {
      if ($def[$k] <> $v)
      {
        $category = str_replace("-"," ",$k);

        if ($def[$k])
        {
          $sql = "SELECT * FROM `".$config_databaseTablePrefix."categories_hierarchy` WHERE id = '".database_safe($def[$k])."'";

          database_querySelect($sql,$rows);

          $alternates = explode("\n",trim($rows[0]["alternates"]));

          $keys = array_keys($alternates,"=".$category);

          if (count($keys))
          {
            unset($alternates[$keys[0]]);
          }

          $alternates = implode("\n",$alternates);

          $sql = "UPDATE `".$config_databaseTablePrefix."categories_hierarchy` SET alternates = '".database_safe($alternates)."' WHERE id = '".database_safe($def[$k])."'";

          database_queryModify($sql,$rows);
        }

        if ($cat[$k])
        {
          $sql = "SELECT * FROM `".$config_databaseTablePrefix."categories_hierarchy` WHERE id = '".database_safe($cat[$k])."'";

          if (database_querySelect($sql,$rows))
          {
            $alternates = explode("\n",trim($rows[0]["alternates"]));

            $keys = array_keys($alternates,"=".$category);

            if (count($keys))
            {
              unset($alternates[$keys[0]]);
            }
          }
          else
          {
            $alternates = array();
          }
          $alternates[] = "=".$category;

          $alternates = implode("\n",$alternates);

          $sql = "UPDATE `".$config_databaseTablePrefix."categories_hierarchy` SET alternates = '".database_safe($alternates)."' WHERE id = '".database_safe($cat[$k])."'";

          database_queryModify($sql,$rows);
        }
      }
    }

    header("Location: categories_hierarchy.php");

    exit();
  }

  require("admin_header.php");

  $admin_importCategoryhMappings = array();

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."categories_hierarchy`";

  if (database_querySelect($sql,$rows))
  {
    foreach($rows as $category)
    {
      $alternates = explode("\n",trim($category["alternates"]));

      foreach($alternates as $alternate)
      {
        $alternate = trim($alternate);

        if ($alternate)
        {
          $admin_importCategoryhMappings[$alternate] = $category["id"];
        }
      }
    }
  }

  $lowerarchy = tapestry_categoryHierarchyLowerarchy(0);

  $options = array(0=>"[Not Mapped]") + tapestry_categoryHierarchyArray($lowerarchy);

  print "<h2>".translate("Category Hierarchy Mapping")."</h2>";

  print "<h3>Reverse Mapping (".($merchant?$merchant:translate("All")).")</h3>";

  widget_formBegin("GET");

  widget_formHidden("merchant",$merchant);

  widget_checkBox("Unmapped Only","unmapped",FALSE,$unmapped);

  widget_textBox("Filter","filter",FALSE,$filter,"",3);

  widget_formButtons(array("Apply Filter"=>TRUE));

  widget_formEnd();

  widget_formBegin();

  $sql = "SELECT DISTINCT(category) FROM `".$config_databaseTablePrefix."products` WHERE category <> ''";

  if ($unmapped)
  {
    $ins = array();

    foreach($admin_importCategoryhMappings as $alternate => $v)
    {
      $alternate = trim($alternate,"=");

      $ins[] = "'".database_safe($alternate)."'";
    }

    if (count($ins))
    {
      $in = implode(",",$ins);

      $sql .= " AND category NOT IN (".$in.")";
    }
  }

  if ($filter)
  {
    $sql .= " AND category LIKE '%".database_safe($filter)."%' ";
  }

  if ($merchant)
  {
    $sql .= " AND merchant = '".database_safe($merchant)."' ";
  }

  $sql .= " ORDER BY category";

  if (database_querySelect($sql,$rows))
  {
    foreach($rows as $k => $row)
    {
      $rows[$k]["default"] = (isset($admin_importCategoryhMappings["=".$row["category"]])?$admin_importCategoryhMappings["=".$row["category"]]:0);
    }

    $newRows = array();

    foreach($rows as $row)
    {
      $newRows[] = $row;
    }

    $rows = $newRows;
  }

  if (count($rows))
  {
    print "<div id='options' style='display:none;'>";

    print "<select style='margin:0;padding:0;' id='_ID_NAME' name='_ID_NAME'>";

    foreach($options as $value => $option)
    {
      print "<option value='".$value."'>".$option."</option>";
    }

    print "</select>";

    print "<script type='text/JavaScript'>";

    print "var id_names = new Array();";

    print "var defaults = new Array();";

    foreach($rows as $k => $row)
    {
      print "id_names[".$k."] = \"cat[".tapestry_hyphenate($row["category"])."]\";";

      print "defaults[".$k."] = \"".$row["default"]."\";";
    }

    print "</script>";

    print "</div>";

    print "<table>";

    print "<tr>";

    print "<th class='pta_key'>&nbsp;</th>";

    print "<td class='pta_tools'>";

    admin_tool("Edit All","JavaScript:rm_edit_all();",TRUE);

    print "</td>";

    print "<td>&nbsp;</td>";

    print "</tr>";

    foreach($rows as $k => $row)
    {
      print "<tr>";

      print "<th class='pta_key'>".$row["category"]."</th>";

      print "<td class='pta_tools'>";

      admin_tool("Edit","JavaScript:rm_edit(".$k.")",TRUE);

      print "<input type='hidden' name='def[".tapestry_hyphenate($row["category"])."]' value='".$row["default"]."' />";

      print "</td>";

      print "<td id='edit_".$k."'>";

      print (isset($options[$row["default"]])?$options[$row["default"]]:$options[0]);

      print "</td>";

      print "</tr>";
    }

    print "</table>";

    widget_formButtons(array("Save"=>TRUE),"categories_hierarchy.php");
  }
  else
  {
    print "<p>There are no categories to display.</p>";

    widget_formButtons(array(),"categories_hierarchy.php");
  }

  widget_formEnd();
  ?>
  <script type='text/JavaScript'>

  var rm_editing = [];

  function rm_edit(id)
  {
    if (rm_editing[id]) return;

    html = document.getElementById("options").innerHTML.replace(/_ID_NAME/g,id_names[id]);

    document.getElementById("edit_"+id).innerHTML = html;

    document.getElementById(id_names[id]).value = defaults[id];

    rm_editing[id] = true;
  }

  function rm_edit_all()
  {
    for(id=0;id<id_names.length;id++)
    {
      rm_edit(id);
    }
  }

  </script>
  <?php

  require("admin_footer.php");
?>