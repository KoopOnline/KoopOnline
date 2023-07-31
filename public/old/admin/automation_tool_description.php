<?php  

  require("../includes/common.php");

  $admin_checkPassword = TRUE;

  require("../includes/admin.php");

  require("../includes/widget.php");
  
  require("admin_header.php");

  print "<h2>Description Automation Tool</h2>";

	  if (!isset($_GET['EAN'])) {
	  
		echo '<form type="GET" name="" action="">
		<div class="row"><div class="medium-3 columns">
		Enter an EAN:<br><br><input id="name" type="text" size="31" name="EAN">
		Ammount of sentences:
		<br><br><select name="sentences"><option value="1">1</option><option value="2">2</option><option value="3">3</option></select>
		<button type="submit" name="submit" class="pta_button small radius success" value="Send">Send</button>
		</div></div>';
	  } else if (isset($_GET['EAN'])) {
		  
		  echo 'Getting info for '.$_GET['EAN'].'<br><br>';
		  
		   $sql = "SELECT name,description FROM `".$config_databaseTablePrefix."products` WHERE ean='".database_safe($_GET['EAN'])."' AND LENGTH(description) > 150 ORDER BY RAND()";
		   
		     if (database_querySelect($sql,$rows))
			  {
				print "<table>";
				
				$explode_n = 0;

				foreach($rows as $product)
				{
				  print "<tr>";

				  print "<th class='pta_key'>".$product["name"]."</th>";

				  print "<td style=\"font-size: 8px; line-height: 8px;\">";

					print $product["description"];

				  print "</td>";

				  print "</tr>";
				  
				  $explode = explode("~ ", str_replace(array("? ", "! ", ". "), "~ ", $product["description"]));
				  //$explode = explode("- ", $explode[$explode_n]);
				  //$explode = explode('- ', $explode[$explode_n]);
				  if ($explode[$explode_n] != '') $sentence = $sentence."".$explode[$explode_n].". ";
				  $explode_n = $explode_n + $_GET['sentences'];
				}

				print "</table>";
			 }
		  
		  echo '<b>Final desc:</b><br><br><textarea name="description" style="width: 950px; height: 150px;">' .$sentence.'</textarea><br><br>';
		  
	  }
	  
	  error_reporting("E_ALL");
	  
	  echo '<b>Specs:</b><br><br>';
	  
	  include("modules/icecat.php");
	  
	  echo getICEcatProductSpecs($_GET['EAN'], 0, 1);
	  
	  echo '<br><br><textarea name="specs" style="width: 950px; height: 150px;">';
	  
	 echo getICEcatProductSpecs($_GET['EAN'], 0, 1);
	 
	 echo '</textarea><br><br>';
	 echo '<button type="submit" name="submit" class="pta_button small radius success" value="Send">Send</button></form>';
  
 require("admin_footer.php");
	
?>