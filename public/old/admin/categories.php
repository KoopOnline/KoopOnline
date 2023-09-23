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

    if (!widget_errorcount_())
    {
      $sql = "SELECT name FROM `".$config_databaseTablePrefix."categories` WHERE name='".database_safe($_POST["name"])."'";

      if (database_querySelect($sql,$rows))
      {
        widget_errorSet("name","category name already exists");
      }
    }
    if (!widget_errorcount_())
    {
      $sql = sprintf("INSERT INTO `".$config_databaseTablePrefix."categories` SET
                      name = '%s'
                      ",
                      database_safe($_POST["name"])
                      );

      database_queryModify($sql,$insertId);

      header("Location: categories_configure.php?id=".$insertId);

      exit();
    }
  }

  require("admin_header.php");

  print "<h2>".translate("Category Mapping")."</h2>";

  print "<h3>".translate("New Category")."</h3>";

  widget_formBegin();

  widget_textBox("Name","name",TRUE,(isset($_POST["name"])?$_POST["name"]:""),"",3);

  widget_formButtons(array("Add"=>TRUE));

  widget_formEnd();

  print "<h3>".translate("Existing Categories")."</h3>";

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."categories` ORDER BY name";

  if (database_querySelect($sql,$rows))
  {
    print "<table>";

    foreach($rows as $category)
    {
      print "<tr>";

      print "<th class='pta_key'>".$category["name"]."</th>";

      print "<td>";

      admin_tool("Configure","categories_configure.php?id=".$category["id"],TRUE,FALSE);

      if ($enableDelete) admin_tool("Delete","categories_delete.php?id=".$category["id"],TRUE,TRUE,"alert");

      print "</td>";

      print "</tr>";
    }

    print "</table>";

    if (!$enableDelete)
    {
      print "<p class='pta_to'>";

      admin_tool("Enable Delete","categories.php?enableDelete=1",TRUE,FALSE);

      print "</p>";
    }
  }
  else
  {
    print "<p>".translate("There are no categories to display.")."</p>";
  }

  require("admin_footer.php");
?>
