<?php
  set_time_limit(0);
  ob_start();

  require("../includes/common.php");

  require("../includes/admin.php");

  require("../includes/automation.php");

  if ($_GET['password'] != '6205023ta')
  {
    print "Usage: fetch.php <filename>|@ALL\n"; exit;
  }

  $filename = $_GET['filename'];

  function fetch()
  {
    global $job;

    print chr(13)."fetching ".$job["filename"];
	  ob_flush(); flush();

    $status = automation_run($job["id"]);

    print chr(13)."fetching ".$job["filename"]."...[".$status."]            \n";
	  ob_flush(); flush();
  }

  if ($filename == "@ALL")
  {
    $sql = "SELECT * FROM `".$config_databaseTablePrefix."jobs` ORDER BY filename";
  }
  else
  {
    $sql = "SELECT * FROM `".$config_databaseTablePrefix."jobs` WHERE filename='".database_safe($filename)."'";
  }

  if (database_querySelect($sql,$jobs))
  {
    foreach($jobs as $job)
    {
      fetch();
    }
  }

  exit();
?>