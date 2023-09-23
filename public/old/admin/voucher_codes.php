<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  if (isset($_GET["action"]))
  {
    switch ($_GET["action"])
    {
      case "purge":

        $sql = "DELETE FROM `".$config_databaseTablePrefix."vouchers` WHERE valid_to > '0' AND valid_to < '".time()."'";

        database_queryModify($sql,$result);

        break;
    }

    header("Location: voucher_codes.php");

    exit();
  }

  require("admin_header.php");

  print "<h2>".translate("Voucher Codes")."</h2>";

  print "<h3>".translate("Voucher Code Feed Management")."</h3>";

  $error = "";

  if (!$error)
  {
    $dirHandle = opendir($config_voucherCodesFeedDirectory);

    if (!$dirHandle)
    {
      $error = "Could not aquire directory handle on ".$config_voucherCodesFeedDirectory;
    }
  }

  if (!$error)
  {
    $voucherfeeds = array();

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."voucherfeeds`";

    if (database_querySelect($sql,$rows))
    {
      foreach($rows as $voucherfeed)
      {
        $voucherfeeds[$voucherfeed["filename"]] = $voucherfeed;
      }
    }

    $filenames = array();

    while(($filename=readdir($dirHandle))!==FALSE)
    {
      if (substr($filename,0,1) <> ".")
      {
        $filenames[] = $filename;
      }
    }

    $unregistered = array();

    $registered = array();

    $deleted = array();

    foreach($filenames as $filename)
    {
      if (!isset($voucherfeeds[$filename]))
      {
        $unregistered[] = $filename;
      }
      else
      {
        $registered[] = $filename;
      }
    }

    foreach($voucherfeeds as $filename => $voucherfeed)
    {
      if (!in_array($filename,$filenames))
      {
        $deleted[] = $filename;
      }
    }

    if (count_($unregistered) + count_($registered) + count_($deleted))
    {
      asort($unregistered);

      asort($registered);

      asort($deleted);

      print "<table>";

      print "<tr>";

      print "<td>&nbsp;</td>";

      print "<th></th>";

      print "<th>".translate("Modified")."</th>";

      print "</tr>";

      foreach($unregistered as $filename)
      {
        $modified = filemtime($config_voucherCodesFeedDirectory.$filename);

        print "<tr>";

        print "<th class='pta_key'>".$filename."</th>";

        print "<td>";

        admin_tool("Register","voucher_feeds_register_step1.php?filename=".urlencode($filename),TRUE,TRUE);

        admin_tool("List","",FALSE,FALSE);

        print "</td>";

        print "<td>".admin_rfctime($modified)."</td>";

        print "</tr>";
      }

      foreach($registered as $filename)
      {
        $modified = filemtime($config_voucherCodesFeedDirectory.$filename);

        print "<tr>";

        print "<th class='pta_key'>".$filename."</th>";

        print "<td>";

        admin_tool("Register","voucher_feeds_register_step1.php?filename=".urlencode($filename),TRUE,FALSE);

        admin_tool("List","voucher_feeds_list.php?filename=".urlencode($filename),TRUE,FALSE);

        print "</td>";

        print "<td>".admin_rfctime($modified)."</td>";

        print "</tr>";
      }

      foreach($deleted as $filename)
      {
        print "<tr>";

        print "<th class='pta_key'>".$filename."</th>";

        print "<td class='pta_tools'>";

        admin_tool("De-Register","voucher_feeds_deregister.php?filename=".urlencode($filename),TRUE,TRUE);

        print "</td>";

        print "<td>&nbsp;</td>";

        print "</tr>";
      }

      print "</table>";
    }
    else
    {
      print "<p>There are no voucher code feeds to display.</p>";

      print "<p>To get started either upload a voucher code feed to your <strong>".$config_baseHREF."voucherfeeds/</strong> folder or go to <a data-dropdown='drop1'>Setup</a> &raquo; <a href='automation_tool.php'>Automation Tool</a> and then create and run a <a href='automation_tool_edit.php?id=0'>New Job</a>.</p>";
    }
  }

  if ($error)
  {
    print "<p>".$error."</p>";
  }

  print "<h3>".translate("Existing Voucher Codes")."</h3>";

  $vouchers = array();

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."vouchers` ORDER BY merchant,valid_from";

  if (database_querySelect($sql,$vouchers))
  {
    print "<table>";

    print "<tr>";

    print "<th>".translate("Merchant")."</th>";

    print "<th>Code</th>";

    print "<td>&nbsp;</td>";

    print "<th>".translate("Match")."</th>";

    print "<th>".translate("Against")."</th>";

    print "<th>".translate("Minimum Spend")."</th>";

    print "<th>".translate("Discount")."</th>";

    print "<th>".translate("Valid From")."</th>";

    print "<th>".translate("Valid To")."</th>";

    print "</tr>";

    foreach($vouchers as $voucher)
    {
      print "<tr>";

      print "<td>".$voucher["merchant"]."</td>";

      print "<td class='pta_mid'>".$voucher["code"]."</td>";

      print "<td class='pta_tools'>";

      admin_tool("Edit","voucher_codes_edit.php?id=".$voucher["id"],TRUE,FALSE);

      admin_tool("Delete","voucher_codes_delete.php?id=".$voucher["id"],TRUE,FALSE);

      print "</td>";

      if ($voucher["match_value"])
      {
        $match_types = array("exact"=>translate("Exact Match"),"keyword"=>translate("Keyword Match"));

        $match_fields = array("name"=>translate("Product Name"),"category"=>translate("Category"),"brand"=>translate("Brand"));

        print "<td>".$match_types[$voucher["match_type"]]." ".$match_fields[$voucher["match_field"]]."</td>";

        print "<td>".$voucher["match_value"]."</td>";
      }
      else
      {
        print "<td>-</td>";

        print "<td>-</td>";
      }

      print "<td class='pta_num'>".($voucher["min_price"]=="0.00"?"-":$config_currencyHTML.$voucher["min_price"])."</td>";

      if ($voucher["discount_type"]=="S")
      {
        if ($voucher["discount_text"])
        {
          print "<td class='pta_num'>".$voucher["discount_text"]."</td>";
        }
        else
        {
          print "<td class='pta_num'>Other</td>";
        }
      }
      elseif ($voucher["discount_type"]=="#")
      {
        print "<td class='pta_num'>".$config_currencyHTML.$voucher["discount_value"]."</td>";
      }
      else
      {
        print "<td class='pta_num'>".$voucher["discount_value"]."%</td>";
      }

      print "<td class='pta_num'>".date("Y-m-d",$voucher["valid_from"])."</td>";

      print "<td class='pta_mid'>".($voucher["valid_to"]?date("Y-m-d",$voucher["valid_to"]):"-")."</td>";

      print "</tr>";
    }

    print "</table>";
  }
  else
  {
    print "<p>".translate("There are no voucher codes to display.")."</p>";
  }

  print "<p class='pta_to'>";

  admin_tool("New Voucher Code","voucher_codes_edit.php?id=0",TRUE,FALSE);

  admin_tool("Purge Expired","voucher_codes.php?action=purge",count_($vouchers),FALSE);

  print "</p>";

  require("admin_footer.php");
?>
