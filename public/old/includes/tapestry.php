<?php
  function tapestry_search($text)
  {
    $text = str_replace(" ","",strtolower($text));

    return $text;
  }

  function tapestry_hyphenate($text)
  {
    $text = str_replace(" ","-",$text);

    return $text;
  }

  function tapestry_normalise($text,$allow = "")
  {
    global $config_charset;

    global $config_normaliseRegExp;

    $text = str_replace("-"," ",$text);

    $text = preg_replace('/[^'.$config_normaliseRegExp.$allow.']/','',$text);

    $text = preg_replace('/[ ]{2,}/',' ',$text);

    return trim($text);
  }

  function tapestry_decimalise($price)
  {
    if (strpos($price,","))
    {
      if (strpos($price,",") > strpos($price,"."))
      {
        $price = str_replace(".","",$price);

        $price = str_replace(",",".",$price);
      }
      elseif (strpos($price,".") > strpos($price,","))
      {
        $price = str_replace(",","",$price);
      }
      else
      {
        $price = str_replace(",",".",$price);
      }
    }

    $price = preg_replace('/[^0-9\.]/','',$price);

    $price = sprintf("%.2f",$price);

    return $price;
  }

  function tapestry_productHREF($product)
  {
    global $config_baseHREF;

    global $config_useRewrite;

    if ($config_useRewrite)
    {
      return $config_baseHREF."product/".urlencode(tapestry_hyphenate($product["normalised_name"])).".html";
    }
    else
    {
      return $config_baseHREF."products.php?q=".urlencode($product["normalised_name"]);
    }
  }

  function tapestry_reviewHREF($product)
  {
    global $config_baseHREF;

    global $config_useRewrite;

    if ($config_useRewrite)
    {
      return $config_baseHREF."review/".urlencode(tapestry_hyphenate($product["normalised_name"])).".html";
    }
    else
    {
      return $config_baseHREF."reviews.php?q=".urlencode($product["normalised_name"]);
    }
  }

  function tapestry_buyURL($product)
  {
    global $config_baseHREF;

    global $config_useTracking;

    if ($config_useTracking)
    {
      $retval = $config_baseHREF."jump.php?id=".$product["id"];
    }
    else
    {
      $retval = $product["buy_url"];
    }
	 return $retval."' target='_BLANK";
  }
  

  function tapestry_stars($stars,$imagePostfix)
  {
    global $config_baseHREF;

    $html = "<img src='".$config_baseHREF."images/".$stars.$imagePostfix.".gif' alt='".$stars." ".translate("Star Rating")."' />";

    return $html;
  }

  function tapestry_applyVoucherCodes($products)
  {
    global $tapestry_voucherCodes;

    global $config_databaseTablePrefix;

    $ins = array();

    foreach($products as $product)
    {
      if (!isset($tapestry_voucherCodes[$product["merchant"]]))
      {
        $ins[$product["merchant"]] = "'".database_safe($product["merchant"])."'";
      }
      if (count($ins))
      {
        $in = implode(",",$ins);

        $sql = "SELECT * FROM `".$config_databaseTablePrefix."vouchers` WHERE merchant IN (".$in.")";

        database_querySelect($sql,$vouchers);

        foreach($vouchers as $voucher)
        {
          $now = time();

          if ($voucher["valid_from"] > $now) continue;

          if ($voucher["valid_to"]) if ($voucher["valid_to"] < $now) continue;

          $tapestry_voucherCodes[$voucher["merchant"]][] = $voucher;
        }
      }
    }

    foreach($products as $k => $product)
    {
      if (isset($tapestry_voucherCodes[$product["merchant"]]))
      {
        $product["discount_price"] = $product["price"];

        foreach($tapestry_voucherCodes[$product["merchant"]] as $voucher)
        {
          $isValid = TRUE;

          if ($isValid)
          {
            if (
               ($voucher["min_price"])
               &&
               ($voucher["min_price"] > $product["price"])
               )
            {
              $isValid = FALSE;
            }
          }

          if ($isValid)
          {
            if ($voucher["match_value"])
            {
              $matched = FALSE;

              $match_values = explode(",",$voucher["match_value"]);

              foreach($match_values as $match_value)
              {
                switch($voucher["match_type"])
                {
                  case "exact":

                    if ($product[$voucher["match_field"]] == $match_value)
                    {
                      $matched = TRUE;
                    }

                    break;

                  case "keyword":

                    if (stripos($product[$voucher["match_field"]],$match_value) !== FALSE)
                    {
                      $matched = TRUE;
                    }

                    break;
                }
              }
              $isValid = $matched;
            }
          }

          if ($isValid)
          {
            if ($voucher["discount_type"]=="#")
            {
              $discountAmount = $voucher["discount_value"];

              $discountPrice = tapestry_decimalise($product["price"] - $discountAmount);
            }
            elseif ($voucher["discount_type"]=="%")
            {
              $discountAmount = $product["price"]*($voucher["discount_value"]/100);

              $discountPrice = tapestry_decimalise($product["price"] - $discountAmount);
            }
            elseif ($voucher["discount_type"]=="S")
            {
              $discountPrice = $product["price"];
            }

            if (
               ($discountPrice <= $product["discount_price"])
               )
            {
              $product["discount_price"] = $discountPrice;

              $product["voucher_code"] = $voucher["code"];

              if ($voucher["discount_text"])
              {
                $product["voucher_code"] .= " (".$voucher["discount_text"].")";
              }
            }
          }
        }

        if (isset($product["voucher_code"]))
        {
          $products[$k]["price"] = $product["discount_price"];

          $products[$k]["voucher_code"] = $product["voucher_code"];
        }
      }
    }
    return $products;
  }

  function tapestry_substr($text,$length,$append="")
  {
    if (strlen($text) > $length)
    {
      $breakOffset = strpos($text," ",$length);

      if ($breakOffset !== FALSE)
      {
        $text = substr($text,0,$breakOffset).$append;
      }
    }
    return $text;
  }

  function tapestry_mb_substr($text,$pos,$len)
  {
    global $config_charset;

    return (function_exists("mb_substr")?mb_substr($text,$pos,$len,$config_charset):substr($text,$pos,$len));
  }

  function tapestry_mb_strtoupper($text)
  {
    global $config_charset;

    return (function_exists("mb_strtoupper")?mb_strtoupper($text,$config_charset):strtoupper($text));
  }

  function tapestry_mb_strtolower($text)
  {
    global $config_charset;

    return (function_exists("mb_strtolower")?mb_strtolower($text,$config_charset):strtolower($text));
  }

  function tapestry_price($price)
  {
    global $config_currencyHTML;

    global $config_currencySeparator;

    global $config_currencyHTMLAfter;

    $price = str_replace(".",$config_currencySeparator,$price);

    return ($config_currencyHTMLAfter?$price.$config_currencyHTML:$config_currencyHTML.$price);
  }

  function tapestry_indexHREF($index,$entry="")
  {
    global $config_baseHREF;

    global $config_useRewrite;

    $indexHREF["merchant"][FALSE] = "merchants.php";

    $indexHREF["merchant"][TRUE]  = "verkoper/";
	
    $indexHREF["links"][FALSE] = "links.php";

    $indexHREF["links"][TRUE]  = "links/";

	$indexHREF["energie"][FALSE] = "energie.php";

    $indexHREF["energie"][TRUE]  = "energie/";

    $indexHREF["category"][FALSE] = "categories.php";

    $indexHREF["category"][TRUE]  = "categorie/";

    $indexHREF["brand"][FALSE] = "brands.php";

    $indexHREF["brand"][TRUE]  = "merken/";

    $href = $config_baseHREF.$indexHREF[$index][$config_useRewrite];

    if ($entry)
    {
      if ($config_useRewrite)
      {
        $href .= tapestry_hyphenate($entry)."/";
      }
      elseif ($index=="category")
      {
        $href = $config_baseHREF."categories.php?path=".urlencode($entry);
      }
      else
      {
        $href = $config_baseHREF."search.php?q=".$index.":".urlencode($entry);
      }
    }

    return $href;
  }

  function tapestry_categoryHierarchyArray($ids)
  {
    global $config_databaseTablePrefix;

    $categoryHierarchyArray = array();

    if (count($ids))
    {
      $in = implode(",",$ids);

      $sql = "SELECT id,name,parent FROM `".$config_databaseTablePrefix."categories_hierarchy` WHERE id IN (".$in.")";

      if (database_querySelect($sql,$rows))
      {
        foreach($rows as $row)
        {
          $parents = array();

          $parents[] = $row["name"];

          $parent = $row["parent"];

          while($parent)
          {
            $sql2 = "SELECT name,parent FROM `".$config_databaseTablePrefix."categories_hierarchy` WHERE id = '".$parent."'";

            database_querySelect($sql2,$rows2);

            $parents[] = $rows2[0]["name"];

            $parent = $rows2[0]["parent"];
          }

          $parents = array_reverse($parents);

          $categoryHierarchyArray[$row["id"]] = implode("/",$parents);
        }
      }

      asort($categoryHierarchyArray);
    }
    return $categoryHierarchyArray;
  }

  function tapestry_categoryHierarchyLowerarchy($id)
  {
    global $config_databaseTablePrefix;

    $ids = array($id);

    $lowerarchy = array();

    do {

      $id = array_pop($ids);

      if ($id) $lowerarchy[] = $id;

      $sql = "SELECT id FROM `".$config_databaseTablePrefix."categories_hierarchy` WHERE parent='".$id."'";

      if (database_querySelect($sql,$rows))
      {
        foreach($rows as $row)
        {
          array_push($ids,$row["id"]);
        }
      }

    } while(count($ids));

    return $lowerarchy;
  }

  function tapestry_categoryHierarchyNodeInfo($path)
  {
    global $config_databaseTablePrefix;

    $nodeInfo = array();

    $nodeInfo["path"] = $path;

    $nodeInfo["hierarchy"] = array();

    $names = explode("/",$path);

    $currentId = 0;

    $currentNames = array();

    foreach($names as $name)
    {
      if (!$name) continue;

      $sql = "SELECT id,name,parent FROM `".$config_databaseTablePrefix."categories_hierarchy` WHERE name='".database_safe($name)."' AND parent='".$currentId."'";

      database_querySelect($sql,$rows);

      $currentId = $rows[0]["id"];

      $currentNames[] = $rows[0]["name"];

      $currentPath = implode("/",$currentNames);

      if ($currentPath == $path) break;

      $nodeInfo["hierarchy"][] = array("id"=>$rows[0]["id"],"name"=>$rows[0]["name"],"path"=>$currentPath);
    }

    $nodeInfo["name"] = $name;

    $nodeInfo["id"] = $currentId;

    $nodeInfo["lowerarchy"] = tapestry_categoryHierarchyLowerarchy($nodeInfo["id"]);

    return $nodeInfo;
  }

  function tapestry_shoppingListHREF()
  {
    global $config_baseHREF;

    return $config_baseHREF."shoppinglist.php";
  }

  function tapestry_shoppingListCookieName()
  {
    global $config_baseHREF;

    return "shoppingList".bin2hex($config_baseHREF);
  }

  function tapestry_shoppingList()
  {
    $shoppingListCookieName = tapestry_shoppingListCookieName();

    if (get_magic_quotes_gpc())
    {
      $_COOKIE[$shoppingListCookieName] = stripslashes($_COOKIE[$shoppingListCookieName]);
    }

    return (isset($_COOKIE[$shoppingListCookieName])?unserialize($_COOKIE[$shoppingListCookieName]):array());
  }

  function tapestry_shoppingListSort($a,$b)
  {
    if ($a["price"] == $b["price"])
    {
      return 0;
    }
    return ($a["price"] < $b["price"]) ? -1 : 1;
  }

  function tapestry_shoppingListRows($name)
  {
    global $config_databaseTablePrefix;

    global $config_useVoucherCodes;

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."products` WHERE name='".database_safe($name)."'";

    if (database_querySelect($sql,$rows))
    {
      if ($config_useVoucherCodes === 1) $rows = tapestry_applyVoucherCodes($rows);

      usort($rows,"tapestry_shoppingListSort");
    }

    return $rows;
  }

  function tapestry_shoppingListAdd($name)
  {
    if ($rows = tapestry_shoppingListRows($name))
    {
      $shoppingList = tapestry_shoppingList();

      $item = array();

      $item["added"] = time();

      $item["price"] = $rows[0]["price"];

      $shoppingList[$name] = $item;

      setcookie(tapestry_shoppingListCookieName(),serialize($shoppingList),strtotime("+90 days"),"/");
    }
  }

  function tapestry_shoppingListRemove($name)
  {
    if ($_GET["remove"]=="@ALL")
    {
      setcookie(tapestry_shoppingListCookieName(),"");
    }
    else
    {
      $shoppingList = tapestry_shoppingList();

      unset($shoppingList[$name]);

      setcookie(tapestry_shoppingListCookieName(),serialize($shoppingList),strtotime("+90 days"),"/");
    }
  }

  function tapestry_productMeta($name,$field)
  {
    global $config_databaseTablePrefix;

    $sql = "SELECT meta FROM `".$config_databaseTablePrefix."productsmap` WHERE name='".database_safe($name)."'";

    if (database_querySelect($sql,$rows))
    {
      $productmap = $rows[0];

      if ($productmap["meta"])
      {
        $productmap["meta"] = unserialize($productmap["meta"]);
      }

      if (isset($productmap["meta"][$field]))
      {
        return $productmap["meta"][$field];
      }
    }

    return "";
  }
?>