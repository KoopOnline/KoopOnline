<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("admin_header.php");

  print "<h2>".translate("Automation Tool")."</h2>";

  print "<h3>".translate("Current Jobs")."</h3>";

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."jobs` ORDER BY directory,filename";

  if (database_querySelect($sql,$rows))
  {
    print "<table>";

    print "<tr>";

    print "<td>&nbsp;</td>";

    print "<td>&nbsp;</td>";

    print "<td>&nbsp;</td>";

    print "<th>".translate("Last OK Run")."</th>";

    print "<th>".translate("Status")."</th>";

    print "</tr>";

    foreach($rows as $job)
    {
      print "<tr>";

      $directoryVar = "config_".$job["directory"]."Directory";

      print "<th class='pta_key'>".str_replace("../","",$$directoryVar)."</th>";

      print "<th class='pta_key'>".$job["filename"]."</th>";

      print "<td class='pta_tools'>";

      admin_tool("Edit","automation_tool_edit.php?id=".$job["id"],TRUE,FALSE);

      admin_tool("Run","automation_tool_run.php?id=".$job["id"],TRUE,($job["status"]!="OK"));

      admin_tool("Delete","automation_tool_delete.php?id=".$job["id"],TRUE,FALSE);

      print "</td>";

      print "<td class='pta_mid'>".($job["lastrun"]?date("Y-m-d H:i:s",$job["lastrun"]):"-")."</td>";

      print "<td class='pta_mid' style='font-size:x-small; color:".($job["status"]=="OK"?"darkgreen":"red").";'>".($job["status"]?$job["status"]:"-")."</td>";

      print "</tr>";
    }

    print "</table>";
  }
  else
  {
    print "<p>".translate("There are no jobs to display.")."</p>";
  }

  print "<p class='pta_to'>";

  admin_tool("New Job","automation_tool_edit.php?id=0",TRUE,FALSE);

  print "</p>";

  require("admin_footer.php");
?>