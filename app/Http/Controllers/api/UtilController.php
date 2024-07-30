<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class UtilController extends Controller
{
    public static function uploadImage($field, $destination)
    {
        if ($field) {
            $image = Image::make($field);
            $png_url = md5(rand(1000, 10000)) . ".png";
            $width = $image->width();
            $height = $image->height();
            $image->resize($width / 2, $height / 2); // Redimensionnement de l'image à 120 x 80 px
            $image->save(public_path() . $destination . $png_url);
            return $png_url;
        }
    }

    public static function removeImage($field, $destination)
    {
        if (File::exists(public_path($destination . $field))) {
            File::delete(public_path($destination . $field));
        }
    }
    public static function removeMultipleImage($field, $destination)
    {
        foreach ($field as $file) {
            if (File::exists(public_path($destination . $file->images))) {
                File::delete(public_path($destination . $file->images));
            }
        }
    }
    public static function uploadMultipleImage($field, $destination)
    {
        $images = [];
        if ($field) {
            foreach ($field as $file) {
                $image = Image::make($file);
                $png_url = md5(rand(1000, 10000)) . ".png";
                // $width = $image->width();
                // $height = $image->height();
                // $image->resize($width / 2, $height / 2); // Redimensionnement de l'image à 120 x 80 px
                $image->save(public_path() . $destination . $png_url);
                array_push($images, $png_url);
            }
        }

        return $images;
    }

    public static function uploadFile($field, $destination, $name)
    {
        if ($field) {
            $fileName = $name.".".$field->getClientOriginalExtension();
            $field->move(public_path().$destination,  $fileName);
            return $fileName;
        }
    }
}
