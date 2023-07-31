<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $id = $_GET["id"];

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."categories` WHERE id='".database_safe($id)."'";

  database_querySelect($sql,$rows);

  $category = $rows[0];

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  if ($submit == "Cancel")
  {
    header("Location: categories.php");

    exit();
  }

  if ($submit == "Save")
  {
    if ($_POST["alternates"])
    {
      $uniqueAlternates1 = array();

      $alternates = explode("\n",$_POST["alternates"]);

      foreach($alternates as $alternate)
      {
        $uniqueAlternates1[trim($alternate)] = 1;
      }

      $uniqueAlternates2 = array();

      foreach($uniqueAlternates1 as $alternate => $v)
      {
        $uniqueAlternates2[] = $alternate;
      }

      asort($uniqueAlternates2);

      $alternates = implode("\n",$uniqueAlternates2);
    }
    else
    {
      $alternates = "";
    }

    $sql = "UPDATE `".$config_databaseTablePrefix."categories` SET alternates = '".database_safe($alternates)."' WHERE id='".database_safe($id)."'";

    database_queryModify($sql,$insertId);

    header("Location: categories.php");

    exit();
  }

  require("admin_header.php");

  print "<h2>Category Mapping</h2>";

  print "<h3>Configure (".$category["name"].")</h3>";

  print "<div class='row'>";

  print "<div class='small-6 columns'>";

  widget_formBegin();

  widget_textArea("Alternatives","alternates",FALSE,$category["alternates"],200,12);

  widget_formButtons(array("Save"=>TRUE),"categories.php");

  widget_formEnd();

  print "</div>";

  print "<div class='small-6 columns'>";

  $helper["field"] = "category";

  $helper["callbackKeyword"] = "callbackKeyword";

  $helper["callbackExact"] = "callbackExact";

  require("helper.php");

  print "</div>";

  print "</div>";
?>
  <script type='text/JavaScript'>

  function callbackKeyword(name)
  {
    alternates = document.getElementById("alternates");

    alternates.value = alternates.value + "\n" + name;
  }

  function callbackExact(name)
  {
    alternates = document.getElementById("alternates");

    alternates.value = alternates.value + "\n=" + name;
  }

  </script>
<?php
  require("admin_footer.php");
?>