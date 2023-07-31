<?php

print "niet gebruiken";
exit;

  header("Content-Type: text/plain;charset=utf-8");

  print "Working...\n";

	set_time_limit(0);

  require("../includes/common.php");

  if (!isset($config_uidField)) die("No \$config_uidField set in config.advanced.php");

  $link1 = mysqli_connect($config_databaseServer,$config_databaseUsername,$config_databasePassword,$config_databaseName);

  $sql1 = "
    DROP TABLE IF EXISTS `".$config_databaseTablePrefix."uidfix`;
    ";

  mysqli_query($link1,$sql1);

  $sql1 = "
    CREATE TABLE `".$config_databaseTablePrefix."uidfix`
    (
    id INT(11) NOT NULL auto_increment,

    `uid` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,

    PRIMARY KEY (id)
    )
    ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
    ";

  mysqli_query($link1,$sql1);

  $sql1 = "CREATE INDEX ".$config_uidField." ON `".$config_databaseTablePrefix."products` (".$config_uidField.")";

  mysqli_query($link1,$sql1);

  print "Done.\n";
?>