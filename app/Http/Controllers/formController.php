<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Unique;
use SplTempFileObject;
use ZipArchive;

class formController extends Controller
{
    public function form(Request $request) {

        $dataForm = $request->all();


        $validation = Validator::make([
            'name' => $dataForm['name'],
            'surname' => $dataForm['surname'],
            'position' => $dataForm['position'],
            'number' => $dataForm['number']
        ],
        [
            'name' => 'required|min:3|max:50|string',
            'surname' => 'required|min:3|max:50|string',
            'position' => 'required|min:1|max:100|string',
            'number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|size:12'
        ]);

        if ($validation->fails())
        {
            dd($validation->errors()->all());

        }else{

            $dataForm['number'] = wordwrap($dataForm['number'], 3, ' ', true);
//----------------------------------------------------------------------------------------


            $tmpfileUrl = sys_get_temp_dir();

            $uniqueId= uniqid();
            $url = "$tmpfileUrl/$uniqueId";
            mkdir($url);
            if(empty("$tmpfileUrl/zip")) {
                mkdir("$tmpfileUrl/zip");
            }

            File::copyDirectory(storage_path('test'), $url);


            //-------------------------------------------------------------


            $this->update($dataForm, $url, ['SunGroup.html', 'SunGroup.txt', 'SunGroup.rtf']);


            //-------------------------------------------------------------


            $zip = new ZipArchive;
            $fileName = $uniqueId.".zip";
            if($zip->open("$tmpfileUrl/zip/$fileName", ZipArchive::CREATE) == TRUE) {

                $files = File::files($url);
                    foreach ($files as $key => $value) {
                        $relativeName = basename($value);
                        $zip->addFile($value, $relativeName);
                    }
                    $zip->addEmptyDir('SunGroup_pliki');

                    $a = File::files("$url/SunGroup_pliki");
                    foreach($a as $key =>$value) {
                        $relativeName = basename($value);
                        $zip->addFile($value, "SunGroup_pliki/$relativeName");
                    }
                    $zip->close();
                }
                return response()->download("$tmpfileUrl/zip/$fileName");
            }


//----------------------------------------------------------------------------------------

            /*
            $this->updateTestCopy($dataForm, ['SunGroup.html','SunGroup.txt'], $uniqid);

            //--------------------------------------------------------
                $zip = new ZipArchive;
                $fileName = 'zipFile.zip';

                if($zip->open(storage_path($fileName), ZipArchive::CREATE) == TRUE)
                {
                    $files = File::files(storage_path("catalogs/$uniqid"));
                    foreach ($files as $key => $value) {
                        $relativeName = basename($value);
                        $zip->addFile($value, $relativeName);
                    }
                    $zip->close();
                }
                return response()->download(storage_path($fileName));
            //--------------------------------------------------------
            */

            //return response()->view('dir.first', ['data' => $dataForm]);
        }

        //return view('welcome',['data' => $dataForm]);

        public function update(array $dataForm ,string $url, array $file)
        {
            $count = count($file);

            for($i=0; $i<$count; $i++) {

                $string = file_get_contents("$url/$file[$i]");

                $strreplace = str_replace([
                    'imie','nazwisko','stanowisko','telefon'
                ],
                [
                    $dataForm['name'], $dataForm['surname'], $dataForm['position'],$dataForm['number']
                ], $string);

                file_put_contents("$url/$file[$i]", $strreplace);

                }
        }
    }
/*
    public function updateTestCopy(array $dataForm, array $nameStr,string $uniqid)
    {
        $count= count($nameStr);

        for($i=0; $i<$count; $i++) {
            $url = "../storage/catalogs/$uniqid/$nameStr[$i]";

            $string = file_get_contents($url);
        dd($string);
            $strreplace = str_replace([
                'imie','nazwisko','stanowisko','telefon'
            ],
            [
                $dataForm['name'], $dataForm['surname'], $dataForm['position'],$dataForm['number']
            ], $string);

            file_put_contents($url, $strreplace);
        }
    }
*/

