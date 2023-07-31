<?php
  function automation_run($job_id)
  {
    global $config_databaseTablePrefix;

    global $config_feedDirectory;

    global $config_filenameRegExp;

    global $config_voucherCodesFeedDirectory;

    global $config_automationHandler;

    global $config_automationUnzipPrograms;

    $status = "OK";

    $sql = "SELECT * FROM `".$config_databaseTablePrefix."jobs` WHERE id='".$job_id."'";

    if (!database_querySelect($sql,$jobs)) return FALSE;

    $job = $jobs[0];

    $directoryVar = "config_".$job["directory"]."Directory";

    if ($status == "OK")
    {
      if (!is_writable($$directoryVar))
      {
        $status = "ERROR - DIRECTORY NOT WRITABLE";
      }
    }

    if ($status == "OK")
    {
      $automation_handler = "automation_handler_".$config_automationHandler;

      if (!function_exists($automation_handler))
      {
        $status = "ERROR - INVALID HANDLER";
      }
    }

    if ($status == "OK")
    {
      if (!preg_match($config_filenameRegExp,$job["filename"]))
      {
        $status = "ERROR - INVALID FILENAME";
      }
    }

    if ($status == "OK")
    {
      $tmp = $$directoryVar."tmp";

      if (!$automation_handler($job["url"],$tmp))
      {
        if (file_exists($tmp)) unlink($tmp);

        $status = "ERROR - TRANSFER FAILED";
      }
    }

    if ($status == "OK")
    {
      if ($job["minsize"])
      {
        if (filesize($tmp) < intval($job["minsize"]))
        {
          unlink($tmp);

          $status = "ERROR - MIN SIZE ABORT";
        }
      }
    }
	
	//Dit hieronder was alleen voor de oude import, nu uitgezet want veroorzaakt mogelijk import problemen (11-06-2020)
  /*  if ($status == "OK") 
    {
	  if (filesize($tmp) == filesize($$directoryVar.$job["filename"])) {
	  
	  unlink($tmp);

		  $status = "STOP - SAME SIZE ABORT";
	  }

	} */

    if ($status == "OK")
    {
      if ($job["unzip"])
      {
        $unzipped = FALSE;

        foreach($config_automationUnzipPrograms as $k => $v)
        {
          $unzip_function = "automation_unzip_".$k;

          $result = $unzip_function($tmp);

          if ($result)
          {
            $unzipped = TRUE;

            break;
          }
        }

        if (!$unzipped)
        {
          unlink($tmp);

          $status = "ERROR - UNZIP FAILED";
        }
      }
    }

    if ($status == "OK")
    {
      rename($tmp,$$directoryVar.$job["filename"]);

      $sql = "UPDATE `".$config_databaseTablePrefix."jobs` SET lastrun='".time()."',status='OK' WHERE id='".$job_id."'";
    }
    else
    {
      $sql = "UPDATE `".$config_databaseTablePrefix."jobs` SET status='".$status."' WHERE id='".$job_id."'";
    }

    database_queryModify($sql,$result);

    return $status;
  }

  function automation_handler_auto($src,$dst)
  {
    if (function_exists("curl_init"))
    {
      return automation_handler_curl($src,$dst);
    }
    else
    {
      return automation_handler_php($src,$dst);
    }
  }

  function automation_handler_php($src,$dst)
  {
    return copy($src,$dst);
  }

  function automation_handler_curl($src,$dst)
  {
    $fp = fopen($dst,"w");

    $ch = curl_init($src);

    if (!ini_get('open_basedir')) curl_setopt($ch,CURLOPT_FOLLOWLOCATION,TRUE);

    curl_setopt($ch,CURLOPT_HEADER,0);

    curl_setopt($ch,CURLOPT_FILE,$fp);

    $retval = curl_exec($ch);

    fclose($fp);

    return $retval;
  }

  function automation_unzip_unzip($tmp)
  {
    global $config_automationUnzipPrograms;

    if (function_exists("zip_open"))
    {
      $z1 = zip_open($tmp);

      if (!is_resource($z1)) return FALSE;

      $z2 = zip_read($z1);

      if (!is_resource($z2)) return FALSE;

      $zf = zip_entry_open($z1,$z2);

      $fp = fopen($tmp.".unzipped","w");

      while($line = zip_entry_read($z2))
      {
        fwrite($fp,$line);
      }

      fclose($fp);

      zip_entry_close($z2);

      zip_close($z1);

      unlink($tmp);

      rename($tmp.".unzipped",$tmp);

      return TRUE;
    }
    else
    {
      $fp = fopen($tmp,"r");

      $header = fread($fp,4);

      fclose($fp);

      if ($header <> "PK".chr(0x03).chr(0x04)) return FALSE;

      $cmd = $config_automationUnzipPrograms["unzip"]." -p ".$tmp." > ".$tmp.".unzipped";

      exec($cmd);

      unlink($tmp);

      rename($tmp.".unzipped",$tmp);

      return TRUE;
    }
  }

  function automation_unzip_gzip($tmp)
  {
    global $config_automationUnzipPrograms;

    if (function_exists("gzopen"))
    {
      $gfp = gzopen($tmp,"r");

      if (!is_resource($gfp)) return FALSE;

      $fp = fopen($tmp.".ungzipped","w");

      while(!gzeof($gfp)) fwrite($fp,gzread($gfp,2048));

      fclose($fp);

      gzclose($gfp);

      unlink($tmp);

      rename($tmp.".ungzipped",$tmp);

      return TRUE;
    }
    else
    {
      $cmd = $config_automationUnzipPrograms["gzip"]." -c -d ".$tmp." > ".$tmp.".ungzipped";

      exec($cmd);

      if (filesize($tmp.".ungzipped"))
      {
        unlink($tmp);

        rename($tmp.".ungzipped",$tmp);

        return TRUE;
      }
      else
      {
        unlink($tmp.".ungzipped");

        return FALSE;
      }
    }
  }
?>