<?php
  $config_adminPassword = "6205023ta";

  $config_feedDirectory = "../feeds/";

  $config_databaseDebugMode = FALSE;

  $config_useFullText = TRUE;

  $config_searchDescription = FALSE;

  $config_fieldSet = array();
  $config_fieldSet["name"] = "Product Naam";
  $config_fieldSet["description"] = "Beschrijving";
  $config_fieldSet["image_url"] = "Image URL";
  $config_fieldSet["buy_url"] = "Koop URL";
  $config_fieldSet["price"] = "Prijs";
  $config_fieldSet["category"] = "Categorie";
  $config_fieldSet["brand"] = "Merk";
  $config_fieldSet["ean"] = "EAN";

  $config_commonFields = array();
  $config_commonFields["name"] =
    array("TEXT/NAME","PRODUCT-NAME","product title","PRODUCT TITLE","Product Title","product_title","PRODUCT_TITLE","Product_Title","PRODUCT NAME","product name","Product Name","PRODUCT_NAME","product_name","Product_Name","producttitle","PRODUCTTITLE","ProductTitle","productTitle","productname","PRODUCTNAME","ProductName","productName","name","NAME","Name","title","TITLE");
  $config_commonFields["description"] =
    array("TEXT/DESC","DESCRIPTION/LONG","DESCRIPTION/SHORT","long description","LONG DESCRIPTION","Long Description","long_description","LONG_DESCRIPTION","Long_Description","longdescription","LONGDESCRIPTION","LongDescription","longDescription","short description","SHORT DESCRIPTION","Short Description","short_description","SHORT_DESCRIPTION","Short_Description","shortdescription","SHORTDESCRIPTION","ShortDescription","shortDescription","description","DESCRIPTION","Description");
  $config_commonFields["image_url"] =
    array("URL/PRODUCTIMAGE","URI/MIMAGE","URI/AWIMAGE","image url","IMAGE URL","Image Url","Image URL","image_url","IMAGE_URL","Image_Url","Image_URL","imageurl","IMAGEURL","ImageUrl","ImageURL","imageUrl","imageURL","image","IMAGE","Image","merchant_image_url","Image url","IMAGES/IMAGE/LOCATION");
  $config_commonFields["buy_url"] =
    array("URI/AWTRACK","URL/PRODUCT","product link","PRODUCT LINK","Product Link","product_link","PRODUCT_LINK","Product_Link","productlink","PRODUCTLINK","ProductLink","productLink","producturl","PRODUCTURL","ProductUrl","productUrl","deep link","DEEP LINK","Deep Link","deep_link","DEEP_LINK","Deep_Link","deeplink","DEEPLINK","DeepLink","deepLink","buy url","BUY URL","Buy Url","buy URL","Buy URL","buy url","BUY_URL","Buy_Url","buy_URL","Buy_URL","buyurl","BUYURL","BuyUrl","buyURL","buyURL","BuyURL","url","URL","Url","aw_deep_link","buyat_short_deeplink_url","link","LINK","productURL");
  $config_commonFields["price"] =
    array("PRICE/PRICE","PRICE/BUYNOW","price","PRICE","Price","search_price","online_price","our_price");
  $config_commonFields["category"] =
    array("CAT/AWCAT","CATEGORY/PRIMARY","mastercategory","MASTERCATEGORY","MasterCategory","masterCategory","merchant category","MERCHANT CATEGORY","Merchant Category","merchant_category","MERCHANT_CATEGORY","Merchant_Category","category_name","CATEGORY_NAME","Category_Name","category name","CATEGORY NAME","Category Name","category","CATEGORY","Category","TDCategoryName","ADVERTISERCATEGORY","categories");
  $config_commonFields["brand"] =
    array("BRAND/BRANDNAME","merchant brand","MERCHANT BRAND","Merchant Brand","merchant_brand","MERCHANT_BRAND","Merchant_Brand","brand_name","BRAND_NAME","Brand_Name","brand name","BRAND NAME","Brand Name","brandname","BRANDNAME","BrandName","brandName","BRAND","brand","Brand","MANUFACTURER");
  $config_commonFields["ean"] =
    array("ean","EAN","Ean");

  $config_slowImportBlock = 2000;

  $config_slowImportSleep = 0;

  $config_useVoucherCodes = 0;

  $config_automationHandler = "php";

  $config_automationUnzipPrograms = array();
  $config_automationUnzipPrograms["unzip"] = "/usr/bin/unzip";
  $config_automationUnzipPrograms["gzip"] = "/bin/gzip";

  $config_CRONPrograms = array();
  $config_CRONPrograms["php"] = array("/usr/bin/php","/usr/local/bin/php");
  $config_CRONPrograms["wget"] = array("/usr/bin/wget","/usr/local/bin/wget");

  $config_nicheMode = FALSE;

  $config_enableSearchFilters = TRUE;

  $config_voucherCodesFeedDirectory = "../voucherfeeds/";

  $config_voucherCodesFieldSet = array();
  $config_voucherCodesFieldSet["merchant"] = "Merchant Name";
  $config_voucherCodesFieldSet["code"] = "Voucher Code";
  $config_voucherCodesFieldSet["valid_from"] = "Valid From";
  $config_voucherCodesFieldSet["valid_to"] = "Valid To";
  $config_voucherCodesFieldSet["description"] = "Description";

  $config_voucherCodesCommonFields = array();
  $config_voucherCodesCommonFields["merchant"] =
    array("merchant","program_name");
  $config_voucherCodesCommonFields["code"] =
    array("code");
  $config_voucherCodesCommonFields["valid_from"] =
    array("start_date");
  $config_voucherCodesCommonFields["valid_to"] =
    array("end_date");
  $config_voucherCodesCommonFields["description"] =
    array("description");

  $config_voucherCodesDiscountValuePrefixRegexp = "(save)";

  $config_voucherCodesDiscountValuePostfixRegexp = "(discount|off|voucher)";

  $config_voucherCodesMinSpendPrefixRegexp = "(spend|over|more than|at least)";

  $config_timezone = "UTC";

  $config_currencySeparator = ".";

  $config_currencyHTMLAfter = FALSE;

  $config_normaliseRegExp = "A-Za-z0-9".chr(0x80)."-".chr(0xFF)." \.";

  $config_useCategoryHierarchy = TRUE;

  $config_filenameRegExp = "/^([0-9A-Za-z_\-\.]+)(\.xml|\.csv)$/";

  $config_logoExtension = ".img";

  $config_useShoppingList = FALSE;

  $config_uidField = "ean";

?>
