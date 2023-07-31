<?php
  function javascript_focus($element)
  {
    global $config_useJavaScript;

    if ($config_useJavaScript)
    {
      return "<script type='text/javascript'>document.".$element.".focus();</script>";
    }
    else
    {
      return "";
    }
  }
  function javascript_statusBar($text)
  {
    global $config_useJavaScript;

    if ($config_useJavaScript)
    {
      return " onMouseOver='JavaScript:window.status=\"".$text."\";return true;' onMouseOut='JavaScript:window.status=\"\";return true;' ";
    }
    else
    {
      return "";
    }
  }
?>