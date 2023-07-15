<?php

namespace App\Trait;
use Illuminate\Http\Request;

trait UploadMediaTrait{

    public function uploadMedia(Request $request , $folderName, $mediaType){

        $file = $request->file($mediaType);
        $media = str_replace(' ', '', $file->getClientOriginalName());
        $path = $request->file($mediaType)->storeAs($folderName, $media, 'departmentAdministration');
        return $path;
    }
}
