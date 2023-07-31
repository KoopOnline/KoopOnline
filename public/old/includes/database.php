<?php
  function database_link()
  {
    global $config_databaseServer;

    global $config_databaseName;

    global $config_databaseUsername;

    global $config_databasePassword;

    global $database_link;

    if (!isset($database_link) || !mysqli_ping($database_link))
    {
      $database_link = mysqli_connect($config_databaseServer,$config_databaseUsername,$config_databasePassword,$config_databaseName);

      mysqli_set_charset($database_link,"utf8");

      mysqli_query($database_link,"SET SESSION sql_mode=''");
    }
    return $database_link;
  }

  function database_querySelect($sql,&$rows)
  {
    global $config_databaseDebugMode;

    $link = database_link();

    $result = mysqli_query($link,$sql);

    if (!$result && $config_databaseDebugMode)
    {
      print "[".$sql."][".mysqli_error($link)."]";
    }

    $rows = array();

    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
    {
      $rows[] = $row;
    }

    return mysqli_num_rows($result);
  }

  function database_queryModify($sql,&$insertId)
  {
    global $config_databaseDebugMode;

    $link = database_link();

    $result = mysqli_query($link,$sql);

    if (!$result && $config_databaseDebugMode)
    {
      print "[".$sql."][".mysqli_error($link)."]";
    }

    $insertId = mysqli_insert_id($link);

    return mysqli_affected_rows($link);
  }

  function database_safe($text)
  {
    $link = database_link();

    return mysqli_real_escape_string($link,$text);
  }
?>