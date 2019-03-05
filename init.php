<?

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


AddEventHandler("main",'OnFileSave','OnFileSave');
function OnFileSave(&$arFile, $strFileName, $strSavePath, $bForceMD5 = false, $bSkipExt = false, $dirAdd = '')
{
	//Working with path
	$strSavePath = $arFile['tmp_name'];						//original path from where is uploading 
															//for example if select from local PC /home/bitrix/www/upload/tmp/BXTEMP-2019-03-02/03/bxu/main/b3bb5d25665966cb038ca38a5ff65b25/file1551441900551/default
	$path_parts = pathinfo($strSavePath);					//array with dirname, basename, extension and filename

	if (strpos($path_parts['dirname'], '/home/bitrix/www/upload/medialibrary/') !== false)
	{
		$filename_with_points_and_extension = $path_parts['basename'];
		$array_filename = explode(".", $filename_with_points_and_extension);
		$count_array_filename = count($array_filename);
		while($count_array_filename--)
		{
			//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $count_array_filename. PHP_EOL, FILE_APPEND);
		}

		$count_array_filename = count($array_filename);

		$clear_filename_without_extension =  $array_filename[$count_array_filename-2];
		//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $clear_filename_without_extension. PHP_EOL, FILE_APPEND);

		$extension =  $array_filename[$count_array_filename-1];
		//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $extension. PHP_EOL, FILE_APPEND);

		$filename_with_extension =  $clear_filename_without_extension . "." . $extension;
		//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $filename_with_extension. PHP_EOL, FILE_APPEND);

		$i = -1;
		$resultPath = '';
		while ($i++ != $count_array_filename-1)
		{			
			$item_Path = $array_filename[$i];
			if ($item_Path == $clear_filename_without_extension)
			{
				//$resultPath .= $filename_with_extension;
				break;
			}
			$resultPath .= $item_Path . '/';
			//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $resultPath. PHP_EOL, FILE_APPEND);
			//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $i. PHP_EOL, FILE_APPEND);
		}

		//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", $resultPath. PHP_EOL, FILE_APPEND);
	} 
	else 
	{
		//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", print_r("не найдено", true), FILE_APPEND);
		return false;
	}

	$resultPathWithName = $resultPath  . $filename_with_extension;
	$arFile["SUBDIR"] = $resultPath; 
	$arFile["FILE_NAME"] = $filename_with_extension;  

	CheckDirPath("/home/bitrix/www/upload/" . $resultPath); // creating new path if not exist

	$result_full_path = "/home/bitrix/www/upload/" . $resultPathWithName;
	//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", "result_full_path is: " . $result_full_path, FILE_APPEND);

	//not needed, but let's stay here in case if $arFile have field with content
	if(is_set($arFile, "content"))
	{
		$f = fopen($resultPathWithName, "w");
		if(!$f)
			return false;
		if(fwrite($f, $arFile["content"]) === false)
			return false;
		fclose($f);
	}
	elseif(
		!copy($arFile["tmp_name"], $result_full_path)
		&& !move_uploaded_file($arFile["tmp_name"], $resultPathWithName)
	) //move_uploaded_file return true only if file was uploaded throw PHP
	{
		CFile::DoDelete($arFile["old_file"]);
		return false;
		//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", "WAS NOT return FALSE", FILE_APPEND);
	}

	if(isset($arFile["old_file"]))
	{
		CFile::DoDelete($arFile["old_file"]);
	}
	//define("BX_FILE_PERMISSIONS", 0664);
	//define("BX_FILE_PERMISSIONS_NEW", 0777);
	@chmod($resultPathWithName, BX_FILE_PERMISSIONS);
	//file_put_contents($_SERVER["DOCUMENT_ROOT"]."/log.txt", BX_FILE_PERMISSIONS, FILE_APPEND);

	//flash is not an image
	$flashEnabled = !CFile::IsImage($arFile["ORIGINAL_NAME"], $arFile["type"]);

	$imgArray = CFile::GetImageSize($result_full_path, false, $flashEnabled);

	if(is_array($imgArray))
	{
		$arFile["WIDTH"] = $imgArray[0];
		$arFile["HEIGHT"] = $imgArray[1];

		if($imgArray[2] == IMAGETYPE_JPEG)
		{
			$exifData = CFile::ExtractImageExif($result_full_path);
			if ($exifData  && isset($exifData['Orientation']))
			{
				//swap width and height
				if ($exifData['Orientation'] >= 5 && $exifData['Orientation'] <= 8)
				{
					$arFile["WIDTH"] = $imgArray[1];
					$arFile["HEIGHT"] = $imgArray[0];
				}

				$properlyOriented = CFile::ImageHandleOrientation($exifData['Orientation'], $result_full_path);
				if ($properlyOriented)
				{
					$jpgQuality = intval(COption::GetOptionString('main', 'image_resize_quality', '95'));
					if($jpgQuality <= 0 || $jpgQuality > 100)
						$jpgQuality = 95;

					imagejpeg($properlyOriented, $strPhysicalFileNameX, $jpgQuality);
					clearstatcache(true, $strPhysicalFileNameX);
				}

				$arFile['size'] = filesize($result_full_path);
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

return true;

}




?>
