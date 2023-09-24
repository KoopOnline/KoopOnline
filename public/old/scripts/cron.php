<?php
  set_time_limit(0);

  chdir('/var/www/laravelapp/KoopOnline/public/old/scripts/');

  require("../includes/common.php");

  print "Cron start at ". date("H:i:s")."\n";

  if (isset($_SERVER["REQUEST_METHOD"]))
  {
    $password = (isset($_GET["password"])?$_GET["password"]:"");

    if ($password != $config_adminPassword)
    {
      header('HTTP/1.0 401 Unauthorized');

      print "<h1>401 Unauthorized</h1>";

      exit();
    }

    header("Content-Type: text/plain");
  }

  require("../includes/admin.php");

  require("../includes/automation.php");

  require("../includes/filter.php");

  require("../includes/MagicParser.php");

  function callback($progress)
  {
    global $feed;

    print chr(13)."importing ".$feed["filename"]."...[".$progress."/".$feed["products"]."]";
  }

  function fetch()
  {
    global $job;

    print chr(13)."fetching ".$job["filename"];

    $status = automation_run($job["id"]);

    print chr(13)."fetching ".$job["filename"]."...[".$status."]            \n";
  }

  function import()
  {
    global $feed;

    global $limit;

    print chr(13)."importing ".$feed["filename"]."...[0/".$feed["products"]."]";

    admin_import($feed["filename"],$limit,"callback");

    print chr(13)."importing ".$feed["filename"]."...[done]            \n";
  }

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."jobs` ORDER BY filename";

  if (database_querySelect($sql,$jobs))
  {
    foreach($jobs as $job)
    {
      fetch();
    }
  }

  echo "fetch skipped";

  exit();
?>
