<?
//error_reporting(E_STRICT);
//ini_set('display_errors','On');

//define("LOG_FILENAME", $_SERVER["DOCUMENT_ROOT"]."/log.txt");

define('CATALOG_IBLOCK_ID', 2);
define('STORES_IBLOCK_ID', 4);

if (isset($_GET['noinit']) && !empty($_GET['noinit']))
{
	$strNoInit = strval($_GET['noinit']);
	if ($strNoInit == 'N')
	{
		if (isset($_SESSION['NO_INIT']))
			unset($_SESSION['NO_INIT']);
	}
	elseif ($strNoInit == 'Y')
	{
		$_SESSION['NO_INIT'] = 'Y';
	}
}

if (!(isset($_SESSION['NO_INIT']) && $_SESSION['NO_INIT'] == 'Y'))
{
	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/config/common_func.inc.php"))
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/config/common_func.inc.php");

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/config/individ_classes/CICatalog.php"))
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/config/individ_classes/CICatalog.php");

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/config/individ_classes/CISuggest.php"))
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/config/individ_classes/CISuggest.php");

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/config/individ_classes/CINews.php"))
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/config/individ_classes/CINews.php");

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/config/individ_classes/CIComplain.php"))
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/config/individ_classes/CIComplain.php");

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/config/PropSearch.php"))
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/config/PropSearch.php");

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/sent_post.php"))
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/sent_post.php");

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/app_counter.php"))
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/app_counter.php");

	if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/order_service.php"))
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/order_service.php");

}

/*
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/config/common_func.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/config/individ_classes/CICatalog.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/config/individ_classes/CISuggest.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/config/individ_classes/CINews.php");
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/config/individ_classes/CIComplain.php");
*/
/*Проверка If-Modified-Since и вывод 304 Not Modified */
AddEventHandler('main', 'OnEpilog', array('CBDPEpilogHooks', 'CheckIfModifiedSince'));
class CBDPEpilogHooks
{
    function CheckIfModifiedSince()
    {
        GLOBAL $lastModified;
        
        if ($lastModified)
        {
			//header("Cache-Control: no-cache");
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified) {
                $GLOBALS['APPLICATION']->RestartBuffer();CHTTP::SetStatus('304 Not Modified');
                exit();
                /*header('HTTP/1.1 304 Not Modified');
                exit;*/
            }
        }
    }
}


AddEventHandler("main",'OnBeforeResizeImage','OnBeforeResizeImageFunc');
function OnBeforeResizeImageFunc($arFile, $arResizeParams, &$callbackData, &$bNeedResize, &$sourceImageFile, &$cacheImageFileTmp)
{
   ?><!--<pre><?print_r($arFile);?></pre><?
	?><pre><?print_r($arResizeParams);?></pre><?
	?><pre><?print_r($callbackData);?></pre><?
	?><pre><?print_r($bNeedResize);?></pre><?
	?><pre><?print_r($sourceImageFile);?></pre><?
	?><pre><?print_r($cacheImageFileTmp);?></pre>--><?
	//exit();
}


AddEventHandler("main",'OnAfterResizeImage','OnAfterResizeImageFunc');
function OnAfterResizeImageFunc($arFile, $arResizeParams, &$callbackData, &$cacheImageFile, &$cacheImageFileTmp, &$arImageSize)
{
	?><script>
	//alert('OnAfterResizeImageFunc');
	//document.location.href='/personal/cart/';
	</script><?

   ?><!--<pre><?print_r($arFile);?></pre><?
	?><pre><?print_r($arResizeParams);?></pre><?
	?><pre><?print_r($callbackData);?></pre><?
	?><pre><?
	//print_r($cacheImageFile);
	//echo nl2br("\r\n");
	//$cacheImageFile = '/' . $arFile['FILE_NAME'];
	//print_r($cacheImageFile);

?></pre><?
	?><pre><?print_r($cacheImageFileTmp);?></pre><?
	?><pre><?print_r($arImageSize);?></pre>--><?
	//exit();
}


