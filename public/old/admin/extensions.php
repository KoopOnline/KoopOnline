<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $ns = (isset($_GET["ns"])?preg_replace('/[^A-Za-z0-9]/','',$_GET["ns"]):"");

  $pn = (isset($_GET["pn"])?preg_replace('/[^A-Za-z0-9]/','',$_GET["pn"]):"");

  $extensionMeta = "extensions/".$ns."/".$ns.".php";

  if (!file_exists($extensionMeta)) die("Extension meta data not found.");

  $extension = "extensions/".$ns."/".$pn.".php";

  if (!file_exists($extension)) die("Extension or handler not found.");

  function extension_done()
  {
    global $ns;

    global $pn;

    header("location: extensions.php?ns=".$ns."&pn=".$pn);

    exit();
  }

  require($extensionMeta);

  require($extension);

  if (isset($_POST) && count_($_POST)) extension_handler();

  $meta = $$ns;

  if (isset($meta["head"]))
  {
    $header["head"] = $meta["head"];
  }

  require("admin_header.php");

  extension_form();

  require("admin_footer.php");
?>
