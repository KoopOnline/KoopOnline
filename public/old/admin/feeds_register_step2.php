<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  require("../includes/MagicParser.php");

  $filename = $_GET["filename"];

  if (!preg_match($config_filenameRegExp,$filename))
  {
    die("Invalid filename");
  }

  $format = $_GET[$_GET["useFormat"]];

  $samplePage = (isset($_GET["samplePage"])?intval($_GET["samplePage"]):1);

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  if ($submit)
  {
    if (($_POST["merchant"]=="") && ($_POST["field_merchant"]==""))
    {
      widget_errorSet("merchant","required field");
    }
    else
    {
      widget_validate("merchant",FALSE,"normalised");
    }

    widget_validate("category",FALSE,"normalised");

    widget_validate("brand",FALSE,"normalised");

    widget_required("field_name");

    widget_required("field_buy_url");

    widget_required("field_price");

    if (!widget_errorcount_())
    {
      $registerFields = array();

      foreach($config_fieldSet as $field => $caption)
      {
        $registerFields[$field] = (isset($_POST["field_".$field])?$_POST["field_".$field]:"");
      }

      admin_register(
          $filename,
          $format,
          (isset($_POST["merchant"])?$_POST["merchant"]:""),
          (isset($_POST["field_merchant"])?$_POST["field_merchant"]:""),
          $registerFields,
          (isset($_POST["category"])?$_POST["category"]:""),
          (isset($_POST["brand"])?$_POST["brand"]:"")
        );

      switch($submit)
      {
        case "Register":
          header("Location: ".$config_baseHREF."admin/");
          break;

        case "Register and Trial Import":
          header("Location: feeds_import.php?limit=10&filename=".urlencode($filename));
          break;

        case "Register and Full Import":
          header("Location: feeds_import.php?limit=0&filename=".urlencode($filename));
          break;

      }

      exit();
    }
  }

  $productRecord = array();

  $productCount = 0;

  $productRecordFields = array();

  function myRecordHandler($record)
  {
    global $productRecord;

    global $productCount;

    global $samplePage;

    $productCount++;

    $productRecord = $record;

    return ($productCount == $samplePage);
  }

  MagicParser_parse($config_feedDirectory.$filename,"myRecordHandler",$format);

  function sampleBody()
  {
    global $productRecord;

    foreach($productRecord as $k => $v)
    {
      print "<tr>";

      print "<th class='pta_key'>".widget_safe($k)."</th>";

      print "<td>";

      if (strlen($v) > 100)
      {
        print widget_safe(substr($v,0,100))."...";
      }
      else
      {
        print widget_safe($v);
      }

      print "</td>";

      print "</tr>";
    }
  }

  if (isset($_GET["samplePage"]))
  {
    print sampleBody();

    exit();
  }

  $productRecordFields[""] = "select field...";

  foreach($productRecord as $k => $v)
  {
    $productRecordFields[htmlspecialchars($k,ENT_QUOTES,$config_charset)] = $k;
  }

  function field($title,$name,$required)
  {
    global $productRecordFields;

    global $config_commonFields;

    global $widget_errors;

    $default = (isset($_POST["field_".$name]) ? $_POST["field_".$name] : "");

    if (!$default)
    {
      if (isset($config_commonFields[$name]))
      {
        foreach($config_commonFields[$name] as $test)
        {
          if (isset($productRecordFields[$test]))
          {
            $default = $test;

            break;
          }
        }
      }
    }

    print "<div class='row collapse'>";

    print "<div class='medium-6 columns'>";

    widget_label($title,"field_".$name,$required,TRUE);

    print "</div>";

    print "<div class='medium-6 columns'>";

    widget_selectArray("","field_".$name,$required,$default,$productRecordFields,12);

    print "</div>";

    print "</div>";
  }

  function field_custom($title,$name,$required)
  {
    global $productRecordFields;

    global $config_commonFields;

    global $widget_errors;

    print "<div class='row collapse'>";

    widget_label($title,$name,$required);

    print "<div class='medium-6 columns'>";

    print "<span class='prefix' style='background-color:#fff;padding-right:10px;border:0'>";

    $default = (isset($_POST[$name]) ? $_POST[$name] : "");

    widget_textBox("",$name,FALSE,$default,"Enter a ".$name." name or",12,"onKeyDown='JavaScript:customClearField(\"".$name."\");'");

    print "</span>";

    print "</div>";

    print "<div class='medium-6 columns'>";

    if (!$default)
    {
      $default = (isset($_POST["field_".$name]) ? $_POST["field_".$name] : "");

      if (!$default)
      {
        if (isset($config_commonFields[$name]))
        {
          foreach($config_commonFields[$name] as $test)
          {
            if (isset($productRecordFields[$test]))
            {
              $default = $test;

              break;
            }
          }
        }
      }
    }

    widget_selectArray("","field_".$name,FALSE,$default,$productRecordFields,12,"onChange='JavaScript:customClearText(\"".$name."\");'");

    print "</div>";

    print "</div>";
  }

  require("admin_header.php");

  print "<h2>".translate("Admin")."</h2>";

  print "<h3>".translate("Register")." (".$filename.") ".translate("Step 2 - Merchant Info and Field Mapping")."</h3>";

  widget_formBegin("POST","");

  print "<div class='row'>";

  print "<div class='medium-6 columns'>";

  print "<fieldset>";

  print "<legend>".translate("Required Fields")."</legend>";

  field_custom("Merchant Name","merchant",TRUE);

  $skipFields = array("name","buy_url","price");

  foreach($config_fieldSet as $field => $caption)
  {
    if (!in_array($field,$skipFields)) continue;

    field($caption,$field,TRUE);
  }

  print "</fieldset>";

  print "</div>";

  print "<div class='medium-6 columns'>";

  print "<fieldset>";

  print "<legend>".translate("Optional Fields")."</legend>";

  field_custom("Category","category",FALSE);

  field_custom("Brand","brand",FALSE);

  $skipFields = array("category","brand","name","buy_url","price");

  foreach($config_fieldSet as $field => $caption)
  {
    if (in_array($field,$skipFields)) continue;

    field($caption,$field,FALSE);
  }

  print "</fieldset>";

  print "</div>";

  print "</div>";

  widget_formButtons(array("Register"=>TRUE,"Register and Trial Import"=>TRUE,"Register and Full Import"=>TRUE));

  widget_formEnd();

  print "<table style='width:100%'>";

  print "<tr>";

  print "<th style='width:25%;'>".translate("Field")."</th>";

  print "<th><span id='samplePrev'></span>".translate("Sample Data")."<span id='sampleNext'></span></th>";

  print "</tr>";

  print "<tbody id='sampleBody'>";

  print sampleBody();

  print "</tbody>";

  print "</table>";
