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

  $merchant = (isset($_GET["merchant"])?$_GET["merchant"]:"");

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."voucherfeeds` WHERE filename='".database_safe($filename)."'";

  database_querySelect($sql,$voucherfeeds);

  $voucherfeed = $voucherfeeds[0];

  $voucherfeed["merchant_mappings"] = unserialize($voucherfeed["merchant_mappings"]);

  $vouchers = array();

  $sql = "SELECT id,merchant,code FROM `".$config_databaseTablePrefix."vouchers`";

  if ($merchant) $sql .= " WHERE merchant='".database_safe($merchant)."'";

  if (database_querySelect($sql,$rows))
  {
    foreach($rows as $row)
    {
      $vouchers[$row["merchant"]][$row["code"]] = $row["id"];
    }
  }

  require("admin_header.php");

  print "<h2>".translate("Voucher Codes")."</h2>";

  print "<h3>".translate("List")." (".$filename.")</h3>";

  widget_formBegin("GET");

  widget_formHidden("filename",$filename);

  $merchantFilterOptions = array();

  $merchantFilterOptions[""] = translate("Select")."...";

  foreach($voucherfeed["merchant_mappings"] as $v) $merchantFilterOptions[$v] = $v;

  widget_selectArray("Merchant Filter","merchant",TRUE,$merchant,$merchantFilterOptions);

  widget_formButtons(array("Apply"=>TRUE));

  widget_formEnd();

  print "<table>";

  print "<tr>";

  print "<th>".translate("Merchant")."</th>";

  print "<th>".translate("Voucher Code")."</th>";

  print "<td>&nbsp;</td>";

  print "<th>".translate("Description")."</th>";

  print "</tr>";

  $then = "voucher_feeds_list.php?filename=".urlencode($filename);

  if ($merchant) $then .= "&merchant=".urlencode($merchant);

  $then = base64_encode($then);

  function myRecordHandler($record)
  {
    global $config_charset;

    global $voucherfeed;

    global $vouchers;

    global $filename;

    global $merchant;

    global $now;

    global $then;

    $feedMerchant = $record[$voucherfeed["field_merchant"]];

    if (!isset($voucherfeed["merchant_mappings"][$feedMerchant])) return;

    $dataMerchant = $voucherfeed["merchant_mappings"][$feedMerchant];

    if ($merchant && ($merchant <> $dataMerchant)) return;

    $code = (isset($record[$voucherfeed["field_code"]])?$record[$voucherfeed["field_code"]]:"");

    if (!$code) return;

    $description = (isset($record[$voucherfeed["field_description"]])?$record[$voucherfeed["field_description"]]:"");

    $valid_from = (isset($record[$voucherfeed["field_valid_from"]])?$record[$voucherfeed["field_valid_from"]]:"");

    $valid_to = (isset($record[$voucherfeed["field_valid_to"]])?$record[$voucherfeed["field_valid_to"]]:"");

    print "<tr>";

    print "<td class='pta_key'>".$dataMerchant."</td>";

    print "<td class='pta_mid'>".$code."</td>";

    print "<td class='pta_tools'>";

    $exists = isset($vouchers[$dataMerchant][$code]);

    admin_tool("Add","voucher_codes_edit.php?id=0&amp;filename=".urlencode($filename)."&amp;merchant=".urlencode($dataMerchant)."&amp;code=".urlencode($code)."&amp;then=".$then,!$exists,FALSE);

    admin_tool("Edit",($exists?"voucher_codes_edit.php?id=".$vouchers[$dataMerchant][$code]."&amp;filename=".urlencode($filename)."&amp;merchant=".urlencode($dataMerchant)."&amp;code=".urlencode($code)."&amp;then=".$then:""),$exists,FALSE);

    admin_tool("Delete",($exists?"voucher_codes_delete.php?id=".$vouchers[$dataMerchant][$code]."&amp;then=".$then:""),$exists,FALSE);

    print "</td>";

    print "<td>".$description."</td>";

    print "</tr>";
  }

  MagicParser_parse($config_voucherCodesFeedDirectory.$filename,"myRecordHandler",$voucherfeed["format"]);

  print "</table>";

  print "<p class='pta_to'>";

  admin_tool("Back","voucher_codes.php",TRUE,FALSE);

  print "</p>";

  require("admin_footer.php");
?>