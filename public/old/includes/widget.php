<?php
  $widget_errors = array();

  function widget_safe($text)
  {
    global $config_charset;

    return htmlspecialchars($text,ENT_QUOTES,$config_charset);
  }

  function widget_label($label,$name,$required,$inline=FALSE)
  {
    global $widget_errors;

    if (isset($widget_errors[$name]))
    {
      print "<span data-tooltip class='has-tip' title='".widget_safe($widget_errors[$name])."'>";
    }

    print "<label for='".$name."' class='".(isset($widget_errors[$name])?"error":"")." ".(!$required?"pta_optional":"")." ".($inline?"right inline":"")."'>".translate($label)."</label>";

    if (isset($widget_errors[$name]))
    {
      print "</span>";
    }
  }

  function widget_formBegin($method="POST",$action="",$id="")
  {
    print "<form method='".$method."' ".($action?"action='".$action."'":"")." ".($id?"id='".$id."'":"").">";
  }

  function widget_formEnd()
  {
    print "</form>";
  }

  function widget_formHidden($name,$value)
  {
    global $config_charset;

    print "<input type='hidden' id='".$name."' name='".$name."' value='".widget_safe($value)."' />";
  }

  function widget_formButtons($buttons,$cancel="")
  {
    print "<p class='pta_to'>";

    foreach($buttons as $value => $highlight)
    {
      print "<button type='submit' name='submit' class='pta_button small radius ".($highlight?"success":"secondary")."' value='".$value."'>".translate($value)."</button>";
    }

    if ($cancel)
    {
      print "<a class='button small radius secondary' href='".$cancel."'>".translate("Cancel")."</a>";
    }

    print "</p>";
  }

  function widget_textArea($label,$name,$required,$default,$height,$columns=6)
  {
    global $config_charset;

    global $widget_errors;

    print "<div class='row'>";

    print "<div class='medium-".$columns." columns'>";

    widget_label($label,$name,$required);

    print "<textarea wrap='off' style='width:100%;height:".$height."px' id='".$name."' name='".$name."' ".($required?"required='required'":"").">".widget_safe($default)."</textarea>";

    print "</div>";

    print "</div>";
  }

  function widget_textBox($label,$name,$required,$default,$placeholder="",$columns=6,$atts="")
  {
    global $config_charset;

    print "<div class='row'>";

    print "<div class='medium-".$columns." columns'>";

    widget_label($label,$name,$required);

    print "<input id='".$name."' name='".$name."' type='text' value='".widget_safe($default)."' ".($required?"required='required'":"")." ".($placeholder?"placeholder='".$placeholder."'":"")." ".$atts." />";

    print "</div>";

    print "</div>";
  }

  function widget_passwordBox($label,$name,$required,$default,$placeholder="",$columns=6)
  {
    global $config_charset;

    print "<div class='row'>";

    print "<div class='medium-".$columns." columns'>";

    widget_label($label,$name,$required);

    print "<input id='".$name."' name='".$name."' type='password' value='".widget_safe($default)."' ".($required?"required='required'":"")." ".($placeholder?"placeholder='".$placeholder."'":"")." />";

    print "</div>";

    print "</div>";
  }

  function widget_checkBox($label,$name,$required,$checked,$columns=6)
  {
    global $config_charset;

    print "<div class='row'>";

    print "<div class='medium-".$columns." columns'>";

    print "<input type='checkbox' id='".$name."' name='".$name."' ".($checked?"checked='checked'":"")." />";

    widget_label($label,$name,$required);

    print "</div>";

    print "</div>";
  }

  function widget_file($label,$name,$required)
  {
    print "<div class='row'>";

    print "<div class='medium-12 columns'>";

    widget_label($label,$name,$required);

    print "<div style='display:none;'><input type='file' name='".$name."' id='".$name."' onchange='JavaScript:document.getElementById(\"".$name."_browse\").innerHTML = this.value;' /></div>";

    print "<button class='tiny radius secondary' id='".$name."_browse' onclick='JavaScript:document.getElementById(\"".$name."\").click();return false;'>Browse...</button>";

    print "</div>";

    print "</div>";
  }

  function widget_selectArray($label,$name,$required,$default,$options,$columns=3,$atts="")
  {
    global $widget_errors;

    print "<div class='row'>";

    print "<div class='medium-".$columns." columns'>";

    widget_label($label,$name,$required);

    print "<select id='".$name."' name='".$name."' ".($required?"required='required'":"")." ".$atts.">";

    foreach($options as $value => $label)
    {
      print "<option value='".$value."' ".($value==$default?"selected='selected'":"").">".$label."</option>";
    }

    print "</select>";

    print "</div>";

    print "</div>";
  }

  function widget_date($label,$name,$default,$null_option)
  {
    if ($default)
    {
      $d_default = date("j",$default);

      $m_default = date("n",$default);

      $y_default = date("Y",$default);
    }
    else
    {
      $d_default = "";

      $m_default = "";

      $y_default = "";
    }
    print "<div class='row'>";

    print "<div class='medium-12 columns'>";

    widget_label($label,$name,TRUE);

    print "<select name='".$name."_d' style='width:75px;margin-right:2px;float:left;'>";

    if ($null_option) print "<option>".$null_option."</option>";

    for($i=1;$i<=31;$i++)
    {
      $selected = ($i==$d_default?"selected='selected'":"");

      print "<option value='".$i."' ".$selected.">".$i."</option>";
    }

    print "</select>";

    $m = array(1=>"Jan",2=>"Feb",3=>"Mar",4=>"Apr",5=>"May",6=>"Jun",7=>"Jul",8=>"Aug",9=>"Sep",10=>"Oct",11=>"Nov",12=>"Dec");

    print "<select name='".$name."_m' style='width:90px;margin-right:2px;float:left;'>";

    if ($null_option) print "<option>".$null_option."</option>";

    foreach($m as $k => $v)
    {
      $selected = ($k==$m_default?"selected='selected'":"");

      print "<option value='".$k."' ".$selected.">".$v."</option>";
    }
    print "</select>";

    print "<select name='".$name."_y' style='width:90px;float:left;'>";

    if ($null_option) print "<option>".$null_option."</option>";

    $y_from = ($y_default?$y_default:date("Y"));

    $y_to = $y_from + 10;

    for($i=$y_from;$i<=$y_to;$i++)
    {
      $selected = ($i==$y_default?"selected='selected'":"");

      print "<option value='".$i."' ".$selected.">".$i."</option>";
    }
    print "</select>";

    print "</div>";

    print "</div>";
  }

  function widget_formIndentBegin()
  {
    print "<div class='pta_sf'>";
  }

  function widget_formIndentEnd()
  {
    print "</div>";
  }

  function widget_errorSet($field,$error)
  {
    global $widget_errors;

    $widget_errors[$field] = $error;
  }

  function widget_errorGet($field)
  {
    global $widget_errors;

    if (isset($widget_errors[$field]))
    {
      widget_errorDisplay($widget_errors[$field]);
    }
  }

  function widget_errorCount()
  {
    global $widget_errors;

    return count($widget_errors);
  }

  function widget_required($postVar)
  {
    if (!isset($_POST[$postVar]))
    {
      widget_errorSet($postVar,"required field");
    }
    elseif(!$_POST[$postVar])
    {
      widget_errorSet($postVar,"required field");
    }
  }

  function widget_errorDisplay($text)
  {
    print "<span class='error'><nobr> &bull; ".$text."</nobr></span>";
  }

  function widget_validate($postVar,$required,$type)
  {
    global $config_filenameRegExp;

    if ($required && !$_POST[$postVar])
    {
      widget_errorSet($postVar,"required field");

      return;
    }
    elseif(!$_POST[$postVar])
    {
      return;
    }

    switch($type)
    {
      case "numeric":

        if (!is_numeric($_POST[$postVar]))
        {
          widget_errorSet($postVar,"numeric value required");
        }

        break;

      case "regexp":

        if (@preg_match($_POST[$postVar],NULL)===FALSE)
        {
          widget_errorSet($postVar,"invalid regexp");
        }

        break;

      case "normalised":

        if ($_POST[$postVar]!=tapestry_normalise($_POST[$postVar]))
        {
          widget_errorSet($postVar,"invalid characters");
        }

        break;

      case "filename":

        if (!preg_match($config_filenameRegExp,$_POST[$postVar]))
        {
          widget_errorSet($postVar,"invalid filename");
        }

        break;
    }
  }

  if (get_magic_quotes_gpc())
  {
    if (isset($_GET))
    {
      foreach($_GET as $k => $v)
      {
        if (is_array($_GET[$k]))
        {
          foreach($_GET[$k] as $k2 => $v2)
          {
            $_GET[$k][$k2] = stripslashes($v2);
          }
        }
        else
        {
          $_GET[$k] = stripslashes($v);
        }
      }
    }
    if (isset($_POST))
    {
      foreach($_POST as $k => $v)
      {
        if (is_array($_POST[$k]))
        {
          foreach($_POST[$k] as $k2 => $v2)
          {
            $_POST[$k][$k2] = stripslashes($v2);
          }
        }
        else
        {
          $_POST[$k] = stripslashes($v);
        }
      }
    }
  }
?>