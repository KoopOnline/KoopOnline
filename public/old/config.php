<?php

function count_($array) {
    return is_array($array) ? count($array) : 0;
}

	// Load DotEnvironment Class
	require_once('class.environment.php');
	$__DotEnvironment = new DotEnvironment(realpath(__DIR__."/../../.env"));

	  $config_title = "Koop Online | De nummer één prijsvergelijker!";

	  $config_charset = "utf-8";

	  $config_baseHREF = "/old/";

	  $config_useRewrite = TRUE;

	  $config_useRelated = TRUE;

	  $config_useTracking = TRUE;

	  $config_useJavaScript = TRUE;

	  $config_useInteraction = FALSE;

	  $config_currencyHTML = "&euro;";

	  $config_resultsPerPage = 10;

	  $config_databaseServer = $_ENV["DB_HOST"];

	  $config_databaseUsername = $_ENV["DB_USERNAME"];

	  $config_databasePassword = $_ENV["DB_PASSWORD"];

	  $config_databaseName = $_ENV["DB_DATABASE"];

	  $config_databaseTablePrefix = "pt_";
?>
