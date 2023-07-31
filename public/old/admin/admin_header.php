<?php
  header("Content-Type: text/html;charset=".$config_charset);

  header('Cache-Control: no-cache, private, must-revalidate, max-stale=0, post-check=0, pre-check=0, no-store');

  header('Pragma: no-cache');

  header('Expires: Thu, 1 Jan 1970 00:00:00 GMT');
?>
<!DOCTYPE HTML>

<html>

  <head>

    <meta name='viewport' content='width=device-width, initial-scale=1.0' />

    <title><?php print translate("Admin"); ?></title>

      <link rel='stylesheet' href='vendor/foundation.min.css' />

      <link rel='stylesheet' href='admin_default.css' />

      <script src='vendor/jquery.min.js'></script>

      <script src='vendor/foundation.min.js'></script>

      <?php if (isset($header["head"])) print $header["head"]; ?>

    </head>

  <body>

  <div class='row'>

    <div class='small-12 columns'>

      <ul class='inline-list'>

        <li><a target='_BLANK' href='<?php print $config_baseHREF; ?>'><?php print translate("Site Home"); ?> <img border='0' width='16' height='16' src='newwindow.png' /></a></li>

        <li><a href='./'><?php print translate("Admin Home"); ?></a></li>

        <?php if (isset($_COOKIE["admin"])): ?>

        <li>

          <a href="#" data-dropdown="drop1"><?php print translate("Setup"); ?></a>

          <ul id="drop1" class="f-dropdown" data-dropdown-content>

            <li><a href='automation_tool.php'><?php print translate("Automation Tool"); ?></a></li>

            <li><a href='cron.php'><?php print translate("CRON"); ?></a></li>

          </ul>

        </li>

        <li>

          <a href="#" data-dropdown="drop2"><?php print translate("Data Management"); ?></a>

          <ul id="drop2" class="f-dropdown" data-dropdown-content>

            <li><a href='brands.php'><?php print translate("Brand Mapping"); ?></a></li>
			
            <li><a href='automation_tool_description.php'><?php print 'Automatic descriptions'; ?></a></li>

            <li><a href='categories.php'><?php print translate("Category Mapping"); ?></a></li>

            <li><a href='categories_hierarchy.php'><?php print translate("Category Hierarchy Mapping"); ?></a></li>

            <li><a href='productsmap.php'><?php print translate("Product Mapping"); ?></a></li>

            <li><a href='productsmap_regexp.php'><?php print translate("Product Mapping RegExp"); ?></a></li>

          </ul>

        </li>

        <li>

          <a href="#" data-dropdown="drop3"><?php print translate("Content Management"); ?></a>

          <ul id="drop3" class="f-dropdown" data-dropdown-content>

            <li><a href='featured.php'><?php print translate("Featured Products"); ?></a></li>

            <li><a href='moderate.php'><?php print translate("Moderate Reviews"); ?></a></li>

            <li><a href='merchant_logos.php'><?php print translate("Merchant Logos"); ?></a></li>

            <li><a href='voucher_codes.php'><?php print translate("Voucher Codes"); ?></a></li>

          </ul>

        </li>

        <li>

          <a href="#" data-dropdown="drop4"><?php print translate("Tools"); ?></a>

          <ul id="drop4" class="f-dropdown" data-dropdown-content>

            <li><a href='database_tool.php'><?php print translate("Backup and Restore"); ?></a></li>

            <li><a href='feeds_utils.php'><?php print translate("Feed Utilities"); ?></a></li>

            <li><a href='support.php'><?php print translate("Support Info"); ?></a></li>

          </ul>

        </li>

        <?php
          $extensions = array();

          $extensionsDirectory = "extensions/";

          if (file_exists($extensionsDirectory))
          {
            $dh = opendir($extensionsDirectory);

            while($de = readdir($dh))
            {
              if ((strpos($de,".") === 0) || (!is_dir($extensionsDirectory.$de))) continue;

              $extensionMeta = $extensionsDirectory.$de."/".$de.".php";

              if (!file_exists($extensionMeta)) continue;

              require($extensionMeta);

              $extensions[$de] = $$de;
            }
          }

          if (count($extensions))
          {
            print "<li>";

            print "<a href='#' data-dropdown='drop5'>".translate("Extensions")."</a>";

            print "<ul id='drop5' class='f-dropdown' data-dropdown-content>";

            print "<dl class='accordion' data-accordion>";

            foreach($extensions as $namespace => $extension)
            {
              print "<dd class='accordion-navigation'>";

                print "<a href='#panel_".$namespace."'>".$extension["title"]."</a>";

                print "<div id='panel_".$namespace."' class='content content_pta'>";

                  print "<ul class='f-dropdown_pta'>";

                    foreach($extension["menu"] as $etitle => $efilename)
                    {
                      print "<li><a href='extensions.php?ns=".$namespace."&pn=".$efilename."'>".$etitle."</a></li>";
                    }

                  print "</ul>";

                print "</div>";

              print "</dd>";
            }

            print "</dl>";

            print "</ul>";

            print "</li>";
          }
        ?>

        <?php endif; ?>

      </ul>

    </div>

  </div>

  <div class='row'>

    <div class='small-12 columns'>