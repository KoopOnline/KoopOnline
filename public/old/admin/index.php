<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("admin_header.php");

  print "<h2>".translate("Admin Home")."</h2>";

  print "<h3>".translate("Feed Management")."</h3>";

  $totalProducts = 0;

  $totalClicks = 0;

  $error = "";

  if (!$error)
  {
    $dirHandle = opendir($config_feedDirectory);

    if (!$dirHandle) $error = "Could not aquire directory handle on ".$config_feedDirectory;
  }

  if (!$error)
  {
    $feeds = array();

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds`";

    if (database_querySelect($sql,$rows))
    {
      foreach($rows as $feed)
      {
        $feeds[$feed["filename"]] = $feed;
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
      if (!isset($feeds[$filename]))
      {
        $unregistered[] = $filename;
      }
      else
      {
        $registered[] = $filename;
      }
    }

    foreach($feeds as $filename => $feed)
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

      print "<th>".translate("Imported")."</th>";

      print "<th>".translate("Products")."</th>";

      if ($config_useTracking)
      {
        print "<th>".translate("Clicks")."</th>";
      }

      print "</tr>";

      foreach($unregistered as $filename)
      {
        $modified = filemtime($config_feedDirectory.$filename);

        print "<tr>";

        print "<th class='pta_key'>".$filename."</th>";

        print "<td class='pta_tools'>";

        admin_tool("Register","feeds_register_step1.php?filename=".urlencode($filename),TRUE,TRUE);

        admin_tool("Filters","",FALSE,FALSE);

        admin_tool("Import","",FALSE,FALSE);

        admin_tool("Slow Import","",FALSE,FALSE);

        print "</td>";

        print "<td>".admin_rfctime($modified)."</td>";

        print "<td>&nbsp;</td>";

        print "<td>&nbsp;</td>";

        if ($config_useTracking)
        {
          print "<td>&nbsp;</td>";
        }

        print "</tr>";
      }

      foreach($registered as $filename)
      {
        $modified = filemtime($config_feedDirectory.$filename);

        print "<tr>";

        print "<th class='pta_key'>".$filename."</th>";

        print "<td class='pta_tools'>";

        admin_tool("Register","feeds_register_step1.php?filename=".urlencode($filename),TRUE,FALSE);

        admin_tool("Filters","feeds_filters.php?filename=".urlencode($filename),TRUE,FALSE);

        admin_tool("Import","feeds_import.php?filename=".urlencode($filename),TRUE,($feeds[$filename]["imported"] < $modified));

        admin_tool("Slow Import","feeds_import_slow.php?filename=".urlencode($filename),TRUE,($feeds[$filename]["imported"] < $modified));

        print "</td>";

        print "<td>".admin_rfctime($modified)."</td>";

        print "<td>".($feeds[$filename]["imported"] ? admin_rfctime($feeds[$filename]["imported"]) : "&nbsp;")."</td>";

        print "<td class='pta_num'>".($feeds[$filename]["imported"] ? $feeds[$filename]["products"] : "&nbsp;")."</td>";

        $totalProducts += $feeds[$filename]["products"];

        if ($config_useTracking)
        {
          print "<td class='pta_num'>";
		  if ($feeds[$filename]["clicks"] > 0) { 
			  print "<big><b>";
			  print ($feeds[$filename]["imported"] ? $feeds[$filename]["clicks"] : "&nbsp;");
		  }
		  print "</td>";
          $totalClicks += $feeds[$filename]["clicks"];
        }

        print "</tr>";
      }

      foreach($deleted as $filename)
      {
        print "<tr>";

        print "<th class='pta_key'>".$filename."</th>";

        print "<td class='pta_tools'>";

        admin_tool("De-Register","feeds_deregister.php?filename=".urlencode($filename),TRUE,TRUE);

        print "</td>";

        print "<td>&nbsp;</td>";

        print "<td>&nbsp;</td>";

        print "<td>&nbsp;</td>";

        if ($config_useTracking)
        {
          print "<td>&nbsp;</td>";

          $totalClicks += $feeds[$filename]["clicks"];
        }

        print "</tr>";
      }

      print "</table>";
    }
    else
    {
      print "<p>There are no feeds to display.</p>";

      print "<p>To get started either upload a feed to your <strong>".$config_baseHREF."feeds/</strong> folder or go to <a data-dropdown='drop1'>Setup</a> &raquo; <a href='automation_tool.php'>Automation Tool</a> and then create and run a <a href='automation_tool_edit.php?id=0'>New Job</a>.</p>";
    }
  }

  if ($error)
  {
    print "<p>".$error."</p>";
  }

  print "<p class='pta_to'>";

  admin_tool("Global Filters","feeds_filters.php",TRUE,FALSE);

  admin_tool("Import All","feeds_import.php",count_($registered),FALSE);

  admin_tool("Slow Import All","feeds_import_slow.php",count_($registered),FALSE);

  print "</p>";

  print "<h3>Site Summary</h3>";

  admin_tableBegin();

  admin_tableRow("Total Products",$totalProducts,"pta_num");

  if ($config_useTracking)
  {
    admin_tableRow("Total Clicks",$totalClicks,"pta_num");
  }

  admin_tableEnd();

  require("admin_footer.php");
?>
