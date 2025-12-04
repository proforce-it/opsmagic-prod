<?php


namespace App\Helper\File;


use Illuminate\Support\Facades\Response;

class FileHelper
{
    public static function fileUploadEmptyArray()
    {
        return [
            'file_name' => '',
            'file_type' => ''
        ];
    }
    public static function file_upload($file, $upload_path) {
        $file_name  = time().'_'.$file->getClientOriginalName();
        $file_type  = $file->getMimeType();
        $file->move(public_path($upload_path), $file_name);

        return [
            'file_original_name' => $file->getClientOriginalName(),
            'file_name' => $file_name,
            'file_type' => $file_type,
        ];
    }

    public static function file_remove($file_name, $file_path) {
        $url = public_path($file_path.'/'.$file_name);
        if(is_file($url))
            unlink($url);

        return [
            'success' => true,
            'message' => 'File removed.'
        ];
    }

    /*public static function file_download($document_name, $document_type, $download_path)
    {
        $file       = public_path($download_path.'/'.$document_name);
        $headers    = [
            'Content-Type: '.$document_type.'',
        ];

        return Response::download($file, $document_name, $headers);
    }*/
}
