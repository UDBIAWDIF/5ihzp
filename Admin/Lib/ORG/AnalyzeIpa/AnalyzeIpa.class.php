<?php
//Enable Error Reporting If You Make Changes
//Location of CFProperyList framework
// require_once(dirname(__FILE__).'/CFPropertyList.php');

class AnalyzeIpa {
	//This function will process the ipa file and create a plist if needed
	public static function processIPA($ipaname) {
		//Check if PLIST exists for that ipa
		//TODO: Optionally check to see if the .ipa is newer than the .plist
		//	  and force it to re-create the .plist

		$format = end(explode(".",$ipaname));
		$ipa_plist_file = str_replace(".$format", ".plist", $ipaname);

		//Open .ipa zip
		$zip = new ZipArchive;
		if ($zip->open($ipaname) === TRUE) {
			for($i=0; $i<$zip->numFiles; $i++){
				$path_info = pathinfo($zip->getNameIndex($i));
				if($path_info['basename'] == "Info.plist" && preg_match('/Payload\/[^\/]+?\.app$/i', $path_info['dirname'])) {
					$bplist = $zip->getFromIndex($i);
					break;
				}
			}
			$zip->close();
		} else {
			echo 'Failure to open IPA';
		}

		//If we got the binary plist data, process it
		if (isset($bplist) && $bplist != '') {
			//read plist into CFPropertyList object
			$plist = new CFPropertyList();
			$plist->parse($bplist);

			return $plist->toArray();

		} else {
			echo 'Failure reading Info.plist';
		}
	}

	/**
	* @功能：恢复ipa包中的ICON.PNG图片
	*/
	public static function decodeIcon($ipaname, $icon_name='icon.png'){
		$zip = new ZipArchive;
		if($zip->open($ipaname) === TRUE) {
			//首先寻找符合iphone标准的512*512的大图
			$icon_png_index = $zip->locateName('iTunesArtwork');
			$icon_need_decode = false;
			if($icon_png_index === false) {
				$icon_png_index = $zip->locateName($icon_name, ZIPARCHIVE::FL_NODIR);
				$icon_need_decode = true;
				if($icon_png_index === false && strrpos($icon_name, '.png') === false) {
					$icon_name .= '.png';
					$icon_png_index = $zip->locateName($icon_name, ZIPARCHIVE::FL_NODIR);
				}
			}
			if($icon_png_index !== false) {
				$new_icon_png_name = getAttachName('.png', C('ATTACH_FOLDER_APP_ICON'));
				mkdirs(dirname(UPLOAD_PATH.$new_icon_png_name));
				$icon_content = $zip->getFromIndex($icon_png_index);
				if ($icon_need_decode === false) {
					file_put_contents(UPLOAD_PATH.$new_icon_png_name, $icon_content);
				} else {
					$new_icon_png_tmp_name = tempnam(TEMP_PATH, 'tmp');
					file_put_contents($new_icon_png_tmp_name, $icon_content);
					import('ImageDecode', LIB_PATH.'Tool');
					$img_decode = new ImageDecode($new_icon_png_tmp_name);
					if($img_decode->decode(UPLOAD_PATH.$new_icon_png_name)) {
						unlink($new_icon_png_tmp_name);
					}
				}

				$zip->close();
				return $new_icon_png_name;
			}

			$zip->close();
		}

		return false;
	}

	//Find all the .ipa files in the current folder
	//and process them.
	//TODO: Allow to point to another folder
	//$ipa = '218839bd6dc84f138cf18e0eb72af08c.ipa';

	//processIPA($ipa);
}//end class ManifestDestiny
?>