<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  require("../includes/MagicParser.php");

  $filename = (isset($_GET["filename"])?$_GET["filename"]:"");

  if ($filename && !preg_match($config_filenameRegExp,$filename))
  {
    die("Invalid filename");
  }

  if (!$config_currencyHTML) $config_currencyHTML = "#";

  if (isset($_GET["helper"]))
  {
    $merchant = $_GET["merchant"];

    $match_field = $_GET["match_field"];

    $match_value = $_GET["match_value"];

    $match_fields = array("name","category","brand");

    if (!in_array($match_field,$match_fields)) exit();

    $match_values = explode(",",$match_value);

    $wheres = array();

    foreach($match_values as $match_value)
    {
      $wheres[] = "(".$match_field." LIKE '%".database_safe($match_value)."%')";
    }

    $where = "merchant='".database_safe($merchant)."' AND (".implode(" OR ",$wheres).")";

    $sql = "SELECT DISTINCT(".$match_field.") FROM `".$config_databaseTablePrefix."products` WHERE ".$where." AND ".$match_field." <> '' ORDER BY ".$match_field;

    database_querySelect($sql,$rows);

    foreach($rows as $row)
    {
      print "<option value='".htmlspecialchars($row[$match_field],ENT_QUOTES,$config_charset)."'>".htmlspecialchars($row[$match_field],ENT_QUOTES,$config_charset)."</option>";
    }

    exit();
  }

  $id = $_GET["id"];

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  $then = (isset($_GET["then"])?base64_decode($_GET["then"]):"voucher_codes.php");

  if ($submit == "Cancel")
  {
    header("Location: ".$then);

    exit();
  }

  if ($submit == "Save")
  {
    widget_required("merchant");

    widget_required("code");

    widget_validate("discount_value",FALSE,"numeric");

    widget_validate("min_price",FALSE,"numeric");

    if(($_POST["discount_value"] == "") && ($_POST["discount_type"] <> "S"))
    {
      widget_errorSet("discount_value","discount value required for the selected discount type");
    }

    if(($_POST["min_price"] == "") && ($_POST["match_value"] == ""))
    {
      widget_errorSet("min_price","at least one of Match or Minimum Spend required");
    }

    if ($_POST["valid_from_d"] || $_POST["valid_from_m"] || $_POST["valid_from_y"])
    {
      $time = strtotime($_POST["valid_from_y"]."-".$_POST["valid_from_m"]."-".$_POST["valid_from_d"]);

      if ($time==FALSE)
      {
        $_POST["valid_from"] = "";

        widget_errorSet("valid","invalid entry");
      }
      else
      {
        $_POST["valid_from"] = $time;
      }
    }

    if ($_POST["valid_to_d"]<>"-" || $_POST["valid_to_m"]<>"-" || $_POST["valid_to_y"]<>"-")
    {
      $time = strtotime($_POST["valid_to_y"]."-".$_POST["valid_to_m"]."-".$_POST["valid_to_d"]);

      if ($time==FALSE)
      {
        $_POST["valid_to"] = "";

        widget_errorSet("valid","invalid entry");
      }
      else
      {
        $_POST["valid_to"] = $time + 86399;
      }
    }
    else
    {
      $_POST["valid_to"] = "";
    }

    if (!widget_errorcount_())
    {
      if ($_POST["discount_value"])
      {
        $_POST["discount_value"] = tapestry_decimalise($_POST["discount_value"]);
      }

      if ($_POST["min_price"] <> "")
      {
        $_POST["min_price"] = tapestry_decimalise($_POST["min_price"]);
      }

      if ($id)
      {
        $sql = "UPDATE `".$config_databaseTablePrefix."vouchers` SET ";
      }
      else
      {
        $sql = "INSERT INTO `".$config_databaseTablePrefix."vouchers` SET ";
      }

      $sql .= sprintf("
                      merchant = '%s',
                      code = '%s',
                      match_type = '%s',
                      match_field = '%s',
                      match_value = '%s',
                      discount_value = '%s',
                      discount_type = '%s',
                      discount_text = '%s',
                      min_price = '%s',
                      valid_from = '%s',
                      valid_to = '%s'
                      ",
                      database_safe($_POST["merchant"]),
                      database_safe(trim($_POST["code"])),
                      database_safe($_POST["match_type"]),
                      database_safe($_POST["match_field"]),
                      database_safe(trim($_POST["match_value"])),
                      database_safe($_POST["discount_value"]),
                      database_safe($_POST["discount_type"]),
                      database_safe(trim($_POST["discount_text"])),
                      database_safe($_POST["min_price"]),
                      database_safe($_POST["valid_from"]),
                      database_safe($_POST["valid_to"])
                      );

      if ($id)
      {
        $sql .= " WHERE id = '".$id."' ";
      }

      database_queryModify($sql,$insertId);

      header("Location: ".$then);

      exit();
    }
  }
  elseif($id)
  {
    $sql = "SELECT * FROM `".$config_databaseTablePrefix."vouchers` WHERE id='".database_safe($id)."'";

    database_querySelect($sql,$vouchers);

    foreach($vouchers[0] as $k => $v)
    {
      $_POST[$k] = $v;
    }
  }

  function myRecordHandler($record)
  {
    global $voucherfeed;

    global $voucherRecord;

    if (!isset($record[$voucherfeed["field_merchant"]])) return;

    if (!isset($record[$voucherfeed["field_code"]])) return;

    $feedMerchant = $record[$voucherfeed["field_merchant"]];

    $feedCode = $record[$voucherfeed["field_code"]];

    if (!isset($voucherfeed["merchant_mappings"][$feedMerchant])) return;

    if (
       ($voucherfeed["merchant_mappings"][$feedMerchant] == $_GET["merchant"])
       &&
       ($feedCode == $_GET["code"])
       )
     {
       $voucherRecord = $record;

       return TRUE;
     }
  }

  if ($filename)
  {
    $sql = "SELECT * FROM `".$config_databaseTablePrefix."voucherfeeds` WHERE filename='".database_safe($filename)."'";

    database_querySelect($sql,$voucherfeeds);

    $voucherfeed = $voucherfeeds[0];

    $voucherfeed["merchant_mappings"] = unserialize($voucherfeed["merchant_mappings"]);

    MagicParser_parse($config_voucherCodesFeedDirectory.$filename,"myRecordHandler",$voucherfeed["format"]);

    if (isset($voucherRecord) && !$id && !count_($_POST))
    {
      $_POST["merchant"] = $_GET["merchant"];

      $_POST["code"] = $_GET["code"];

      $description = $voucherRecord[$voucherfeed["field_description"]];

      $desc = strtolower($description);

      $desc = preg_replace('/[^A-Za-z0-9%\., ]/','',$desc);

      $desc = preg_replace('/[ ]{2,}/',' ',$desc);

      if (preg_match('/([\.0-9]*)% '.$config_voucherCodesDiscountValuePostfixRegexp.'/',$desc,$matches))
      {
        $_POST["discount_type"] = "%";

        $_POST["discount_value"] = tapestry_decimalise($matches[1]);
      }
      elseif (preg_match('/([\.0-9]*) '.$config_voucherCodesDiscountValuePostfixRegexp.'/',$desc,$matches))
      {
        $_POST["discount_type"] = "#";

        $_POST["discount_value"] = tapestry_decimalise($matches[1]);
      }
      elseif (preg_match('/'.$config_voucherCodesDiscountValuePrefixRegexp.' ([\.0-9]*)%/',$desc,$matches))
      {
        $_POST["discount_type"] = "%";

        $_POST["discount_value"] = tapestry_decimalise($matches[2]);
      }
      elseif (preg_match('/'.$config_voucherCodesDiscountValuePrefixRegexp.' ([\.0-9]*)/',$desc,$matches))
      {
        $_POST["discount_type"] = "#";

        $_POST["discount_value"] = tapestry_decimalise($matches[2]);
      }

      if (preg_match('/'.$config_voucherCodesMinSpendPrefixRegexp.' ([0-9]*)/',$desc,$matches))
      {
        $_POST["min_price"] = tapestry_decimalise($matches[2]);
      }

      if (isset($voucherfeed["field_valid_from"]))
      {
        $valid_from = $voucherRecord[$voucherfeed["field_valid_from"]];

        $valid_from = strtotime($valid_from);

        if (!$valid_from)
        {
          $valid_from = str_replace("/","-",$voucherRecord[$voucherfeed["field_valid_from"]]);

          $valid_from = strtotime($valid_from);
        }

        if ($valid_from)
        {
          $_POST["valid_from"] = $valid_from;
        }
      }

      if (isset($voucherfeed["field_valid_to"]))
      {
        $valid_to = $voucherRecord[$voucherfeed["field_valid_to"]];

        $valid_to = strtotime($valid_to);

        if (!$valid_to)
        {
          $valid_to = str_replace("/","-",$voucherRecord[$voucherfeed["field_valid_to"]]);

          $valid_to = strtotime($valid_to);
        }

        if ($valid_to)
        {
          $_POST["valid_to"] = $valid_to;
        }
      }
    }
  }

  require("admin_header.php");

  print "<h2>".translate("Voucher Codes")."</h2>";

  $sql = "SELECT DISTINCT(merchant) FROM `".$config_databaseTablePrefix."products` ORDER BY merchant";

  if (database_querySelect($sql,$rows))
  {
    if ($id)
    {
      print "<h3>".translate("Edit Voucher Code")."</h3>";
    }
    else
    {
      print "<h3>".translate("New Voucher Code")."</h3>";
    }

    $merchants = array();

    foreach($rows as $row)
    {
      $merchants[$row["merchant"]] = $row["merchant"];
    }

    print "<div class='row'>";

    print "<div class='medium-6 columns'>";

    widget_formBegin();

    print "<div class='row'>";

      print "<div class='medium-6 columns'>";

      widget_selectArray("Merchant","merchant",TRUE,(isset($_POST["merchant"]) ? $_POST["merchant"] : ""),$merchants,12);

      print "</div>";

      print "<div class='medium-6 columns'>";

      widget_textBox("Voucher Code","code",TRUE,(isset($_POST["code"]) ? $_POST["code"] : ""),"",12);

      print "</div>";

    print "</div>";

    print "<fieldset><legend>".translate("Match Criteria")."</legend>";

      $match_types = array("exact"=>"Exact Match","keyword"=>"Keyword Match");

      widget_selectArray("","match_type",FALSE,(isset($_POST["match_type"]) ? $_POST["match_type"] : ""),$match_types,12);

      $ignore = array("description","image_url","buy_url","price");

      $match_fields = array();

      foreach($config_fieldSet as $k => $v)
      {
        if (!in_array($k,$ignore))
        {
          $match_fields[$k] = $v;
        }
      }

      widget_selectArray("","match_field",FALSE,(isset($_POST["match_field"]) ? $_POST["match_field"] : ""),$match_fields,12);

      print "<div class='row collapse'>";

        print "<div class='small-10 columns'>";

          print "<input type='text' name='match_value' id='match_value' value='".(isset($_POST["match_value"]) ? htmlspecialchars($_POST["match_value"],ENT_QUOTES,$config_charset) : "")."' placeholder='Against' />";

        print "</div>";

        print "<div class='small-2 columns'>";

          print "<button id='helper_submit' type='button' class='button tiny postfix' onclick='JavaScript:helper_submitOnClick();return false;'>Search</button>";

        print "</div>";

        print "<div class='small-12 columns'>";

          print "<select id='helper_results' name='helper_results' size='7' style='width:100%;height:110px;' onchange='JavaScript:helper_resultsOnChange();' ></select>";

        print "</div>";

      print "</div>";

      widget_textBox("Minimum Spend","min_price",FALSE,(isset($_POST["min_price"]) ? $_POST["min_price"] : ""),$config_currencyHTML,6);

    print "</fieldset>";

    print "<fieldset><legend>".translate("Offer Details")."</legend>";

      $discount_types = array("#"=>html_entity_decode($config_currencyHTML,ENT_QUOTES,$config_charset),"%"=>"%","S"=>"Other");

      widget_selectArray("Discount Type","discount_type",TRUE,(isset($_POST["discount_type"]) ? $_POST["discount_type"] : "#"),$discount_types,6,"onchange='JavaScript:discount_typeOnChange();'");

      print "<div id='discount_value_c'>";

      widget_textBox("Discount Value","discount_value",FALSE,(isset($_POST["discount_value"]) ? $_POST["discount_value"] : ""),"",6);

      print "</div>";

      widget_textBox("Description (for discount type \"Other\")","discount_text",FALSE,(isset($_POST["discount_text"]) ? $_POST["discount_text"] : ""),"",12);

    print "</fieldset>";

    print "<fieldset><legend>".translate("Valid")."</legend>";

      widget_date("From","valid_from",(isset($_POST["valid_from"]) ? $_POST["valid_from"] : time()),"");

      widget_date("Until","valid_to",(isset($_POST["valid_to"]) ? $_POST["valid_to"] : ""),"-");

    print "</fieldset>";

    widget_formButtons(array("Save"=>TRUE),($filename?"voucher_feeds_list.php?filename=".urlencode($filename):"voucher_codes.php"));

    widget_formEnd();

    ?>

    <script type='text/JavaScript'>

    function helper_submitOnClick()
    {
      $("#helper_submit").prop("disabled",true);

      href = 'voucher_codes_edit.php?helper=1&merchant='+encodeURIComponent($("#merchant").val())+'&match_field='+encodeURIComponent($("#match_field").val())+'&match_value='+encodeURIComponent($("#match_value").val())

      $("#helper_results").load(href,function() {helper_searchDone(); });
    }

    function helper_searchDone()
    {
      $("#helper_submit").prop("disabled",false);

      $("#helper_results").css("display","block");

      helper_firstDblClick = true;
    }

    function helper_resultsDblClick()
    {
      if (helper_firstDblClick)
      {
        $("#match_value").val("");
      }
      else if ($("#match_value").val())
      {
        $("#match_value").val($("#match_value").val()+",")
      }

      $("#match_value").val($("#match_value").val()+$("#helper_results").val())

      helper_firstDblClick = false;
    }

    $("#helper_results").dblclick( function() { helper_resultsDblClick() });

    var helper_firstDblClick = false;

    function discount_typeOnChange()
    {
      document.getElementById('discount_value').placeholder = '';

      switch(document.getElementById('discount_type').value)
      {
        case '#':

          document.getElementById('discount_value').placeholder = '0.00';

          break;

        case '%':

          document.getElementById('discount_value').placeholder = '0';

          break;
      }
    }

    discount_typeOnChange();

    </script>

    <?php

    print "</div>";

    print "<div class='medium-6 columns'>";

    if (isset($voucherRecord))
    {
      print "<table style='width:100%'>";

      print "<tr>";

      print "<th style='width:25%;'>".translate("Field")."</th>";

      print "<th><span id='samplePrev'></span>".translate("Sample Data")."<span id='sampleNext'></span></th>";

      print "</tr>";

      foreach($voucherRecord as $k => $v)
      {
        print "<tr>";

        print "<th class='pta_key'>".widget_safe($k)."</th>";

        print "<td>".widget_safe($v)."</td>";

        print "</tr>";
      }
    }

    print "</div>";

    print "</div>";
  }
  else
  {
    print "<p>At least 1 product feed must be imported before adding a new voucher code.</p>";
  }
  require("admin_footer.php");
?>
