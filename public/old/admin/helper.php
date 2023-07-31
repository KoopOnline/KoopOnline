<?php
  if (!isset($helper))
  {
    require("../includes/common.php");
  }

  $helper_fields = array();

  foreach($config_fieldSet as $field => $label)
  {
    $helper_fields[$field] = array("label"=>$label." Search");
  }

  if (!isset($helper))
  {
    $field = $_GET["field"];

    if (!isset($helper_fields[$field])) exit();

    $q = (isset($_GET["q"])?$_GET["q"]:"");

    $sql = "SELECT DISTINCT(".$field.") FROM `".$config_databaseTablePrefix."products` WHERE ".$field." LIKE '%".database_safe($q)."%' ORDER BY ".$field." LIMIT 25";

    database_querySelect($sql,$rows);

    foreach($rows as $row)
    {
      print "<option value='".htmlspecialchars($row[$field],ENT_QUOTES,$config_charset)."'>".htmlspecialchars($row[$field],ENT_QUOTES,$config_charset)."</option>";
    }

    exit();
  }
  else
  {
    if (!isset($helper_fields[$helper["field"]])) exit();

    print "<div class='row collapse'>";

      print "<div class='small-2 columns'>";

        if (isset($helper["callbackKeyword"]))
        {
          print "<label>&nbsp;<br />";

          print "<button id='helper_addKeyword' name='helper_addKeyword' class='tiny radius' onclick='JavaScript:helper_addKeywordOnClick();' disabled='disabled'>&laquo;</button>";

          print "</label>";
        }
        else
        {
          print "&nbsp;";
        }

      print "</div>";

      print "<div class='small-10 columns'>";

        print "<form onsubmit='return false;' style='display:inline;'>";

        print "<div class='row collapse'>";

        print "<div class='small-10 columns'>";

          print "<label>".translate($helper_fields[$helper["field"]]["label"])."<br />";

          print "<input type='text' name='helper_q' id='helper_q' onkeyup='JavaScript:helper_qOnChange();' onchange='JavaScript:helper_qOnChange();' />";

          print "</label>";

        print "</div>";

        print "<div class='small-2 columns'>";

          print "<label>&nbsp;<br />";

          print "<button id='helper_submit' type='submit' class='button tiny postfix' onclick='JavaScript:helper_search();'>Search</button>";

          print "</label>";

        print "</div>";

        print "</form>";

      print "</div>";

    print "</div>";

    print "<div class='row collapse' style='clear:both;'>";

      print "<div class='small-2 columns'>";

        if (isset($helper["callbackExact"]))
        {
          print "<button id='helper_add' name='helper_add' class='tiny radius' onclick='JavaScript:helper_addOnClick();' disabled='disabled'>&laquo;</button>";
        }
        else
        {
          print "&nbsp;";
        }

      print "</div>";

      print "<div class='small-10 columns'>";

        print "<select id='helper_results' name='helper_results' size='7' multiple='multiple' style='width:100%;height:110px;' onchange='JavaScript:helper_resultsOnChange();'>";

        print "</select>";

      print "</div>";

    print "</div>";

  print "</div>";

  ?>

  <script type='text/JavaScript'>

  function helper_search()
  {
    $("#helper_submit").prop("disabled",true);

    $("#helper_add").prop("disabled",true);

    href = "helper.php?field=<?php print $helper["field"]; ?>&q="+encodeURIComponent($("#helper_q").val());

    $("#helper_results").load(href,function() {helper_searchDone(); });
  }

  function helper_searchDone()
  {
    $("#helper_submit").prop("disabled",false);
  }

  function helper_qOnChange()
  {
    <?php if (isset($helper["callbackKeyword"])): ?>

      if ($("#helper_q").val())
      {
        $("#helper_addKeyword").prop("disabled",false);
      }
      else
      {
        $("#helper_addKeyword").prop("disabled",true);
      }

    <?php endif; ?>
  }

  function helper_resultsOnChange()
  {
    if ($("#helper_results :selected").length)
    {
      $("#helper_add").prop("disabled",false);
    }
    else
    {
      $("#helper_add").prop("disabled",true);
    }
  }

  <?php if (isset($helper["callbackExact"])): ?>

    function helper_addOnClick()
    {
      $("#helper_results :selected").each(function() { <?php print $helper["callbackExact"]; ?>(this.value); });
    }

    $("#helper_results").dblclick( function() { helper_addOnClick() });

  <?php endif; ?>

  <?php if (isset($helper["callbackKeyword"])): ?>

    function helper_addKeywordOnClick()
    {
      <?php print $helper["callbackKeyword"]; ?>($("#helper_q").val());
    }

  <?php endif; ?>

  </script>

  <?php
  }
?>