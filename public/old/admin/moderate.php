<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  if ($submit)
  {
    if ($submit == "Accept")
    {
      $sql = "UPDATE `".$config_databaseTablePrefix."reviews` SET comments='".database_safe($_POST["comments"])."',approved='".time()."' WHERE id='".$_POST["id"]."'";
    }
    else
    {
      $sql = "DELETE FROM `".$config_databaseTablePrefix."reviews` WHERE id='".$_POST["id"]."'";
    }

    database_queryModify($sql,$insertId);

    if ($_POST["numPending"] == 1)
    {
      admin_importReviews();
    }

    header("Location: moderate.php");

    exit();
  }

  require("admin_header.php");

  $sql = "SELECT COUNT(*) as numPending FROM `".$config_databaseTablePrefix."reviews` WHERE approved='0'";

  database_querySelect($sql,$rows);

  $numPending = $rows[0]["numPending"];

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."reviews` WHERE approved='0' ORDER BY created LIMIT 1";

  print "<h2>".translate("Moderate Reviews")."</h2>";

  if (database_querySelect($sql,$rows))
  {
    print "<h3>".translate("Reviews Pending Approval")." (1 ".translate("of")." ".$numPending.")</h3>";

    $review = $rows[0];

    print "<table>";

    print "<tr>";

    print "<th class='pta_key'>Product Name</th>";

    print "<td>".$review["product_name"]."</td>";

    print "</tr>";

    print "<tr>";

    print "<th class='pta_key'>".translate("Rating")."</th>";

    print "<td>".tapestry_stars($review["rating"],"")."</td>";

    print "</table>";

    print "<br />";

    widget_formBegin();

    widget_formHidden("id",$review["id"]);

    widget_formHidden("numPending",$numPending);

    widget_textArea("Comments","comments",FALSE,$review["comments"],200,6);

    widget_formButtons(array("Accept"=>TRUE,"Reject"=>TRUE));

    widget_formEnd();
  }
  else
  {
    print "<p>".translate("There are no reviews pending approval.")."</p>";
  }

  require("admin_footer.php");
?>