AddEventHandler("main",'OnFileSave','OnFileSave');
function OnFileSave(&$arFile, $strFileName, $strSavePath, $bForceMD5 = false, $bSkipExt = false, $dirAdd = '')
{

	file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", print_r($arFile, true), FILE_APPEND);

	//$upload_dir = "/upload/";
	$upload_dir = "";

	//Working with path
	$strSavePath = $arFile['tmp_name'];						//original path from where is uploading 
															//for example if select from local PC /home/bitrix/www/upload/tmp/BXTEMP-2019-03-02/03/bxu/main/b3bb5d25665966cb038ca38a5ff65b25/file1551441900551/default
	$path_parts = pathinfo($strSavePath);					//array with dirname, basename, extension and filename

	if (strpos($path_parts['dirname'], 'upload') !== false)
	{
		$position = strpos($path_parts['dirname'], 'upload') . PHP_EOL;  //==17 if path start with /home/bitrix/www/upload
		$message = print_r("найдено", true) . PHP_EOL;
		file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $message, FILE_APPEND);
		file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $position, FILE_APPEND);
		$resultPath = substr($path_parts['dirname'], intval($position) + 6);
		file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $resultPath, FILE_APPEND);


	} 
	else 
	{
		file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", print_r("не найдено", true));
	}

/*
	$arFile["SUBDIR"] = dirname($strSavePath); 
	$arFile["FILE_NAME"] = $strFileName;

	//$strDirName = $_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/".$strSavePath."/";
	$strDirName = $_SERVER["DOCUMENT_ROOT"]."/".$upload_dir."/";
	$strDbFileNameX = $strDirName.$strFileName;
	$strPhysicalFileNameX = $io->GetPhysicalName($strDbFileNameX);

	CheckDirPath($strDirName);

	if(is_set($arFile, "content"))
	{
		$f = fopen($strPhysicalFileNameX, "w");
		if(!$f)
			return false;
		if(fwrite($f, $arFile["content"]) === false)
			return false;
		fclose($f);
	}
	elseif(
		!copy($arFile["tmp_name"], $strPhysicalFileNameX)
		&& !move_uploaded_file($arFile["tmp_name"], $strPhysicalFileNameX)
	)
	{
		CFile::DoDelete($arFile["old_file"]);
		return false;
	}

	if(isset($arFile["old_file"]))
		CFile::DoDelete($arFile["old_file"]);

	@chmod($strPhysicalFileNameX, BX_FILE_PERMISSIONS);

	//flash is not an image
	$flashEnabled = !CFile::IsImage($arFile["ORIGINAL_NAME"], $arFile["type"]);

	$imgArray = CFile::GetImageSize($strDbFileNameX, false, $flashEnabled);

	if(is_array($imgArray))
	{
		$arFile["WIDTH"] = $imgArray[0];
		$arFile["HEIGHT"] = $imgArray[1];

		if($imgArray[2] == IMAGETYPE_JPEG)
		{
			$exifData = CFile::ExtractImageExif($strPhysicalFileNameX);
			if ($exifData  && isset($exifData['Orientation']))
			{
				//swap width and height
				if ($exifData['Orientation'] >= 5 && $exifData['Orientation'] <= 8)
				{
					$arFile["WIDTH"] = $imgArray[1];
					$arFile["HEIGHT"] = $imgArray[0];
				}

				$properlyOriented = CFile::ImageHandleOrientation($exifData['Orientation'], $io->GetPhysicalName($strDbFileNameX));
				if ($properlyOriented)
				{
					$jpgQuality = intval(COption::GetOptionString('main', 'image_resize_quality', '95'));
					if($jpgQuality <= 0 || $jpgQuality > 100)
						$jpgQuality = 95;

					imagejpeg($properlyOriented, $strPhysicalFileNameX, $jpgQuality);
					clearstatcache(true, $strPhysicalFileNameX);
				}

				$arFile['size'] = filesize($strPhysicalFileNameX);
			}
		}
	}
	else
	{
		$arFile["WIDTH"] = 0;
		$arFile["HEIGHT"] = 0;
	}

	if($arFile["WIDTH"] == 0 || $arFile["HEIGHT"] == 0)
	{
		//mock image because we got false from CFile::GetImageSize()
		if(strpos($arFile["type"], "image/") === 0 && $arFile["type"] <> 'image/svg+xml')
		{
			$arFile["type"] = "application/octet-stream";
		}
	}

	if($arFile["type"] == '' || !is_string($arFile["type"]))
	{
		$arFile["type"] = "application/octet-stream";
	}

	//QUOTA 
	if (COption::GetOptionInt("main", "disk_space") > 0)
	{
		CDiskQuota::updateDiskQuota("file", $arFile["size"], "insert");
	}
	// QUOTA 

	$NEW_IMAGE_ID = CFile::DoInsert(array(
		"HEIGHT" => $arFile["HEIGHT"],
		"WIDTH" => $arFile["WIDTH"],
		"FILE_SIZE" => $arFile["size"],
		"CONTENT_TYPE" => $arFile["type"],
		"SUBDIR" => $arFile["SUBDIR"],
		"FILE_NAME" => $arFile["FILE_NAME"],
		"MODULE_ID" => $arFile["MODULE_ID"],
		"ORIGINAL_NAME" => $arFile["ORIGINAL_NAME"],
		"DESCRIPTION" => isset($arFile["description"])? $arFile["description"]: '',
		"HANDLER_ID" => isset($arFile["HANDLER_ID"])? $arFile["HANDLER_ID"]: '',
		"EXTERNAL_ID" => isset($arFile["external_id"])? $arFile["external_id"]: md5(mt_rand()),
	));

	CFile::CleanCache($NEW_IMAGE_ID);
*/


