<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $id = $_GET["id"];

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."productsmap` WHERE id='".database_safe($id)."'";

  database_querySelect($sql,$rows);

  $productmap = $rows[0];

  if ($productmap["meta"])
  {
    $productmap["meta"] = unserialize($productmap["meta"]);
  }

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  if ($submit == "Cancel")
  {
    header("Location: productsmap.php");

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

    if (isset($config_productMeta))
    {
      $meta = array();

      foreach($config_productMeta as $field => $label)
      {
        $meta[$field] = $_POST[$field];
      }

      $meta = serialize($meta);
    }
    else
    {
      $meta = "";
    }

    $sql = "UPDATE `".$config_databaseTablePrefix."productsmap` SET

              alternates  = '".database_safe($alternates)."',
              description = '".database_safe($_POST["description"])."',
              category    = '".database_safe($_POST["category"])."',
              brand       = '".database_safe($_POST["brand"])."',
              image_url   = '".database_safe($_POST["image_url"])."',
              meta        = '".database_safe($meta)."'

              WHERE id ='".database_safe($id)."'";

    database_queryModify($sql,$insertId);

    header("Location: productsmap.php");

    exit();
  }

  require("admin_header.php");

  print "<h2>".translate("Product Mapping")."</h2>";

  print "<h3>".translate("Configure")." (".$productmap["name"].")</h3>";

  print "<div class='row'>";

  print "<div class='small-6 columns'>";

  widget_formBegin();

  widget_textArea("Alternatives","alternates",FALSE,$productmap["alternates"],200,12);

  widget_textArea("Custom Description","description",FALSE,$productmap["description"],100,12);

  widget_textBox("Custom Category","category",FALSE,$productmap["category"],"",6);

  widget_textBox("Custom Brand","brand",FALSE,$productmap["brand"],"",6);

  widget_textBox("Custom Image URL","image_url",FALSE,$productmap["image_url"],"",6);

  if (isset($config_productMeta))
  {
    foreach($config_productMeta as $field => $label)
    {
      widget_textBox($label,$field,FALSE,(isset($productmap["meta"][$field])?$productmap["meta"][$field]:""),"",6);
    }
  }

  widget_formButtons(array("Save"=>TRUE),"productsmap.php");

  widget_formEnd();

  print "</div>";

  print "<div class='small-6 columns'>";

  $helper["field"] = "name";

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