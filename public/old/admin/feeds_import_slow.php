<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/filter.php");

  require("../includes/MagicParser.php");

  function import_slow_pre()
  {
    global $config_databaseTablePrefix;

    global $config_slowImportBlock;

    global $admin_importFeed;

    $sql = "DELETE FROM `".$config_databaseTablePrefix."products` WHERE filename='".database_safe($admin_importFeed["filename"])."' LIMIT ".($config_slowImportBlock * 1);

    database_queryModify($sql,$insertId);
  }

  function import_slow_each()
  {
    global $admin_importLimit;

    global $admin_importCallback;

    $admin_importLimit = 0;

    $admin_importCallback = FALSE;

    admin_importSetGlobals();
  }

  function import_slow_post()
  {
    global $config_databaseTablePrefix;

    global $admin_importFeed;

    $sql = "SELECT COUNT(*) AS productCount FROM `".$config_databaseTablePrefix."products` WHERE filename='".database_safe($admin_importFeed["filename"])."'";

    database_querySelect($sql,$rows);

    $productCount = $rows[0]["productCount"];

    $sql = "UPDATE `".$config_databaseTablePrefix."feeds` SET imported='".time()."',products='".$productCount."' WHERE filename='".database_safe($admin_importFeed["filename"])."'";

    database_queryModify($sql,$insertId);
  }

  function slow__importRecordHandler($record)
  {
    global $config_slowImportBlock;

    global $progress;

    global $count;

    global $newoffset;

    $progress++;

    $count++;

    admin__importRecordHandler($record);

    if ($count==$config_slowImportBlock)
    {
      $newoffset = MagicParser_getOffsetData();

      return TRUE;
    }
  }

  require("admin_header.php");

  print "<h2>".translate("Slow Import")."</h2>";

  $refreshBase = $config_baseHREF."admin/";

  $filename = (isset($_GET["filename"])?$_GET["filename"]:"@ALL");

  $starttime = (isset($_GET["starttime"])?intval($_GET["starttime"]):time());

  $progress = (isset($_GET["progress"])?$_GET["progress"]:0);

  $offset = (isset($_GET["offset"])?$_GET["offset"]:"0|0");

  $count = 0;

  if ($filename=="@ALL")
  {
    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE imported < '".$starttime."' ORDER BY imported LIMIT 1";
  }
  else
  {
    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE filename = '".database_safe($filename)."'";
  }

  database_querySelect($sql,$rows);

  $admin_importFeed = $rows[0];

  if ($offset=="0|0")
  {
    import_slow_pre();

    $sql = "SELECT COUNT(*) AS numProducts FROM `".$config_databaseTablePrefix."products` WHERE filename = '".database_safe($admin_importFeed["filename"])."'";

    database_querySelect($sql,$rows);

    if ($rows[0]["numProducts"])
    {
      $refresh = $refreshBase."feeds_import_slow.php?filename=".$filename."&amp;progress=0&amp;offset=0|0&amp;starttime=".$starttime;

      $deleting = $rows[0]["numProducts"];
    }
  }

  if (!isset($deleting))
  {
    import_slow_each();

    MagicParser_parse($config_feedDirectory.$admin_importFeed["filename"],"slow__importRecordHandler",$admin_importFeed["format"],$offset);

    if (MagicParser_completed())
    {
      import_slow_post();

      $more = FALSE;

      if ($filename == "@ALL")
      {
        $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE imported < '".$starttime."' LIMIT 1";

        $more = database_querySelect($sql,$result);
      }

      if ($more)
      {
        $refresh = $refreshBase."feeds_import_slow.php?filename=".$filename."&amp;progress=0&amp;offset=0|0&amp;starttime=".$starttime;
      }
      else
      {
        admin_importReviews();

        $refresh = $refreshBase;
      }
    }
    else
    {
      $refresh = $refreshBase."feeds_import_slow.php?filename=".$filename."&amp;progress=".$progress."&amp;offset=".$newoffset."&amp;starttime=".$starttime;
    }
  }

  print "<table>";

  print "<tr>";

  print "<td>&nbsp;</td>";

  print "<th>".translate("Products")."</th>";

  print "</tr>";

  print "<tr>";

  print "<th class='pta_key'>".$admin_importFeed["filename"]."</th>";

  print "<td class='pta_num'>".(isset($deleting)?translate("DELETING"):$progress)."</td>";

  print "</tr>";

  print "</table>";

  print "<p class='pta_to'>";

  admin_tool("Abort","./",TRUE,FALSE);

  print "</p>";

  print "<meta http-equiv='refresh' content='".$config_slowImportSleep.";url=".$refresh."' />";

  require("admin_footer.php");
?>