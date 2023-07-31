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

  $voucherRecord = array();

  $voucherCount = 0;

  $voucherRecordFields = array();

  function myRecordHandler($record)
  {
    global $voucherRecord;

    global $voucherCount;

    global $samplePage;

    $voucherCount++;

    $voucherRecord = $record;

    return ($voucherCount == $samplePage);
  }

  MagicParser_parse($config_voucherCodesFeedDirectory.$filename,"myRecordHandler",$format);

  function sampleBody()
  {
    global $voucherRecord;

    foreach($voucherRecord as $k => $v)
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

  $voucherRecordFields[""] = "Select..";

  foreach($voucherRecord as $k => $v)
  {
    $voucherRecordFields[$k] = $k;
  }

  function field($title,$name,$required=FALSE)
  {
    global $voucherRecordFields;

    global $config_voucherCodesCommonFields;

    global $widget_errors;

    $default = (isset($_POST["field_".$name]) ? $_POST["field_".$name] : "");

    if (!$default)
    {
      if (isset($config_voucherCodesCommonFields[$name]))
      {
        foreach($config_voucherCodesCommonFields[$name] as $test)
        {
          if (isset($voucherRecordFields[$test]))
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

    widget_selectArray("","field_".$name,$required,$default,$voucherRecordFields,12);

    print "</div>";

    print "</div>";
  }

  require("admin_header.php");

  print "<h2>".translate("Voucher Codes")."</h2>";

  print "<h3>".translate("Register")." (".$filename.") ".translate("Step 2 - Field Mapping")."</h3>";

  widget_formBegin("POST","voucher_feeds_register_step3.php?".htmlspecialchars($_SERVER["QUERY_STRING"],ENT_QUOTES,$config_charset));

  print "<div class='row'>";

  print "<div class='medium-6 columns'>";

  print "<fieldset>";

  print "<legend>".translate("Required Fields")."</legend>";

  $skipFields = array("valid_from","valid_to");

  foreach($config_voucherCodesFieldSet as $field => $caption)
  {
    if (in_array($field,$skipFields)) continue;

    field($caption,$field,TRUE);
  }

  print "</fieldset>";

  print "</div>";

  print "<div class='medium-6 columns'>";

  print "<fieldset>";

  print "<legend>".translate("Optional Fields")."</legend>";

  $skipFields = array("merchant","code","description");

  foreach($config_voucherCodesFieldSet as $field => $caption)
  {
    if (in_array($field,$skipFields)) continue;

    field($caption,$field);
  }

  print "</fieldset>";

  print "</div>";

  print "</div>";

  widget_formButtons(array("Next"=>TRUE));

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

  var sampleBaseHREF = "voucher_feeds_register_step2.php?filename=<?php print urlencode($_GET["filename"]); ?>&useFormat=<?php print urlencode($_GET["useFormat"]); ?>&formatDetected=<?php print urlencode($_GET["formatDetected"]); ?>&formatSelected=<?php print urlencode($_GET["formatSelected"]); ?>";

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
</script>

<?php

  require("admin_footer.php");
?>