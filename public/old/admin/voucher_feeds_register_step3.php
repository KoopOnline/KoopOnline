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

  if (isset($_POST["done"]))
  {
    $merchant_mappings = array();

    foreach($_POST as $k => $v)
    {
      if (substr($k,0,8)=="merchant")
      {
        $merchant = substr($k,9);

        if ($v)
        {
          $merchant_mappings[$v] = base64_decode($merchant);
        }
      }
    }

    $sql = "DELETE FROM `".$config_databaseTablePrefix."voucherfeeds` WHERE filename = '".database_safe($filename)."'";

    database_queryModify($sql,$insertId);

    $sql = "INSERT INTO `".$config_databaseTablePrefix."voucherfeeds` SET
      filename = '".database_safe($filename)."',
      format = '".database_safe($format)."',
      registered = '".time()."',
      field_merchant = '".database_safe($_POST["field_merchant"])."',
      field_code = '".database_safe($_POST["field_code"])."',
      field_valid_from = '".database_safe($_POST["field_valid_from"])."',
      field_valid_to = '".database_safe($_POST["field_valid_to"])."',
      field_description = '".database_safe($_POST["field_description"])."',
      merchant_mappings = '".database_safe(serialize($merchant_mappings))."'
      ";

    database_queryModify($sql,$result);

    switch($_POST["submit"])
    {
      case "Register":

        header("Location: voucher_codes.php");

        break;

      case "Register and List":

        header("Location: voucher_feeds_list.php?filename=".urlencode($filename));

        break;
    }

    exit();
  }

  require("admin_header.php");

  $feedMerchants = array();

  $feedMerchants[""] = "Select...";

  function myRecordHandler($record)
  {
    global $feedMerchants;

    $field_merchant = $_POST["field_merchant"];

    if ($record[$field_merchant])
    {
      $feedMerchants[$record[$field_merchant]] = $record[$field_merchant];
    }
  }

  MagicParser_parse($config_voucherCodesFeedDirectory.$filename,"myRecordHandler",$format);

  ksort($feedMerchants);

  $dataMerchants = array();

  $sql = "SELECT DISTINCT(merchant) FROM `".$config_databaseTablePrefix."products` ORDER BY merchant";

  if (database_querySelect($sql,$rows))
  {
    foreach($rows as $row)
    {
      $dataMerchants[] = $row["merchant"];
    }
  }

  print "<h2>".translate("Voucher Codes")."</h2>";

  print "<h3>".translate("Register")." (".$filename.") ".translate("Step 3 - Merchant Mapping")."</h3>";

  widget_formBegin("POST","");

  foreach($dataMerchants as $merchant)
  {
    widget_selectArray($merchant,"merchant_".base64_encode($merchant),FALSE,$merchant,$feedMerchants,3);
  }

  widget_formButtons(array("Register"=>TRUE,"Register and List"=>TRUE));

  unset($_POST["submit"]);

  foreach($_POST as $k => $v)
  {
    widget_formHidden($k,$v);
  }

  widget_formHidden("done","1");

  widget_formEnd();

  require("admin_footer.php");
?>