return true;

	//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", print_r($strFileName, true));		//just name

	//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $arFile);

	//echo "<pre>";
	//echo mydump($USER);
	//echo "</pre>";

	//print_r(Debug::getTimeLabels());
	//print_r(Debug::writeToFile($_SERVER["DOCUMENT_ROOT"]."/log.txt"));

	//AddMessage2Log("Произвольный текст сообщения", "init");

	//ShowMessage("Ошибка! Вы забыли заполнить обязательные поля!");
	//$APPLICATION->SetTitle("О компании");
	//throw new CTaskAssertException();
	//$e = new CAdminException();

	//$APPLICATION->throwException($e);

	/*$arNewFile = CIBlock::ResizePicture($arFile, array("WIDTH" => 20, "HEIGHT" => 20, "METHOD" => "resample"));
   if(is_array($arNewFile))
      $arFile = $arNewFile;
   else
      $APPLICATION->throwException("Ошибка масштабирования изображения в свойстве \"Файлы\":".$arNewFile);
	*/
}







/*public static function OnFileSave(&$arFile, $strFileName, $strSavePath, $bForceMD5 = false, $bSkipExt = false, $dirAdd = '')
{
		if (!$arFile["tmp_name"] && !array_key_exists("content", $arFile))
			return false;

		if (array_key_exists("bucket", $arFile))
			$bucket = $arFile["bucket"];
		else
			$bucket = CCloudStorage::FindBucketForFile($arFile, $strFileName);

		if (!is_object($bucket))
			return false;

		if (!$bucket->Init())
			return false;

		$copySize = false;
		$subDir = "";
		$filePath = "";

		if (array_key_exists("content", $arFile))
		{
			$arFile["tmp_name"] = CTempFile::GetFileName($arFile["name"]);
			CheckDirPath($arFile["tmp_name"]);
			$fp = fopen($arFile["tmp_name"], "ab");
			if ($fp)
			{
				fwrite($fp, $arFile["content"]);
				fclose($fp);
			}
		}

		if (array_key_exists("bucket", $arFile))
		{
			$newName = bx_basename($arFile["tmp_name"]);

			$prefix = $bucket->GetFileSRC("/");
			$subDir = substr($arFile["tmp_name"], strlen($prefix));
			$subDir = substr($subDir, 0, -strlen($newName) - 1);
		}
		else
		{
			if (
				$bForceMD5 != true
				&& COption::GetOptionString("main", "save_original_file_name", "N") == "Y"
			)
			{
				if (COption::GetOptionString("main", "convert_original_file_name", "Y") == "Y")
					$newName = CCloudStorage::translit($strFileName);
				else
					$newName = $strFileName;
			}
			else
			{
				$strFileExt = ($bSkipExt == true? '': strrchr($strFileName, "."));
				$newName = md5(uniqid(mt_rand(), true)).$strFileExt;
			}

			//check for double extension vulnerability
			$newName = RemoveScriptExtension($newName);
			$dir_add = $dirAdd;

			if (empty($dir_add))
			{
				while (true)
				{
					$dir_add = md5(mt_rand());
					$dir_add = substr($dir_add, 0, 3)."/".$dir_add;

					$subDir = trim($strSavePath, "/")."/".$dir_add;
					$filePath = "/".$subDir."/".$newName;

					if (!$bucket->FileExists($filePath))
						break;
				}
			}
			else
			{
				$subDir = trim($strSavePath, "/")."/".$dir_add;
				$filePath = "/".$subDir."/".$newName;
			}

			$targetPath = $bucket->GetFileSRC("/");
			if (strpos($arFile["tmp_name"], $targetPath) === 0)
			{
				$arDbFile = array(
					"SUBDIR" => "",
					"FILE_NAME" => substr($arFile["tmp_name"], strlen($targetPath)),
					"CONTENT_TYPE" => $arFile["type"],
				);
				$copyPath = $bucket->FileCopy($arDbFile, $filePath);
				if (!$copyPath)
					return false;

				$copySize = $bucket->GetFileSize("/".urldecode(substr($copyPath, strlen($targetPath))));
			}
			else
			{
				$imgArray = CFile::GetImageSize($arFile["tmp_name"], true, false);
				if (is_array($imgArray) && $imgArray[2] == IMAGETYPE_JPEG)
				{
					$exifData = CFile::ExtractImageExif($arFile["tmp_name"]);
					if ($exifData && isset($exifData['Orientation']))
					{
						$properlyOriented = CFile::ImageHandleOrientation($exifData['Orientation'], $arFile["tmp_name"]);
						if ($properlyOriented)
						{
							$jpgQuality = intval(COption::GetOptionString('main', 'image_resize_quality', '95'));
							if ($jpgQuality <= 0 || $jpgQuality > 100)
								$jpgQuality = 95;

							imagejpeg($properlyOriented, $arFile["tmp_name"], $jpgQuality);
							clearstatcache(true, $arFile["tmp_name"]);
						}
						$arFile['size'] = filesize($arFile["tmp_name"]);
					}
				}

				if (!$bucket->SaveFile($filePath, $arFile))
					return false;
			}
		}

		$arFile["HANDLER_ID"] = $bucket->ID;
		$arFile["SUBDIR"] = $subDir;
		$arFile["FILE_NAME"] = $newName;
		$arFile["WIDTH"] = 0;
		$arFile["HEIGHT"] = 0;

		if (array_key_exists("bucket", $arFile))
		{
			$arFile["WIDTH"] = $arFile["width"];
			$arFile["HEIGHT"] = $arFile["height"];
			$arFile["size"] = $arFile["file_size"];
		}
		elseif ($copySize !== false)
		{
			$arFile["WIDTH"] = $arFile["width"];
			$arFile["HEIGHT"] = $arFile["height"];
			$arFile["size"] = $copySize;
			$bucket->IncFileCounter($copySize);
		}
		else
		{
			$bucket->IncFileCounter(filesize($arFile["tmp_name"]));
			$flashEnabled = !CFile::IsImage($arFile["ORIGINAL_NAME"], $arFile["type"]);
			$imgArray = CFile::GetImageSize($arFile["tmp_name"], true, $flashEnabled);
			if (is_array($imgArray))
			{
				$arFile["WIDTH"] = $imgArray[0];
				$arFile["HEIGHT"] = $imgArray[1];
			}
		}

		if (isset($arFile["old_file"]))
			CFile::DoDelete($arFile["old_file"]);

	return true;
}*/





?>
