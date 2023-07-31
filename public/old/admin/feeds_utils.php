<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  require("admin_header.php");

  print "<h2>".translate("Feed Utilities")."</h2>";

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` ORDER BY filename";

  if (database_querySelect($sql,$feeds))
  {
    print "<table>";

    foreach($feeds as $feed)
    {
      print "<tr>";

      print "<th class='pta_key'>".$feed["filename"]."</th>";

      print "<td class='pta_tools'>";

      admin_tool("Parse Analysis","feeds_utils_parse.php?filename=".urlencode($feed["filename"]),TRUE,FALSE);

      admin_tool("Imported Analysis","feeds_utils_imported.php?filename=".urlencode($feed["filename"]),TRUE,FALSE);

      print "</td>";

      print "</tr>";
    }

    print "</table>";
  }
  else
  {
    print "<p>There are no feeds to display.</p>";
  }

  require("admin_footer.php");
?>