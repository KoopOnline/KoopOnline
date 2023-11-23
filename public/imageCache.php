<?php
  //require("old/includes/common.php");
  $config_automationHandler = "auto";
  $imageCacheDir = "cache/"; // set to writable folder for image cache storage
  $imageCacheTime = 3628800;  // cache period in seconds, set to zero for proxy only mode (3628800 = 1.5 months)
  $imageCacheResize = 180; // set to resize dimension to enable  e.g. $imageCacheResize = 250;
  function cacheFetch_auto($src)
  {
    if (function_exists("curl_init"))
    {
      return cacheFetch_curl($src);
    }
    else
    {
      return cacheFetch_php($src);
    }
  }
  function cacheFetch_curl($src)
  {
    $ch = curl_init($src);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);
	curl_setopt( $ch, CURLOPT_USERAGENT, "KoopOnline Bot" );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $img = curl_exec($ch);
    return $img;
  }
  function cacheFetch_php($src)
  {
    return file_get_contents($src);
  }
  function cacheFetch($src)
  {
    global $config_automationHandler;
    global $imageCacheDir;
    global $imageCacheTime;
    global $imageCacheResize;
    $filename = $imageCacheDir.md5($src).".jpg";
    $fetch = (
      (!$imageCacheTime)
      ||
      (!file_exists($filename))
      ||
      (filemtime($filename) < (time()-$imageCacheTime))
      );
    if ($fetch)
    {
      //$cacheFetch_fn = "cacheFetch_".$config_automationHandler;
	  $cacheFetch_fn = "cacheFetch_curl";
      $img = $cacheFetch_fn($src);
    }
    else
    {
      $img = file_get_contents("".$filename."");
    }
    if (!$res = @imagecreatefromstring($img)) return FALSE;
    if ($imageCacheResize)
    {
      $oldX = imagesx($res);
      $oldY = imagesy($res);
      $new = imagecreatetruecolor($imageCacheResize,$imageCacheResize);
      $newBackground = imagecolorallocate($new,255,255,255);
      imagefill($new,0,0,$newBackground);
      if ($oldX > $oldY)
      {
        $newX = $imageCacheResize;
        $xMultiplier = ($newX / $oldX);
        $newY = intval($oldY * $xMultiplier);
        $dstX = 0;
        $dstY = ($imageCacheResize / 2) - ($newY / 2);
      }
      else
      {
        $newY = $imageCacheResize;
        $yMultiplier = ($newY / $oldY);
        $newX = intval($oldX * $yMultiplier);
        $dstX = ($imageCacheResize/2)-($newX/2);
        $dstY = 0;
      }
      imagecopyresampled($new,$res,$dstX,$dstY,0,0,$newX,$newY,$oldX,$oldY);
      ob_start();
      imagejpeg($new, NULL, 50);
      $img = ob_get_contents();
      ob_end_clean();
    }
    if ($fetch && $imageCacheTime)
    {
      $fp = fopen($filename,"w");
      fwrite($fp,$img);
      fclose($fp);
    }
    return $img;
  }
   $src = str_replace(' ', '%20', base64_decode($_GET["src"])); //inclu %20 hack
  if (!$img = cacheFetch($src))
  {
	$image = file_get_contents('imgs/productnietgevonden.jpg');
	header('Content-type: image/jpeg;');
	header("Content-Length: " . strlen($image));
	echo $image;
    exit();
  }
  else
  {
    header("Content-Type: image/jpeg");
	//header('Last-Modified: '.gmdate('r', time()));
	//header('Expires: '.gmdate('r', time() + 18000));
    print $img;
    exit();
  }
?>
