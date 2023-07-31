<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  if ($submit == "Save")
  {
    $sql = "DELETE FROM `".$config_databaseTablePrefix."featured`";

    database_queryModify($sql,$insertId);

    $sequence = 1;

    $names = explode("\n",$_POST["names"]);

    $dupes = array();

    foreach($names as $name)
    {
      $name = trim($name);

      if ($name && (!isset($dupes[$name])))
      {
        $sql = "INSERT INTO `".$config_databaseTablePrefix."featured` SET name='".database_safe($name)."',sequence='".$sequence."'";

        database_queryModify($sql,$insertId);

        $dupes[$name] = 1;

        $sequence++;
      }
    }

    header("Location: featured.php");

    exit();
  }

  require("admin_header.php");

  $names = array();

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."featured` ORDER BY sequence";

  if (database_querySelect($sql,$rows))
  {
    foreach($rows as $featured)
    {
      $names[] = $featured["name"];
    }
  }

  $names = implode("\n",$names);

  print "<h2>".translate("Featured Products")."</h2>";

  print "<div class='row'>";

  print "<div class='small-6 columns'>";

  widget_formBegin();

  widget_textArea("Featured Product Names (one per line)","names",FALSE,$names,200,12);

  widget_formButtons(array("Save"=>TRUE));

  widget_formEnd();

  print "</div>";

  print "<div class='small-6 columns'>";

  $helper["field"] = "name";

  $helper["callbackExact"] = "featured_callbackExact";

  require("helper.php");

  print "</div>";
?>
  <script type='text/JavaScript'>

  function featured_callbackExact(name)
  {
    names = document.getElementById("names");

    if (names.value != "") names.value = names.value + "\n";

    names.value = names.value + name;
  }

  </script>
<?php
  require("admin_footer.php");
?>