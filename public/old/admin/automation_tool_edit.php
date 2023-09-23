<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $id = (isset($_GET["id"])?$_GET["id"]:"");

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  if ($submit == "Cancel")
  {
    header("Location: automation_tool.php");

    exit();
  }

  if (($submit == "Save") || ($submit == "Save and Run"))
  {
    widget_required("url");

    widget_validate("minsize",FALSE,"numeric");

    widget_validate("filename",TRUE,"filename");

    if (!widget_errorcount_())
    {
      if ($id)
      {
        $sql = "UPDATE `".$config_databaseTablePrefix."jobs` SET ";
      }
      else
      {
        $sql = "INSERT INTO `".$config_databaseTablePrefix."jobs` SET ";
      }

      $sql .= sprintf("
                      directory = '%s',
                      filename = '%s',
                      url = '%s',
                      unzip = '%s',
                      minsize = '%s'
                      ",
                      database_safe($_POST["directory"]),
                      database_safe(trim($_POST["filename"])),
                      database_safe(trim($_POST["url"])),
                      database_safe($_POST["unzip"]),
                      database_safe($_POST["minsize"])
                      );

      if ($id)
      {
        $sql .= " WHERE id = '".$id."' ";
      }

      database_queryModify($sql,$insertId);

      if (!$id)
      {
        $id = $insertId;
      }

      if ($submit == "Save and Run")
      {
        header("Location: automation_tool_run.php?id=".$id);
      }
      else
      {
        header("Location: automation_tool.php");
      }

      exit();
    }
  }
  elseif($id)
  {
    $sql = "SELECT * FROM `".$config_databaseTablePrefix."jobs` WHERE id='".database_safe($id)."'";

    database_querySelect($sql,$jobs);

    foreach($jobs[0] as $k => $v)
    {
      $_POST[$k] = $v;
    }
  }

  require("admin_header.php");

  print "<h2>".translate("Automation Tool")."</h2>";

  if ($id)
  {
    print "<h3>".translate("Edit Job")."</h3>";
  }
  else
  {
    print "<h3>".translate("New Job")."</h3>";
  }

  widget_formBegin();

  $default = (isset($_POST["url"]) ? $_POST["url"] : "");

  widget_textBox("URL","url",TRUE,$default,"http://www.example.com/path/to/example.xml");

  widget_formIndentBegin();

  $default = (isset($_POST["unzip"]) ? $_POST["unzip"] : "0");

  widget_selectArray("Unzip?","unzip",TRUE,$default,array("0"=>"No","1"=>"Yes"));

  $default = (isset($_POST["minsize"]) ? $_POST["minsize"] : "");

  widget_textBox("Abort if less than","minsize",FALSE,$default,"bytes",3);

  widget_formIndentEnd();

  print "<div class='row'>";

  print "<div class='medium-6 columns'>";

  print "<div class='row collapse'>";

  print "<label for='filename' ".(isset($widget_errors["filename"])?"class='error'":"").">Save as...</label>";

  print "<div class='medium-4 columns'>";

  print "<span class='prefix' style='background-color:#fff;padding:0px;'>";

  $default = (isset($_POST["directory"]) ? $_POST["directory"] : "");

  print "<select name='directory' style='border:0;'>";

  print "<option value='feed' ".($default=="feed"?"selected='selected'":"")." >".str_replace("../","",$config_feedDirectory)."</option>";

  print "<option value='voucherCodesFeed' ".($default=="voucherCodesFeed"?"selected='selected'":"")." >".str_replace("../","",$config_voucherCodesFeedDirectory)."</option>";

  print "</select>";

  print "</span>";

  print "</div>";

  print "<div class='medium-8 columns'>";

  $default = (isset($_POST["filename"]) ? $_POST["filename"] : "");

  print "<input id='filename' name='filename' type='text' value='".htmlspecialchars($default,ENT_QUOTES,$config_charset)."' placeholder='Use only 0-9 A-Z a-z _ - . ending .xml or .csv' />";

  print "</div>";

  print "</div>";

  print "</div>";

  print "</div>";

  widget_formButtons(array("Save"=>TRUE,"Save and Run"=>TRUE),"automation_tool.php");

  widget_formEnd();

  require("admin_footer.php");
?>