?>
<script type='text/JavaScript'>
  var samplePage = 1;

  var sampleBaseHREF = "feeds_register_step2.php?filename=<?php print urlencode($_GET["filename"]); ?>&useFormat=<?php print urlencode($_GET["useFormat"]); ?>&formatDetected=<?php print urlencode($_GET["formatDetected"]); ?>&formatSelected=<?php print urlencode($_GET["formatSelected"]); ?>";

  function sampleUpdateNav()
  {
    if (samplePage > 1)
    {
      document.getElementById("samplePrev").innerHTML = "<button id='samplePrevButton' class='pta_button small radius' onclick='JavaScript:sampleLoad(-1);'>&laquo;</button>";
    }
    else
    {
      document.getElementById("samplePrev").innerHTML = "<button id='samplePrevButton' class='pta_button small radius secondary disabled'>&laquo;</button>";
    }

    document.getElementById("sampleNext").innerHTML = "<button id='sampleNextButton' class='pta_button small radius' onclick='JavaScript:sampleLoad(1);'>&raquo;</button>";

    $("#samplePrevButton").prop("disabled",false);

    $("#sampleNextButton").prop("disabled",false);
  }

  function sampleLoad(delta)
  {
    $("#samplePrevButton").prop("disabled",true);

    $("#sampleNextButton").prop("disabled",true);

    samplePage += delta;

    $("#sampleBody").load(sampleBaseHREF+"&samplePage="+samplePage,"",function(){ sampleUpdateNav() });
  }

  sampleUpdateNav();

  function customClearText(name)
  {
    $("#"+name).val("");
  }

  function customClearField(name)
  {
    $("#field_"+name).val("");
  }
</script>

<?php
  require("admin_footer.php");
?>
