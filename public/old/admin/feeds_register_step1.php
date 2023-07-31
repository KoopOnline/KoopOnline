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

  require("admin_header.php");

  print "<h2>".translate("Admin")."</h2>";

  $formatDetected = MagicParser_getFormat($config_feedDirectory.$filename);

  print "<h3>".translate("Register")." (".$filename.") ".translate("Step 1 - Feed Format")."</h3>";

  widget_formBegin("GET","feeds_register_step2.php");

  print "<input type='hidden' name='filename' value='".htmlspecialchars($filename,ENT_QUOTES,$config_charset)."' />";

  print "<label><input type='radio' name='useFormat' value='formatDetected' checked='checked' />&nbsp;&nbsp;".translate("Use or modify autodetected XML or CSV format")."</label>";

  widget_textBox("","formatDetected",FALSE,$formatDetected,"",6);

  $optionsFormatSelected[""] = translate("Select Format")."...";

  $optionsFormatSelected["csv|44|1|0"] = "Text - Header Row - Comma Separated";

  $optionsFormatSelected["csv|124|1|0"] = "Text - Header Row - Pipe Separated";

  $optionsFormatSelected["csv|9|1|0"] = "Text - Header Row - Tab Separated";

  $optionsFormatSelected["csv|59|1|0"] = "Text - Header Row - Semicolon Separated";

  $optionsFormatSelected["csv|44|0|0"] = "Text - No Header Row - Comma Separated";

  $optionsFormatSelected["csv|124|0|0"] = "Text - No Header Row - Pipe Separated";

  $optionsFormatSelected["csv|9|0|0"] = "Text - No Header Row - Tab Separated";

  $optionsFormatSelected["csv|59|0|0"] = "Text - No Header Row - Semicolon Separated";

  $optionsFormatSelected["csv|44|1|34"] = "Quoted Text - Header Row - Comma Separated";

  $optionsFormatSelected["csv|124|1|34"] = "Quoted Text - Header Row - Pipe Separated";

  $optionsFormatSelected["csv|9|1|34"] = "Quoted Text - Header Row - Tab Separated";

  $optionsFormatSelected["csv|59|1|34"] = "Quoted Text - Header Row - Semicolon Separated";

  $optionsFormatSelected["csv|44|0|34"] = "Quoted Text - No Header Row - Comma Separated";

  $optionsFormatSelected["csv|124|0|34"] = "Quoted Text - No Header Row - Pipe Separated";

  $optionsFormatSelected["csv|9|0|34"] = "Quoted Text - No Header Row - Tab Separated";

  $optionsFormatSelected["csv|59|0|34"] = "Quoted Text - No Header Row - Semicolon Separated";

  print "<label><input id='useFormat' type='radio' name='useFormat' value='formatSelected' />&nbsp;&nbsp;".translate("Use manually selected CSV format")."</label>";

  widget_selectArray("","formatSelected",FALSE,"",$optionsFormatSelected,6,"onchange='JavaScript:document.getElementById(\"useFormat\").checked=true;'");

  widget_formButtons(array("Next"=>TRUE));

  widget_formEnd();

  require("admin_footer.php");
?>