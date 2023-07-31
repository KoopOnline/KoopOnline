<?php
  function filter_recordPlaceholders($text)
  {
    global $filter_record;

    if (strpos($text,"%")!==FALSE)
    {
      foreach($filter_record as $k => $v)
      {
        $text = str_replace("%".$k."%",$v,$text);
      }
    }
    return $text;
  }

  /*************************************************/
  /* searchReplace                                 */
  /*************************************************/

  $filter_names["searchReplace"] = "Search and Replace";

  function filter_searchReplaceConfigure($filter_data)
  {
    widget_textBox("Search","search",TRUE,$filter_data["search"],"",3);

    widget_textBox("Replace","replace",FALSE,$filter_data["replace"],"",3);
  }

  function filter_searchReplaceValidate($filter_data)
  {
    if ($filter_data["search"]=="")
    {
      widget_errorSet("search","required field");
    }
  }

  function filter_searchReplaceExec($filter_data,$text)
  {
    return trim(str_replace($filter_data["search"],$filter_data["replace"],$text));
  }

  /*************************************************/
  /* textBefore                                    */
  /*************************************************/

  $filter_names["textBefore"] = "Text Before";

  function filter_textBeforeConfigure($filter_data)
  {
    widget_textBox("Text","text",TRUE,$filter_data["text"],"",3);
  }

  function filter_textBeforeValidate($filter_data)
  {
    if (!$filter_data["text"])
    {
      widget_errorSet("text","required field");
    }
  }

  function filter_textBeforeExec($filter_data,$text)
  {
    return filter_recordPlaceholders($filter_data["text"]).$text;
  }

  /*************************************************/
  /* textAfter                                     */
  /*************************************************/

  $filter_names["textAfter"] = "Text After";

  function filter_textAfterConfigure($filter_data)
  {
    widget_textBox("Text","text",TRUE,$filter_data["text"],"",3);
  }

  function filter_textAfterValidate($filter_data)
  {
    if (!$filter_data["text"])
    {
      widget_errorSet("text","required field");
    }
  }

  function filter_textAfterExec($filter_data,$text)
  {
    return $text.filter_recordPlaceholders($filter_data["text"]);
  }

  /*************************************************/
  /* nameCase                                      */
  /*************************************************/

  $filter_names["nameCase"] = "Name Case";

  function filter_nameCaseConfigure($filter_data)
  {
    print "<p>There are no additional configuration parameters for this filter.</p>";
  }

  function filter_nameCaseValidate($filter_data)
  {
  }

  function filter_nameCaseExec($filter_data,$text)
  {
    return ucwords(strtolower($text));
  }

  /*************************************************/
  /* stripHTML                                     */
  /*************************************************/

  $filter_names["stripHTML"] = "Strip HTML";

  function filter_stripHTMLConfigure($filter_data)
  {
    widget_textBox("Allowable Tags","allowable_tags",FALSE,$filter_data["allowable_tags"],"",3);
  }

  function filter_stripHTMLValidate($filter_data)
  {
  }

  function filter_stripHTMLExec($filter_data,$text)
  {
    return strip_tags($text,$filter_data["allowable_tags"]);
  }

  /*************************************************/
  /* dropRecord                                    */
  /*************************************************/

  $filter_names["dropRecord"] = "Drop Record";

  function filter_dropRecordConfigure($filter_data)
  {
    widget_textBox("Drop record if field contains text","text",FALSE,$filter_data["text"],"",6);
  }

  function filter_dropRecordValidate($filter_data)
  {
  }

  function filter_dropRecordExec($filter_data,$text)
  {
    global $filter_dropRecordFlag;

    if($filter_dropRecordFlag)
    {
      return $text;
    }
    elseif($filter_data["text"])
    {
      $filter_dropRecordFlag = (strstr($text,$filter_data["text"]) !== FALSE);
    }
    else
    {
      $filter_dropRecordFlag = (!$text);
    }

    return $text;
  }

  /*************************************************/
  /* dropRecordRegExp                              */
  /*************************************************/

  $filter_names["dropRecordRegExp"] = "Drop Record RegExp";

  function filter_dropRecordRegExpConfigure($filter_data)
  {
    widget_textBox("Drop record if field matches regular expression","text",FALSE,$filter_data["text"],"",6);
  }
  function filter_dropRecordRegExpValidate($filter_data)
  {
  }
  function filter_dropRecordRegExpExec($filter_data,$text)
  {
    global $filter_dropRecordFlag;

    if($filter_dropRecordFlag)
    {
      return $text;
    }
    else
    {
      $filter_dropRecordFlag = preg_match($filter_data["text"],$text);
    }

    return $text;
  }

  /*************************************************/
  /* dropRecordIfNot                               */
  /*************************************************/

  $filter_names["dropRecordIfNot"] = "Drop Record If Not";

  function filter_dropRecordIfNotConfigure($filter_data)
  {
    widget_textBox("Drop record if field does not contain text","text",FALSE,$filter_data["text"],"",6);
  }

  function filter_dropRecordIfNotValidate($filter_data)
  {
  }

  function filter_dropRecordIfNotExec($filter_data,$text)
  {
    global $filter_dropRecordFlag;

    if($filter_dropRecordFlag)
    {
      return $text;
    }
    elseif($filter_data["text"])
    {
      $filter_dropRecordFlag = (strstr($text,$filter_data["text"]) == FALSE);
    }
    else
    {
      $filter_dropRecordFlag = (strlen($text) > 0);
    }

    return $text;
  }

  /*************************************************/
  /* dropRecordIfNotRegExp */
  /*************************************************/

  $filter_names["dropRecordIfNotRegExp"] = "Drop Record If Not RegExp";

  function filter_dropRecordIfNotRegExpConfigure($filter_data)
  {
    widget_textBox("Drop record if field does not match regular expression","text",FALSE,$filter_data["text"],"",6);
  }
  function filter_dropRecordIfNotRegExpValidate($filter_data)
  {
  }
  function filter_dropRecordIfNotRegExpExec($filter_data,$text)
  {
    global $filter_dropRecordFlag;

    if($filter_dropRecordFlag)
    {
      return $text;
    }
    else
    {
      $filter_dropRecordFlag = !preg_match($filter_data["text"],$text);
    }

    return $text;
  }

  /*************************************************/
  /* Explode                                       */
  /*************************************************/
  $filter_names["explode"] = "Explode";

  function filter_explodeConfigure($filter_data)
  {
    widget_textBox("Explode character or string","text",TRUE,$filter_data["text"],"",3);

    widget_textBox("Return index","index",TRUE,$filter_data["index"],"",2);
  }
  function filter_explodeValidate($filter_data)
  {
    if (!$filter_data["text"])
    {
      widget_errorSet("text","required field");
    }
    if (!is_numeric($filter_data["index"]))
    {
      widget_errorSet("index","required numeric field");
    }
  }
  function filter_explodeExec($filter_data,$text)
  {
    $parts = explode($filter_data["text"],$text);

    $index = intval($filter_data["index"]);

    if ($index < 0)
    {
      $index = count($parts) + $index;
    }

    return $parts[$index];
  }

  /*************************************************/
  /* UTF8 Encode                                   */
  /*************************************************/

  $filter_names["utf8Encode"] = "UTF8 Encode";

  function filter_utf8EncodeConfigure($filter_data)
  {
    print "<p>There are no additional configuration parameters for this filter.</p>";
  }

  function filter_utf8EncodeValidate($filter_data)
  {
  }

  function filter_utf8EncodeExec($filter_data,$text)
  {
    return utf8_encode($text);
  }

  /*************************************************/
  /* UTF8 Decode                                   */
  /*************************************************/

  $filter_names["utf8Decode"] = "UTF8 Decode";

  function filter_utf8DecodeConfigure($filter_data)
  {
    print "<p>There are no additional configuration parameters for this filter.</p>";
  }

  function filter_utf8DecodeValidate($filter_data)
  {
  }

  function filter_utf8DecodeExec($filter_data,$text)
  {
    return utf8_decode($text);
  }

  /*************************************************/
  /* HTMLEntityDecode                              */
  /*************************************************/

  $filter_names["HTMLEntityDecode"] = "HTML Entity Decode";

  function filter_HTMLEntityDecodeConfigure($filter_data)
  {
    print "<p>There are no additional configuration parameters for this filter.</p>";
  }

  function filter_HTMLEntityDecodeValidate($filter_data)
  {
  }

  function filter_HTMLEntityDecodeExec($filter_data,$text)
  {
    return html_entity_decode($text);
  }
  
  /*************************************************/
  /* priceAdjust */
  /*************************************************/
  $filter_names["priceAdjust"] = "Price Adjust";
  function filter_priceAdjustConfigure($filter_data)
  {
    global $config_currencyHTML;
    widget_textBox("Amount","amount",TRUE,$filter_data["amount"],"",3);
    widget_selectArray("","type",TRUE,$filter_data["type"],array("#"=>$config_currencyHTML,"%"=>"%"));
  }
  function filter_priceAdjustValidate($filter_data)
  {
    if ($filter_data["amount"]=="") widget_errorSet("amount","required field");
  }
  function filter_priceAdjustExec($filter_data,$text)
  {
    $text = tapestry_decimalise($text);
    if ($filter_data["type"]=="#")
    {
      $text = ($text + $filter_data["amount"]);
    }
    else
    {
      $text = ($text / 100) * (100 + $filter_data["amount"]);
    }
    $text = tapestry_decimalise($text);
    return $text;
  }
  
  /*************************************************/
  /* EANfix */
  /*************************************************/
  $filter_names["EANfix"] = "EAN Fix";
  function filter_EANfixConfigure($filter_data)
  {
    print "<p>There are no additional configuration parameters for this filter.</p>";
  }
  function filter_EANfixValidate($filter_data)
  {
  }
  function filter_EANfixExec($filter_data,$text)
  {
    if (strlen($text)==12) $text = "0".$text;
	if (strlen($text)==11) $text = "00".$text;
	return $text;
  }
?>