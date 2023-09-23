<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  $enableDelete = (isset($_GET["enableDelete"])?TRUE:FALSE);

  if ($submit == "Add")
  {
    widget_required("name");

    if (!widget_errorcount_())
    {
      $_POST["name"] = trim($_POST["name"]);
    }

    if (!widget_errorcount_())
    {
      $sql = "SELECT name FROM `".$config_databaseTablePrefix."productsmap` WHERE name='".database_safe($_POST["name"])."'";

      if (database_querySelect($sql,$rows))
      {
        widget_errorSet("name","product name already exists");
      }
    }
    if (!widget_errorcount_())
    {
      $sql = sprintf("INSERT INTO `".$config_databaseTablePrefix."productsmap` SET
                      name = '%s'
                      ",
                      database_safe($_POST["name"])
                      );

      database_queryModify($sql,$insertId);

      header("Location: productsmap_configure.php?id=".$insertId);

      exit();
    }
  }

  require("admin_header.php");

  print "<h2>".translate("Product Mapping")."</h2>";

  print "<h3>".translate("New Product")."</h3>";

  widget_formBegin();

  widget_textBox("Name","name",TRUE,(isset($_POST["name"])?$_POST["name"]:""),"",3);

  widget_formButtons(array("Add"=>TRUE));

  widget_formEnd();

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."productsmap` ORDER BY name";

  print "<h3>".translate("Existing Products")."</h3>";

  if (database_querySelect($sql,$rows))
  {
    print "<table>";

    foreach($rows as $productmap)
    {
      print "<tr>";

      print "<th class='pta_key'>".$productmap["name"]."</th>";

      print "<td>";

      admin_tool("Configure","productsmap_configure.php?id=".$productmap["id"],TRUE,FALSE);

      if ($enableDelete) admin_tool("Delete","productsmap_delete.php?id=".$productmap["id"],TRUE,TRUE,"alert");

      print "</td>";

      print "</tr>";
    }

    print "</table>";

    if (!$enableDelete)
    {
      print "<p class='pta_to'>";

      admin_tool("Enable Delete","productsmap.php?enableDelete=1",TRUE,FALSE);

      print "</p>";
    }
  }
  else
  {
    print "<p>".translate("There are no products to display.")."</p>";
  }

  require("admin_footer.php");
?>
