<?php
  require("../includes/common.php");

  require("../includes/admin.php");

  require("../includes/widget.php");

  $remoteAddr = (isset($_SERVER["REMOTE_ADDR"])?$_SERVER["REMOTE_ADDR"]:"");

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  if ($submit == "Login")
  {
    if ($_POST["password"] == $config_adminPassword)
    {
      setcookie("admin",md5($config_adminPassword));

      header("Location: ".$config_baseHREF."admin/");

      exit();
    }
    else
    {
      widget_errorSet("password","authentication failed");
    }
  }

  require("admin_header.php");

  print "<h2>".translate("Login")."</h2>";

  widget_formBegin();

  widget_passwordBox("Password","password",TRUE,"","",3);

  widget_formButtons(array("Login"=>TRUE));

  widget_formEnd();

  require("admin_footer.php");
?>