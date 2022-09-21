<?php
namespace IjCore;

use Defuse\Crypto\File;

class IjFile {
    static function UploadFile($file, $folder, $filename){
        $linkFileAttach = '';
        if($file){
            $ext = strtolower($file->getClientOriginalExtension());
            $size = $file->getSize();
            $name = $file->getClientOriginalName();
            if($size <= 400000000){
                $arr_ext_img = ["gif", "jpeg", "jpg", "png", "ico", "psd", "ai"];
                $arr_ext_doc = ["pptx", "ppt", "pps", "xls", "xlsx", "csv", "doc", "docx", "pdf", "txt", "zip", "rar", "cif"];
                $is_doc = in_array($ext, $arr_ext_doc);
                $is_img = in_array($ext, $arr_ext_img);
                if($is_doc || $is_img){
                    $filename = $filename.".".$ext;
                    $linkFileAttach = '/'.$folder.'/'.$filename;
                    $file->move('files-attach', $filename);
                }
            }
        }
        return $linkFileAttach;
    }

    static function CreateZipArchive($files = [], $overwrite = false, $zipName = ''){
        if(!$overwrite) {
            return false;
        }
        $publicPath = public_path();

        $validFiles = array();
        if(is_array($files)) {
            foreach($files as $file) {
                $tmpArr = [];
                if(file_exists(public_path() . $file['Link'])) {
                    $tmpArr['Link'] = $publicPath . $file['Link'];
                    $tmpArr['FileName'] = $file['FileName'];
                    $validFiles[] = $tmpArr;
                }
            }
        }

        if(count($validFiles)) {
            $zip = new \ZipArchive();
            if (!$zipName) {
                $zipName = time() . ".zip";
            }else{
                $zipName .= '_' . time() . '.zip';
            }
            $zipPath = $publicPath . '/files-zip';
            if ( !is_dir( $zipPath ) ) {
                mkdir($zipPath);
            }

            // remove old file zip
            $oldFiles = scandir($zipPath . '/');
            $timeStamp = time();
            foreach($oldFiles as $oldFile) {
                //do your work here
                if ($oldFile != '.' && $oldFile != '..') {
                    $fileCreateDate = filemtime($zipPath . '/' . $oldFile);
                    if (($timeStamp - $fileCreateDate) > 600) {
                        unlink($zipPath . '/' . $oldFile);
                    }
                }
            }

            $destination = $zipPath . '/' . $zipName;
            if($zip->open($destination,  \ZipArchive::CREATE)) {
                foreach($validFiles as $file) {
                    $zip->addFile($file['Link'], $file['FileName']);
                }
                $zip->close();
                return '/files-zip/' . $zipName;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
