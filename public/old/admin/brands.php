<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  $enableDelete = (isset($_GET["enableDelete"])?TRUE:FALSE);

  if ($submit == "Add")
  {
    widget_validate("name",TRUE,"normalised");

    if (!widget_errorCount())
    {
      $sql = "SELECT name FROM `".$config_databaseTablePrefix."brands` WHERE name='".database_safe($_POST["name"])."'";

      if (database_querySelect($sql,$rows))
      {
        widget_errorSet("name","brand name already exists");
      }
    }
    if (!widget_errorCount())
    {
      $sql = sprintf("INSERT INTO `".$config_databaseTablePrefix."brands` SET
                      name = '%s'
                      ",
                      database_safe($_POST["name"])
                      );

      database_queryModify($sql,$insertId);

      header("Location: brands_configure.php?id=".$insertId);

      exit();
    }
  }

  require("admin_header.php");

  print "<h2>".translate("Brand Mapping")."</h2>";

  print "<h3>".translate("New Brand")."</h3>";

  widget_formBegin();

  widget_textBox("Name","name",TRUE,(isset($_POST["name"])?$_POST["name"]:""),"",3);

  widget_formButtons(array("Add"=>TRUE));

  widget_formEnd();

  print "<h3>".translate("Existing Brands")."</h3>";

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."brands` ORDER BY name";

  if (database_querySelect($sql,$rows))
  {
    print "<table>";

    foreach($rows as $brand)
    {
      print "<tr>";

      print "<th class='pta_key'>".$brand["name"]."</th>";

      print "<td>";

      admin_tool("Configure","brands_configure.php?id=".$brand["id"],TRUE,FALSE);

      if ($enableDelete)
      {
        admin_tool("Delete","brands_delete.php?id=".$brand["id"],TRUE,TRUE,"alert");
      }

      print "</td>";

      print "</tr>";
    }

    print "</table>";

    if (!$enableDelete)
    {
      print "<p class='pta_to'>";

      admin_tool("Enable Delete","brands.php?enableDelete=1",TRUE,FALSE);

      print "</p>";
    }
  }
  else
  {
    print "<p>".translate("There are no brands to display.")."</p>";
  }

  require("admin_footer.php");
?>