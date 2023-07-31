<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $merchants = array();

  $sql = "SELECT DISTINCT(merchant) AS merchant FROM `".$config_databaseTablePrefix."products` ORDER BY merchant";

  if (database_querySelect($sql,$rows))
  {
    foreach($rows as $row)
    {
      $merchants[$row["merchant"]] = $row["merchant"];
    }
  }

  if (isset($_POST["action"]))
  {
    if ($_POST["action"] == "upload")
    {
      if (!isset($merchants[$_POST["merchant"]])) exit();

      if (move_uploaded_file($_FILES['image']['tmp_name'],"../logos/".$_POST["merchant"].$config_logoExtension))
      {
        header("Location: merchant_logos.php");

        exit();
      }
      else
      {
        widget_errorSet("image","file upload failed");
      }
    }
  }

  require("admin_header.php");

  print "<h2>".translate("Merchant Logos")."</h2>";

  print "<h3>".translate("Existing Logos")."</h3>";

  $existingLogos = FALSE;

  $sql = "SELECT DISTINCT(merchant) AS merchant FROM `".$config_databaseTablePrefix."products` ORDER BY merchant";

  if (database_querySelect($sql,$rows))
  {
    foreach($rows as $row)
    {
      $logoFilename = "../logos/".$row["merchant"].$config_logoExtension;

      if (file_exists($logoFilename))
      {
        if (!$existingLogos)
        {
          print "<table>";

          $existingLogos = TRUE;
        }

        print "<tr>";

        print "<th class='pta_key'>".$row["merchant"]."</th>";

        print "<td><img style='padding:10px;' src='".$config_baseHREF."logos/".str_replace(" ","%20",$row["merchant"]).$config_logoExtension."' alt='".htmlspecialchars($row["merchant"],ENT_QUOTES,$config_charset)." Logo' /></td>";

        print "</tr>";
      }
    }

    if ($existingLogos) print "</table>";
  }

  if (!$existingLogos)
  {
    print "<p>".translate("There are no logos to display.")."</p>";
  }

  print "<h3>".translate("New Logo")."</h3>";

  if (is_writable("../logos/"))
  {
    if (count($merchants))
    {
      print "<form method='post' enctype='multipart/form-data'>";

      widget_selectArray("Merchant","merchant",TRUE,"",$merchants);

      widget_file("Image Filename","image",TRUE);

      widget_formButtons(array("Upload"=>TRUE));

      widget_formHidden("action","upload");

      print "</form>";
    }
    else
    {
      print "<p>".translate("There are no merchants to display.")."</p>";
    }
  }
  else
  {
    print "<p>Your logos folder is not writable, but you can still upload logo files manually via FTP.</p>";
  }
  require("admin_footer.php");
?>