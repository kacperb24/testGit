<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ZipController extends Controller
{
    public function download()
    {
        $zip = new ZipArchive;
        $fileName = 'zipFile.zip';

        if($zip->open(storage_path($fileName), ZipArchive::CREATE) == TRUE)
        {
            $files = File::files(storage_path('testCopy'));
            foreach ($files as $key => $value) {
                $relativeName = basename($value);
                $zip->addFile($value, $relativeName);
            }
            $zip->close();
        }
        return response()->download($fileName);
    }
}
