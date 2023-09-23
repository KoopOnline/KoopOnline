<?php
  function admin_register($filename,$format,$merchant,$fieldMerchant,$registerFields,$userCategory,$userBrand)
  {
    global $config_databaseTablePrefix;

    if (!$filename)
    {
      return "filename missing";
    }

    if (!$format)
    {
      return "format missing";
    }

    if (!$registerFields["name"])
    {
      return "name field missing";
    }

    if (!$registerFields["buy_url"])
    {
      return "Buy URL missing";
    }

    if (!$registerFields["price"])
    {
      return "price field missing";
    }

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE filename='".database_safe($filename)."'";

    if (database_querySelect($sql,$rows))
    {
      $feed = $rows[0];

      $sql = "DELETE FROM `".$config_databaseTablePrefix."products` WHERE filename = '".database_safe($filename)."'";

      database_queryModify($sql,$insertId);

      $sql = "DELETE FROM `".$config_databaseTablePrefix."feeds` WHERE id = '".database_safe($feed["id"])."'";

      database_queryModify($sql,$insertId);
    }

    $registerFieldsSQL = "";

    foreach($registerFields as $field => $v)
    {
      $registerFieldsSQL .= "field_".$field."='".database_safe($v)."',";
    }

    $sql = sprintf("INSERT INTO `".$config_databaseTablePrefix."feeds` SET
                    filename='%s',
                    registered='%s',
                    format='%s',
                    merchant='%s',
                    field_merchant='%s',

                    %s

                    user_category='%s',
                    user_brand='%s'
                    ",
                    database_safe($filename),
                    time(),
                    database_safe($format),
                    database_safe($merchant),
                    database_safe($fieldMerchant),

                    $registerFieldsSQL,

                    database_safe(tapestry_normalise($userCategory)),
                    database_safe(tapestry_normalise($userBrand))
                    );

    database_queryModify($sql,$insertId);

    return "";
  }

  function admin_deregister($filename)
  {
    global $config_databaseTablePrefix;

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE filename='".database_safe($filename)."'";

    if (!database_querySelect($sql,$rows))
    {
      return $filename." not registered";
    }

    $feed = $rows[0];

    $sql = "DELETE FROM `".$config_databaseTablePrefix."products` WHERE filename='".database_safe($feed["filename"])."'";

    database_queryModify($sql,$insertId);

    $sql = "DELETE FROM `".$config_databaseTablePrefix."filters` WHERE filename='".database_safe($feed["filename"])."'";

    database_queryModify($sql,$insertId);

    $sql = "DELETE FROM `".$config_databaseTablePrefix."feeds` WHERE filename='".database_safe($feed["filename"])."'";

    database_queryModify($sql,$insertId);
  }

  function admin__importRecordHandler($record)
  {
    global $config_databaseTablePrefix;

    global $config_fieldSet;

    global $config_useVoucherCodes;

    global $config_nicheMode;

    global $admin_importBrandMappings;

    global $admin_importCategoryMappings;

    global $admin_importCategoryHierarchyMappings;

    global $admin_importProductMappingsRegExp;

    global $admin_importProductMappingsRegExpOverrides;

    global $admin_importProductMappings;

    global $admin_importProductMappingsOverrides;

    global $admin_importFiltersExist;

    global $admin_importFilters;

    global $admin_importFeed;

    global $admin_importProductCount;

    global $admin_importLimit;

    global $admin_importCallback;

    global $admin_importAll;

    global $filter_dropRecordFlag;

    global $filter_record;

    /* trim all fields */
    foreach($record as $k => $v) $record[$k] = trim($v);

    /* copy record into global space for use by filters */
    $filter_record = $record;

    /* create array on which to apply filters etc. */
    $importRecord = array();

    $skipFields = array("category","brand");

    foreach($config_fieldSet as $field => $v)
    {
      if (in_array($field,$skipFields)) continue;

      if (isset($admin_importFeed["field_".$field]))
      {
        if (isset($record[$admin_importFeed["field_".$field]]))
        {
          $importRecord[$field] = $record[$admin_importFeed["field_".$field]];
        }
        else
        {
          $importRecord[$field] = "";
        }
      }
    }

    if ($admin_importFeed["field_category"])
    {
      if (isset($record[$admin_importFeed["field_category"]]))
      {
        $importRecord["category"] = $record[$admin_importFeed["field_category"]];
      }
      else
      {
        $importRecord["category"] = "";
      }
    }
    elseif($admin_importFeed["user_category"])
    {
      $importRecord["category"] = $admin_importFeed["user_category"];
    }
    else
    {
      $importRecord["category"] = "";
    }
    if ($importRecord["category"])
    {
      $importRecord["category"] = tapestry_normalise($importRecord["category"]);

      $importRecord["category"] = preg_replace('/[ ]{2,}/',' ',$importRecord["category"]);
    }

    if ($admin_importFeed["field_brand"])
    {
      if (isset($record[$admin_importFeed["field_brand"]]))
      {
        $importRecord["brand"] = $record[$admin_importFeed["field_brand"]];
      }
      else
      {
        $importRecord["brand"] = "";
      }
    }
    elseif($admin_importFeed["user_brand"])
    {
      $importRecord["brand"] = $admin_importFeed["user_brand"];
    }
    else
    {
      $importRecord["brand"] = "";
    }
    if ($importRecord["brand"])
    {
      $importRecord["brand"] = tapestry_normalise($importRecord["brand"]);

      $importRecord["brand"] = preg_replace('/[ ]{2,}/',' ',$importRecord["brand"]);
    }

    /* cleansing */
    $importRecord["name"] = preg_replace('/[ ]{2,}/',' ',$importRecord["name"]);

    /* construct merchant value */
    if ($admin_importFeed["field_merchant"])
    {
      $importRecord["merchant"] = $record[$admin_importFeed["field_merchant"]];

      $importRecord["merchant"] = tapestry_normalise($importRecord["merchant"],"\.");
    }
    else
    {
      $importRecord["merchant"] = $admin_importFeed["merchant"];
    }

    /* apply user filters */
    $filter_dropRecordFlag = FALSE;

    if ($admin_importFiltersExist)
    {
      foreach($admin_importFilters as $filter)
      {
        $execFunction = "filter_".$filter["name"]."Exec";

        $importRecord[$filter["field"]] = $execFunction($filter["data"],$importRecord[$filter["field"]]);
      }
    }

    /* drop record if set by user filters filters */
    if ($filter_dropRecordFlag) return;


    /* capture original catalogue product name prior to mapping */
    $importRecord["original_name"] = $importRecord["name"];
    $importRecord["normalised_original_name"] = tapestry_normalise($importRecord["name"]);

    /* apply product mappings */
    if (isset($admin_importProductMappings["=".$importRecord["name"]]))
    {
      $importRecord["name"] = $admin_importProductMappings["=".$importRecord["name"]];
    }
    else
    {
      foreach($admin_importProductMappings as $k => $v)
      {
        if (substr($k,0,1) !== "=")
        {
          $found = 0;

          $words = explode(" ",$k);

          foreach($words as $word)
          {
            if ($word)
            {
              if (strpos($importRecord["name"],$word) !== FALSE) $found++;
            }
          }
          if ($found == count_($words))
          {
            $importRecord["name"] = $v;

            break;
          }
        }
      }
    }

    if (isset($admin_importProductMappingsOverrides[$importRecord["name"]]))
    {
      if ($admin_importProductMappingsOverrides[$importRecord["name"]]["description"]) $importRecord["description"] = $admin_importProductMappingsOverrides[$importRecord["name"]]["description"];

      if ($admin_importProductMappingsOverrides[$importRecord["name"]]["category"]) $importRecord["category"] = $admin_importProductMappingsOverrides[$importRecord["name"]]["category"];

      if ($admin_importProductMappingsOverrides[$importRecord["name"]]["brand"]) $importRecord["brand"] = $admin_importProductMappingsOverrides[$importRecord["name"]]["brand"];

      if ($admin_importProductMappingsOverrides[$importRecord["name"]]["image_url"]) $importRecord["image_url"] = $admin_importProductMappingsOverrides[$importRecord["name"]]["image_url"];
    }

    /* apply product mappings regexp */

    $regexpMapped = FALSE;

    foreach($admin_importProductMappingsRegExp as $v)
    {
      $apply =
        (
        preg_match(($v["trigger_merchant"]?$v["trigger_merchant"]:"/.*/"),$importRecord["merchant"])
        &
        preg_match(($v["trigger_category"]?$v["trigger_category"]:"/.*/"),$importRecord["category"])
        &
        preg_match(($v["trigger_brand"]?$v["trigger_brand"]:"/.*/"),$importRecord["brand"])
        );

      if ($apply)
      {
        if (!$v["regexp"]) continue;

        preg_match($v["regexp"],$importRecord["name"],$matches);

        if (count_($matches))
        {
          $regexpMapped = TRUE;

          $importRecord["name"] = $v["product_name"];

          foreach($matches as $k => $match)
          {
            $importRecord["name"] = str_replace("\$".$k,$match,$importRecord["name"]);
          }

          if ($admin_importProductMappingsRegExpOverrides[$v["name"]]["category"]) $importRecord["category"] = $admin_importProductMappingsRegExpOverrides[$v["name"]]["category"];

          if ($admin_importProductMappingsRegExpOverrides[$v["name"]]["brand"]) $importRecord["brand"] = $admin_importProductMappingsRegExpOverrides[$v["name"]]["brand"];

          break;
        }
      }
    }

    /* apply brand mappings */
    if (isset($admin_importBrandMappings["=".$importRecord["brand"]]))
    {
      $importRecord["brand"] = $admin_importBrandMappings["=".$importRecord["brand"]];
    }
    else
    {
      foreach($admin_importBrandMappings as $k => $v)
      {
        if (substr($k,0,1) !== "=")
        {
          $found = 0;

          $words = explode(" ",$k);

          foreach($words as $word)
          {
            if ($word)
            {
              if (strpos($importRecord["brand"],$word) !== FALSE) $found++;
            }
          }

          if ($found == count_($words))
          {
            $importRecord["brand"] = $v;

            break;
          }
        }
      }
    }

    /* apply category mappings */
    if (isset($admin_importCategoryMappings["=".$importRecord["category"]]))
    {
      $importRecord["category"] = $admin_importCategoryMappings["=".$importRecord["category"]];
    }
    else
    {
      foreach($admin_importCategoryMappings as $k => $v)
      {
        if (substr($k,0,1) !== "=")
        {
          $found = 0;

          $words = explode(" ",$k);

          foreach($words as $word)
          {
            if ($word)
            {
              if (strpos($importRecord["category"],$word) !== FALSE) $found++;
            }
          }

          if ($found == count_($words))
          {
            $importRecord["category"] = $v;

            break;
          }
        }
      }
    }

    /* apply category hierarchy mappings */
    if (isset($admin_importCategoryHierarchyMappings["=".$importRecord["category"]]))
    {
      $importRecord["categoryid"] = $admin_importCategoryHierarchyMappings["=".$importRecord["category"]];
    }
    else
    {
      foreach($admin_importCategoryHierarchyMappings as $k => $v)
      {
        if (substr($k,0,1) !== "=")
        {
          $found = 0;

          $words = explode(" ",$k);

          foreach($words as $word)
          {
            if ($word)
            {
              if (strpos($importRecord["category"],$word) !== FALSE) $found++;
            }
          }

          if ($found == count_($words))
          {
            $importRecord["categoryid"] = $v;

            break;
          }
        }
      }
    }

    /* check product record for minimum required fields */
    if (!$importRecord["name"] || !$importRecord["buy_url"] || !$importRecord["price"]) return;

    /* niche mode */
    if ($config_nicheMode)
    {
      if (
         !in_array($importRecord["name"],$admin_importProductMappings)
         &&
         !$regexpMapped
         )
         return;
    }

    /* create normalised version of product name for use in URLs */
    $normalisedName = tapestry_normalise($importRecord["name"]);

    /* decimalise price */
    $importRecord["price"] = tapestry_decimalise($importRecord["price"]);

    /* construct search_name value */
    $searchName = tapestry_search($normalisedName);

    if (!$importRecord["merchant"]) return;

    if ($config_useVoucherCodes == 2)
    {
      $importRecordArray = tapestry_applyVoucherCodes(array($importRecord));

      $importRecord = $importRecordArray[0];
    }

    /* create dupe_hash value */
    $dupe_key  = $importRecord["merchant"];

    $dupe_key .= tapestry_mb_strtolower($searchName);

    $dupe_hash = md5($dupe_key);

    /* create product record */
    $importRecordSQL = "";

    foreach($importRecord as $field => $v)
    {
      $importRecordSQL .= "`".$field."`='".database_safe($v)."',";
    }

    if (isset($admin_importAll))
    {
      $table = "products_import";
    }
    else
    {
      $table = "products";
    }
    $sql = sprintf("INSERT IGNORE INTO `".$config_databaseTablePrefix.$table."` SET
                    filename='%s',

                    %s

                    search_name='%s',
                    normalised_name='%s',

                    dupe_hash='%s'
                    ",
                    database_safe($admin_importFeed["filename"]),

                    $importRecordSQL,

                    database_safe($searchName),
                    database_safe($normalisedName),

                    $dupe_hash
                    );

    if (database_queryModify($sql,$insertId))
    {
      $admin_importProductCount++;
    }

    if ($admin_importCallback)
    {
      if (!($admin_importProductCount % 100))
      {
        $admin_importCallback($admin_importProductCount);
      }
    }

    return ($admin_importProductCount == $admin_importLimit);
  }

  function admin_importSetGlobals()
  {
    global $config_databaseTablePrefix;

    global $admin_importFeed;

    global $admin_importBrandMappings;

    global $admin_importCategoryMappings;

    global $admin_importCategoryHierarchyMappings;

    global $admin_importProductMappingsRegExp;

    global $admin_importProductMappingsRegExpOverrides;

    global $admin_importProductMappings;

    global $admin_importProductMappingsOverrides;

    global $admin_importFiltersExist;

    global $admin_importFilters;

    $admin_importBrandMappings = array();

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."brands`";

    if (database_querySelect($sql,$rows))
    {
      foreach($rows as $brand)
      {
        $alternates = explode("\n",$brand["alternates"]);

        foreach($alternates as $alternate)
        {
          $alternate = trim($alternate);

          $admin_importBrandMappings[$alternate] = $brand["name"];
        }
      }
    }

    $admin_importCategoryMappings = array();

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."categories`";

    if (database_querySelect($sql,$rows))
    {
      foreach($rows as $category)
      {
        $alternates = explode("\n",$category["alternates"]);

        foreach($alternates as $alternate)
        {
          $alternate = trim($alternate);

          $admin_importCategoryMappings[$alternate] = $category["name"];
        }
      }
    }

    $admin_importCategoryHierarchyMappings = array();

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."categories_hierarchy`";

    if (database_querySelect($sql,$rows))
    {
      foreach($rows as $category)
      {
        $alternates = explode("\n",$category["alternates"]);

        foreach($alternates as $alternate)
        {
          $alternate = trim($alternate);

          $admin_importCategoryHierarchyMappings[$alternate] = $category["id"];
        }
      }
    }

    $admin_importProductMappingsOverrides = array();

    $admin_importProductMappings = array();

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."productsmap`";

    if (database_querySelect($sql,$rows))
    {
      foreach($rows as $productsmap)
      {
        $alternates = explode("\n",$productsmap["alternates"]);

        foreach($alternates as $alternate)
        {
          $alternate = trim($alternate);

          $admin_importProductMappings[$alternate] = $productsmap["name"];
        }
        $admin_importProductMappingsOverrides[$productsmap["name"]]["description"] = (($productsmap["description"])?$productsmap["description"]:"");

        $admin_importProductMappingsOverrides[$productsmap["name"]]["category"] = (($productsmap["category"])?$productsmap["category"]:"");

        $admin_importProductMappingsOverrides[$productsmap["name"]]["brand"] = (($productsmap["brand"])?$productsmap["brand"]:"");

        $admin_importProductMappingsOverrides[$productsmap["name"]]["image_url"] = (($productsmap["image_url"])?$productsmap["image_url"]:"");
      }
    }

    $admin_importProductMappingsRegExpOverrides = array();

    $admin_importProductMappingsRegExp = array();

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."productsmap_regexp`";

    if (database_querySelect($sql,$rows))
    {
      $admin_importProductMappingsRegExp = $rows;

      foreach($rows as $productsmap_regexp)
      {
        $admin_importProductMappingsRegExpOverrides[$productsmap_regexp["name"]]["category"] = (($productsmap_regexp["category"])?$productsmap_regexp["category"]:"");

        $admin_importProductMappingsRegExpOverrides[$productsmap_regexp["name"]]["brand"] = (($productsmap_regexp["brand"])?$productsmap_regexp["brand"]:"");
      }
    }

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."filters` WHERE filename='".database_safe($admin_importFeed["filename"])."' OR filename='' ORDER BY filename,created";

    if (database_querySelect($sql,$rows))
    {
      $admin_importFiltersExist = TRUE;

      $admin_importFilters = array();

      foreach($rows as $filter)
      {
        $filter["data"] = unserialize($filter["data"]);

        $admin_importFilters[] = $filter;
      }
    }
    else
    {
      $admin_importFiltersExist = FALSE;
    }
  }

  function admin_import($filename,$limit=0,$callback="")
  {
    global $config_databaseTablePrefix;

    global $config_feedDirectory;

    global $admin_importFeed;

    global $admin_importProductCount;

    global $admin_importLimit;

    global $admin_importCallback;

    global $admin_importAll;

    $admin_importLimit = $limit;

    $admin_importCallback = $callback;

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE filename='".database_safe($filename)."'";

    if (!database_querySelect($sql,$rows))
    {
      return $filename." not registered";
    }

    $admin_importFeed = $rows[0];

    admin_importSetGlobals();

    if (isset($admin_importAll))
    {
      $table = "products_import";
    }
    else
    {
      $table = "products";

      $sql = "DELETE FROM `".$config_databaseTablePrefix.$table."` WHERE filename='".database_safe($admin_importFeed["filename"])."'";

      database_queryModify($sql,$insertId);
    }
    $admin_importProductCount = 0;

    MagicParser_parse($config_feedDirectory.$admin_importFeed["filename"],"admin__importRecordHandler",$admin_importFeed["format"]);

    $sql = "SELECT count(*) AS productCount FROM `".$config_databaseTablePrefix.$table."` WHERE filename='".database_safe($admin_importFeed["filename"])."'";

    database_querySelect($sql,$rows);

    $productCount = $rows[0]["productCount"];

    $sql = "UPDATE `".$config_databaseTablePrefix."feeds` SET imported='".time()."',products='".$productCount."' WHERE filename='".database_safe($filename)."'";

    database_queryModify($sql,$insertId);

    return "";
  }

  function admin_importReviews()
  {
    global $config_databaseTablePrefix;

    $sql = "UPDATE `".$config_databaseTablePrefix."products` SET rating='0',reviews='0'";

    database_queryModify($sql,$insertId);

    $sql = "SELECT product_name,AVG(rating) as rating,count_(id) as reviews FROM `".$config_databaseTablePrefix."reviews` WHERE approved <> '0' GROUP BY product_name";

    if (database_querySelect($sql,$rows))
    {
      foreach($rows as $review)
      {
        $sql = "UPDATE `".$config_databaseTablePrefix."products` SET rating='".$review["rating"]."',reviews='".$review["reviews"]."' WHERE normalised_name='".database_safe($review["product_name"])."'";

        database_queryModify($sql,$insertId);
      }
    }
  }

  function admin_copyFilters($sourceFilename,$destinationFilename)
  {
    global $config_databaseTablePrefix;

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE filename='".database_safe($sourceFilename)."'";

    if (!database_querySelect($sql,$rows))
    {
      return $sourceFilename." not registered";

      exit();
    }

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."feeds` WHERE filename='".database_safe($destinationFilename)."'";

    if (!database_querySelect($sql,$rows))
    {
      return $destinationFilename." not registered";

      exit();
    }

    $sql = "DELETE FROM `".$config_databaseTablePrefix."filters` WHERE filename='".database_safe($destinationFilename)."'";

    database_queryModify($sql,$insertId);

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."filters` WHERE filename='".database_safe($sourceFilename)."' ORDER BY created";

    if (database_querySelect($sql,$rows))
    {
      $created = time();

      foreach($rows as $filter)
      {
        $sql = sprintf("INSERT INTO `".$config_databaseTablePrefix."filters` SET
                        filename = '%s',
                        field = '%s',
                        name = '%s',
                        data = '%s',
                        created = '%s'
                        ",
                        database_safe($destinationFilename),
                        database_safe($filter["field"]),
                        database_safe($filter["name"]),
                        database_safe($filter["data"]),
                        $created++
                        );

        Database_queryModify($sql,$insertId);
      }
    }

    return "";
  }

  function admin_rfctime($time)
  {
    return @date("Y-m-d H:i:s",$time);
  }

  function admin_tool($title,$href,$enabled=TRUE,$highlight=FALSE,$highlightClass="success")
  {
    if ($enabled)
    {
      print "<a class='button tiny radius ".($highlight?$highlightClass:"")."' href='".$href."'>".translate($title)."</a>";
    }
    else
    {
      print "<a class='button tiny radius secondary disabled'>".translate($title)."</a>";
    }
  }

  function admin_tableBegin()
  {
    print "<table>";
  }

  function admin_tableRow($k,$v,$class="pta_txt")
  {
    print "<tr>";

    print "<th class='pta_key'>".translate($k)."</th>";

    print "<td class='".$class."'>".$v."</td>";

    print "</tr>";
  }

  function admin_tableEnd()
  {
    print "</table>";
  }

  if (isset($admin_checkPassword) && $admin_checkPassword)
  {
    if
      (
      (!$config_adminPassword)
      ||
      (!isset($_COOKIE["admin"]))
      ||
      ($_COOKIE["admin"] <> md5($config_adminPassword))
      )
    {
      header("Location: login.php");

      exit();
    }
  }

  set_time_limit(0);
?>
