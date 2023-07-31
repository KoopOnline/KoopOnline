<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $filename = (isset($_GET["filename"])?$_GET["filename"]:"");

  if (!preg_match($config_filenameRegExp,$filename))
  {
    die("Invalid filename");
  }

  $name = (isset($_GET["name"])?base64_decode($_GET["name"]):"");

  $index = (isset($_GET["index"])?$_GET["index"]:"");

  $q = (isset($_GET["q"])?$_GET["q"]:"");

  $page = (isset($_GET["page"])?intval($_GET["page"]):1);

  $sort = (isset($_GET["sort"])?$_GET["sort"]:"name");

  $filter = (isset($_GET["filter"])?$_GET["filter"]:array());

  $filenameWhere = "filename='".database_safe($filename)."'";

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE ".$filenameWhere;

  if (!database_querySelect($sql,$feeds))
  {
    die("Invalid filename");
  }

  if ($index)
  {
    if (($index != "merchant") && (!isset($config_fieldSet[$index])))
    {
      die("Invalid index");
    }
  }

  if (($sort != "merchant") && (!isset($config_fieldSet[$sort])))
  {
    die("Invalid sort");
  }

  $feed = $feeds[0];

  $fieldSet = array("merchant"=>"Merchant") + $config_fieldSet;

  unset($fieldSet["name"]);

  unset($fieldSet["description"]);

  unset($fieldSet["image_url"]);

  unset($fieldSet["buy_url"]);

  unset($fieldSet["price"]);

  if (!$feed["field_category"] && !$feed["user_category"])
  {
    unset($fieldSet["category"]);
  }

  if (!$feed["field_brand"] && !$feed["user_brand"])
  {
    unset($fieldSet["brand"]);
  }

  $fieldSetCopy = $fieldSet;

  foreach($fieldSetCopy as $k => $v)
  {
    if (($k=="merchant") || ($k=="category") || ($k=="brand")) continue;

    if (!$feed["field_".$k])
    {
      unset($fieldSet[$k]);
    }
  }


  require("admin_header.php");

  print "<h2>".translate("Feed Utilities")."</h2>";

  print "<h3>".translate("Imported Analysis")." (".$filename.")</h3>";

  if ($name)
  {
    print "<h3>".translate("View Record")."</h3>";

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."products` WHERE filename='".database_safe($filename)."' AND name='".database_safe($name)."'";

    database_querySelect($sql,$products);

    $product = $products[0];

    print "<table style='width:100%'>";

    print "<tr>";

    print "<th style='width:25%;'>".translate("Field")."</th>";

    print "<th>&nbsp;</th>";

    print "</tr>";

    print "<tbody>";

    foreach($product as $k => $v)
    {
      print "<tr>";

      print "<th class='pta_key'>".widget_safe($k)."</th>";

      print "<td>";

      if ($v)
      {
        switch($k)
        {
          case "category":

            if ($config_useCategoryHierarchy)
            {
              print widget_safe($v);
            }
            else
            {
              print "<a target='_BLANK' href='".$config_baseHREF."search.php?q=".$k.":".urlencode($v)."'>".$v." <img border='0' width='16' height='16' src='newwindow.png' /></a>";
            }

            break;

          case "merchant":

          case "brand":

            print "<a target='_BLANK' href='".$config_baseHREF."search.php?q=".$k.":".urlencode($v)."'>".$v." <img border='0' width='16' height='16' src='newwindow.png' /></a>";

            break;

          case "name";

            print "<a target='_BLANK' href='".tapestry_productHREF($product)."'>".$v." <img border='0' width='16' height='16' src='newwindow.png' /></a>";

            break;

          case "image_url":

            print "<img class='pta_img' src='".htmlentities($v,ENT_QUOTES,$config_charset)."' />";

            break;

          case "buy_url":

            print "<a target='_BLANK' href='".$v."'>".$v." <img border='0' width='16' height='16' src='newwindow.png' /></a>";

            break;

          default:

            print widget_safe($v);

            break;
        }
      }
      else
      {
        print "&nbsp;";
      }

      print "</td>";

      print "</tr>";
    }

    print "</tbody>";

    print "</table>";
  }
  elseif($index)
  {
    print "<table>";

    print "<tr><th>".$fieldSet[$index]."</th></tr>";

    $sql = "SELECT DISTINCT(".$index.") FROM `".$config_databaseTablePrefix."products` WHERE ".$filenameWhere." AND ".$index." <> '' ORDER BY ".$index;

    database_querySelect($sql,$products);

    foreach($products as $product)
    {
      print "<tr><td><a href='?filename=".urlencode($filename)."&filter[".urlencode($index)."]=".urlencode($product[$index])."'>".$product[$index]."</a></td></tr>";
    }

    print "</table>";
  }
  else
  {
    widget_formBegin("GET","feeds_utils_imported.php");

    widget_formHidden("filename",$filename);

    widget_formHidden("page","1");

    $where = $filenameWhere;

    if ($q)
    {
      $where .= " AND name LIKE '%".database_safe($q)."%'";
    }

    foreach($filter as $field => $value)
    {
      if (!isset($fieldSet[$field])) continue;

      if (!$value) continue;

      $where .= " AND `".$field."` = '".database_safe($value)."'";
    }

    $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM `".$config_databaseTablePrefix."products` WHERE ".$where;

    $sql .= " ORDER BY ".$sort;

    $offset = ($page-1) * $config_resultsPerPage;

    $sql .= " LIMIT ".$offset.",".$config_resultsPerPage;

    $numRows = database_querySelect($sql,$products);

    $sqlResultCount = "SELECT FOUND_ROWS() as resultcount";

    database_querySelect($sqlResultCount,$rowsResultCount);

    $resultCount = $rowsResultCount[0]["resultcount"];

    print "<table width='100%'>";

    print "<tr>";

    print "<td>";

    if ($resultCount)
    {
      $resultFrom = ($offset+1);

      $resultTo = ($offset+$config_resultsPerPage);

      if ($resultTo > $resultCount) $resultTo = $resultCount;

      print "<strong>".$resultFrom."</strong> ".translate("to")." <strong>".$resultTo."</strong> ".translate("of")." <strong>".$resultCount."</strong>";
    }
    else
    {
      print translate("no results found");
    }

    print " | ";

    print "<a href='?filename=".urlencode($filename)."'>Reset</a>";

    print "</t>";

    $width = intval(55 / count($fieldSet));

    foreach($fieldSet as $k => $v)
    {
      print "<th width='".$width."%'><a href='?filename=".urlencode($filename)."&index=".urlencode($k)."'>".$v."</a></th>";
    }

    print "</tr>";

    print "<tr>";

    print "<td>";

    print "<div class='row collapse'>";

    print "<div class='small-10 columns'>";

    print "<input type='text' class='pta_inline' name='q' value='".widget_safe($q)."' />";

    print "</div>";

    print "<div class='small-2 columns'>";

    print "<button class='button tiny postfix pta_inline'>".translate("Search")."</button>";

    print "</div>";

    print "</div>";

    print "</td>";

    foreach($fieldSet as $k => $v)
    {
      print "<td>";

      $sql1 = "SELECT DISTINCT(".$k.") FROM `".$config_databaseTablePrefix."products` WHERE ".$where." AND ".$k." <> '' ORDER BY ".$k;

      if (database_querySelect($sql1,$rows1))
      {
        print "<div class='small-12 columns'>";

        print "<select class='pta_inline' name='filter[".$k."]' onchange='JavaScript:this.form.submit();'>";

        print "<option value=''>All</option>";

        foreach($rows1 as $row)
        {
          $selected = (isset($filter[$k]) && ($filter[$k]==$row[$k])?"selected='selected'":"");

          print "<option value='".htmlspecialchars($row[$k],ENT_QUOTES,$config_charset)."' ".$selected.">".$row[$k]."</option>";
        }

        print "</select>";

        print "</div>";

        print "</td>";
      }
    }

    print "</tr>";

    if ($numRows)
    {
      foreach($products as $product)
      {
        print "<tr>";

        print "<td><strong><a href='?filename=".urlencode($filename)."&name=".base64_encode($product["name"])."'>".$product["name"]."</a></strong></td>";

        foreach($fieldSet as $k => $v)
        {
          if ($product[$k])
          {
            print "<td>".$product[$k]."</td>";
          }
          else
          {
            print "<td>&nbsp;</td>";
          }
        }

        print "</tr>";
      }
    }

    print "</table>";

    widget_formEnd();

    if ($numRows)
    {
      $rewrite = FALSE;

      $navigation["resultCount"] = $resultCount;

      $sort .= "&filename=".urlencode($filename);

      foreach($fieldSet as $k => $v)
      {
        if (isset($filter[$k]))
        {
          $sort .= "&filter[".urlencode($k)."]=".urlencode($filter[$k]);
        }
      }

      print "<br />";

      if ($navigation["resultCount"] > $config_resultsPerPage)
      {
        navigation_display($navigation);
      }
    }
  }

  function navigation_display($navigation)
  {
    global $config_resultsPerPage;

    global $rewrite;

    global $page;

    global $sort;

    global $q;

    $totalPages = ceil($navigation["resultCount"] / $config_resultsPerPage);

    print "<div class='pagination-centered'>";

    print "<ul class='pagination'>";

    if ($page > 1)
    {
      $prevPage = ($page-1);

      if ($rewrite)
      {
        if ($prevPage == 1)
        {
          $prevHREF = "./";
        }
        else
        {
          $prevHREF = $prevPage.".html";
        }
      }
      else
      {
        $prevHREF = "?q=".urlencode($q)."&amp;page=".$prevPage."&amp;sort=".$sort;
      }

      print "<li class='arrow'><a href='".$prevHREF."'>&laquo;</a></li>";
    }
    else
    {
      print "<li class='arrow unavailable'><a href=''>&laquo;</a></li>";
    }

    if ($page < 5)
    {
      $pageFrom = 1;

      $pageTo = 9;
    }
    else
    {
      $pageFrom = ($page - 4);

      $pageTo = ($page + 4);
    }

    if ($pageTo > $totalPages)
    {
      $pageTo = $totalPages;

      $pageFrom = $totalPages - 8;
    }

    if ($pageFrom <= 1)
    {
      $pageFrom = 1;
    }
    else
    {
      if ($rewrite)
      {
        $pageOneHREF = "./";
      }
      else
      {
        $pageOneHREF = "?q=".urlencode($q)."&amp;page=1&amp;sort=".$sort;
      }

      print "<li><a href='".$pageOneHREF."'>1</a></li>";

      print "<li class='unavailable'><a href=''>&hellip;</a></li>";
    }

    for($i=$pageFrom;$i<=$pageTo;$i++)
    {
      if ($rewrite)
      {
        if ($i==1)
        {
          $pageHREF = "./";
        }
        else
        {
          $pageHREF = $i.".html";
        }
      }
      else
      {
        $pageHREF = "?q=".urlencode($q)."&amp;page=".$i."&amp;sort=".$sort;
      }
      if ($page <> $i)
      {
        print "<li><a href='".$pageHREF."'>".$i."</a></li>";
      }
      else
      {
        print "<li class='unavailable current'><a href='".$pageHREF."'>".$i."</a></li>";
      }
    }

    if ($page < $totalPages)
    {
      $nextPage = ($page+1);

      if ($rewrite)
      {
        $nextHREF = $nextPage.".html";
      }
      else
      {
        $nextHREF = "?q=".urlencode($q)."&amp;page=".$nextPage."&amp;sort=".$sort;
      }

      print "<li class='arrow'><a href='".$nextHREF."'>&raquo;</a></li>";
    }
    else
    {
      print "<li class='arrow unavailable'><a href=''>&raquo;</a></li>";
    }

    print "</ul>";

    print "</div>";
  }

  require("admin_footer.php");
?>