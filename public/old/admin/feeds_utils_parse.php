<?php
  set_time_limit(0);

  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  require("../includes/MagicParser.php");

  $filename = (isset($_GET["filename"])?$_GET["filename"]:"");

  if (!preg_match($config_filenameRegExp,$filename))
  {
    die("Invalid filename");
  }

  $vmerchant = (isset($_GET["vmerchant"])?base64_decode($_GET["vmerchant"]):"");

  $vname = (isset($_GET["vname"])?base64_decode($_GET["vname"]):"");

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE filename='".database_safe($filename)."'";

  if (!database_querySelect($sql,$feeds))
  {
    die("Invalid filename");
  }

  $feed = $feeds[0];

  $simport = array();

  $simport["records"] = 0;

  $simport["invalid_merchant"] = 0;

  $simport["invalid_name"] = 0;

  $simport["invalid_buy_url"] = 0;

  $simport["invalid_price"] = 0;

  $simport["duplicates"] = 0;

  $simport["products"] = 0;

  function myRecordHandler($record)
  {
    global $simport;

    global $feed;

    global $vmerchant;

    global $vname;

    global $vrecord;

    $simport["records"]++;

    $valid = TRUE;

    if ($feed["field_merchant"])
    {
      $merchant = (isset($record[$feed["field_merchant"]])?$record[$feed["field_merchant"]]:"");
    }
    else
    {
      $merchant = $feed["merchant"];
    }

    if (!$merchant)
    {
      $simport["invalid_merchant"]++;

      $valid = FALSE;
    }

    if ((!isset($record[$feed["field_name"]])) || (!$record[$feed["field_name"]]))
    {
      $simport["invalid_name"]++;

      $valid = FALSE;
    }

    if ((!isset($record[$feed["field_buy_url"]])) || (!$record[$feed["field_buy_url"]]))
    {
      $simport["invalid_buy_url"]++;

      $valid = FALSE;
    }

    if ((!isset($record[$feed["field_price"]])) || (!$record[$feed["field_price"]]))
    {
      $simport["invalid_price"]++;

      $valid = FALSE;
    }

    if ($valid)
    {
      if ($vmerchant && $vname)
      {
        if (($vmerchant == $merchant) && ($vname == $record[$feed["field_name"]]))
        {
          $vrecord = $record;

          return TRUE;
        }
      }

      $dupe_key = md5($merchant.$record[$feed["field_name"]]);

      if (!isset($simport["dupe_keys"][$dupe_key]))
      {
        $simport["products"]++;

        $simport["dupe_keys"][$dupe_key] = 0;
      }
      else
      {
        $simport["dupe_keys"][$dupe_key]++;

        $simport["duplicates"]++;

        if (!isset($simport["duplicates_detail"][$merchant][$record[$feed["field_name"]]]))
        {
          $simport["duplicates_detail"][$merchant][$record[$feed["field_name"]]] = 2;
        }
        else
        {
          $simport["duplicates_detail"][$merchant][$record[$feed["field_name"]]]++;
        }
      }
    }
  }

  if (!MagicParser_parse($config_feedDirectory.$filename,"myRecordHandler",$feed["format"]))
  {
    die(MagicParser_getErrorMessage());
  }

  require("admin_header.php");

  print "<h2>".translate("Feed Utilities")."</h2>";

  print "<h3>".translate("Parse Analysis")." (".$feed["filename"].")</h3>";

  if ($vmerchant && $vname)
  {
    print "<h3>".translate("View Record")."</h3>";

    print "<table style='width:100%'>";

    print "<tr>";

    print "<th style='width:25%;'>".translate("Field")."</th>";

    print "<th>&nbsp;</th>";

    print "</tr>";

    print "<tbody>";

    foreach($vrecord as $k => $v)
    {
      print "<tr>";

      print "<th class='pta_key'>".widget_safe($k)."</th>";

      print "<td>";

      print widget_safe($v);

      print "</td>";

      print "</tr>";
    }

    print "</tbody>";

    print "</table>";
  }
  else
  {
    admin_tableBegin();

    admin_tableRow("Parsed Records",$simport["records"],"pta_num");

    admin_tableRow("Invalid Merchant",($feed["field_merchant"]?$simport["invalid_merchant"]:"N/A"),"pta_num");

    admin_tableRow("Invalid Name",$simport["invalid_name"],"pta_num");

    admin_tableRow("Invalid Buy URL",$simport["invalid_buy_url"],"pta_num");

    admin_tableRow("Invalid Price",$simport["invalid_price"],"pta_num");

    admin_tableRow("Duplicate Names",$simport["duplicates"],"pta_num");

    admin_tableRow("Valid Records","<strong>".$simport["products"]."</strong>","pta_num");

    admin_tableEnd();

    if ($simport["duplicates"])
    {
      print "<h3>".translate("Duplicate Names Detail")."</h3>";

      print "<table>";

      print "<tr>";

      print "<th>".translate("Merchant")."</th>";

      print "<th>".translate("Name")."</th>";

      print "<th>#</th>";

      print "</tr>";

      foreach($simport["duplicates_detail"] as $merchant => $names)
      {
        foreach($names as $name => $count)
        {
          print "<tr>";

          print "<td>".$merchant."</td>";

          print "<td><a href='?filename=".urlencode($filename)."&vmerchant=".base64_encode($merchant)."&vname=".base64_encode($name)."'>".$name."</a></td>";

          print "<td class='pta_num'>".$count."</td>";

          print "</tr>";
        }
      }
    }
  }

  require("admin_footer.php");
?>