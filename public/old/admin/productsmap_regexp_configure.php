<?php
  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");

  $id = $_GET["id"];

  $sql = "SELECT * FROM `".$config_databaseTablePrefix."productsmap_regexp` WHERE id='".database_safe($id)."'";

  database_querySelect($sql,$rows);

  $productmap = $rows[0];

  $submit = (isset($_POST["submit"])?$_POST["submit"]:"");

  if ($submit == "Cancel")
  {
    header("Location: productsmap_regexp.php");

    exit();
  }

  if ($submit == "Save")
  {
    widget_validate("trigger_merchant",FALSE,"regexp");

    widget_validate("trigger_category",FALSE,"regexp");

    widget_validate("trigger_brand",FALSE,"regexp");

    widget_validate("regexp",TRUE,"regexp");

    widget_required("product_name");

    widget_validate("category",FALSE,"normalised");

    widget_validate("brand",FALSE,"normalised");

    if (!widget_errorcount_() && ($submit == translate("Save")))
    {
      $sql = "UPDATE `".$config_databaseTablePrefix."productsmap_regexp` SET

                `trigger_merchant` = '".database_safe($_POST["trigger_merchant"])."',
                `trigger_category` = '".database_safe($_POST["trigger_category"])."',
                `trigger_brand`    = '".database_safe($_POST["trigger_brand"])."',
                `regexp`           = '".database_safe($_POST["regexp"])."',
                `product_name`     = '".database_safe($_POST["product_name"])."',
                `category`         = '".database_safe($_POST["category"])."',
                `brand`            = '".database_safe($_POST["brand"])."'

                WHERE `id` ='".database_safe($id)."'";


      database_queryModify($sql,$insertId);

      header("Location: productsmap_regexp.php");

      exit();
    }
  }

  if ($submit)
  {
    foreach($_POST as $k => $v)
    {
      $productmap[$k] = $v;
    }
  }

  require("admin_header.php");

  print "<h2>".translate("Product Mapping RegExp")."</h2>";

  print "<h3>".translate("Configure")." (".$productmap["name"].")</h3>";

  if (($submit == translate("Test")) && !widget_errorcount_())
  {
    print "<div class='row'>";

      print "<div class='small-12 columns'>";

        print "<fieldset><legend>".translate("Test Results")."</legend>";

        $link = mysqli_connect($config_databaseServer,$config_databaseUsername,$config_databasePassword,$config_databaseName);

        mysqli_set_charset($link,"utf8");

        $j = strlen($productmap["regexp"]);

        $words = array();

        $word = "";

        for($i=0;$i<$j;$i++)
        {
          $c = substr($productmap["regexp"],$i,1);

          if (ctype_alnum($c))
          {
            $word .= $c;
          }
          else
          {
            if ($word)
            {
              $words[] = $word;

              $word = "";
            }
          }
        }

        $wheres = array();

        foreach($words as $word)
        {
          $wheres[] = "NAME LIKE '%".database_safe($word)."%' ";
        }

        $where = implode(" OR ",$wheres);

        $sql = "SELECT name,original_name,merchant,price,category,brand FROM `".$config_databaseTablePrefix."products` WHERE ".$where;

        mysqli_real_query($link,$sql);

        $result = mysqli_use_result($link);

        $products = array();

        while($importRecord = mysqli_fetch_assoc($result))
        {
          $apply =
            (
              preg_match(($productmap["trigger_merchant"]?$productmap["trigger_merchant"]:"/.*/"),$importRecord["merchant"])
              &
              preg_match(($productmap["trigger_category"]?$productmap["trigger_category"]:"/.*/"),$importRecord["category"])
              &
              preg_match(($productmap["trigger_brand"]?$productmap["trigger_brand"]:"/.*/"),$importRecord["brand"])
            );

          if ($apply)
          {
            preg_match($productmap["regexp"],$importRecord["original_name"],$matches);

            if (count_($matches))
            {
              $importRecord["name"] = $productmap["product_name"];

              foreach($matches as $k => $match)
              {
                $importRecord["name"] = str_replace("\$".$k,$match,$importRecord["name"]);
              }

              $products[] = $importRecord;
            }
          }
        }

        function cmp($a,$b)
        {
          if ($a["name"] == $b["name"])
          {
            if ($a["price"] == $b["price"])
            {
              return 0;
            }
            else
            {
              return ($a["price"] < $b["price"]) ? -1 : 1;
            }
          }
          else
          {
            return strcmp($a["name"],$b["name"]);
          }
        }

        if (count_($products))
        {
          usort($products,"cmp");

          print "<table width='100%' style='border:0;'>";

          print "<tr>";

          print "<th class='pta_txt'>".translate("Product Name")."</th>";

          print "<th class='pta_txt'>".translate("Merchant")."</th>";

          print "<th class='pta_txt'>".translate("Catalogue Product Name")."</th>";

          print "<th class='pta_num'>".translate("Price")."</th>";

          print "</tr>";

          $lastProductName = "";

          $productName = "";

          $bg = 0;

          foreach($products as $k => $product)
          {
            if ($product["name"]!=$lastProductName)
            {
              $productName = $product["name"];

              $bg++;
            }
            else
            {
              $productName = "&nbsp;";
            }

            print "<tr class='pta_bg".($bg%2)."'>";

            print "<th class='pta_key'>".$productName."</td>";

            print "<td>".$product["merchant"]."</td>";

            print "<td>".$product["original_name"]."</td>";

            print "<td class='pta_num'>".tapestry_price($product["price"])."</td>";

            print "</tr>";

            $lastProductName = $product["name"];
          }

          print "</table>";
        }
        else
        {
          print  translate("There are no products to display.");
        }

        print "</fieldset>";

      print "</div>";

    print "</div>";
  }

  widget_formBegin("POST","","f");

  print "<div class='row'>";

    print "<div class='small-6 columns'>";

      print "<fieldset><legend>".translate("Trigger")."</legend>";

        widget_textBox("Merchant","trigger_merchant",FALSE,$productmap["trigger_merchant"],"RegExp",12);

        widget_textBox("Category","trigger_category",FALSE,$productmap["trigger_category"],"RegExp",12);

        widget_textBox("Brand","trigger_brand",FALSE,$productmap["trigger_brand"],"RegExp",12);

      print "</fieldset>";

    print "</div>";

    print "<div class='small-6 columns'>";

      print "<fieldset><legend>".translate("Settings")."</legend>";

        widget_textBox("RegExp","regexp",TRUE,$productmap["regexp"],"RegExp",12);

        widget_textBox("Product Name","product_name",TRUE,$productmap["product_name"],"",12);

        widget_textBox("Custom Category","category",FALSE,$productmap["category"],"",12);

        widget_textBox("Custom Brand","brand",FALSE,$productmap["brand"],"",12);

      print "</fieldset>";

    print "</div>";

  print "</div>";

  widget_formButtons(array(translate("Save")=>TRUE,translate("Test")=>TRUE),"productsmap_regexp.php");

  widget_formEnd();

  require("admin_footer.php");
?>
