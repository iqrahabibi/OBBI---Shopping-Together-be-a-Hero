<?php
/*
 //
 //    ______    _   _           _  __      _   _     ____   ___
 //   |  ____|  | | | |         | |/ _|    | | | |   |___ \ / _ \
 //   | |__ __ _| |_| |__   __ _| | |_ __ _| |_| |__   __) | | | |
 //   |  __/ _` | __| '_ \ / _` | |  _/ _` | __| '_ \ |__ <| | | |
 //   | | | (_| | |_| | | | (_| | | || (_| | |_| | | |___) | |_| |
 //   |_|  \__,_|\__|_| |_|\__,_|_|_| \__,_|\__|_| |_|____/ \___/
 //
 // Licensed under GNU General Public License v3.0
 // http://www.gnu.org/licenses/gpl-3.0.txt
 // Written by Fathalfath30. Email : fathalfath30@gmail.com
 // Follow me on GithHub : https://github.com/Fathalfath30
 //
 // For the brave souls who get this far: You are the chosen ones,
 // the valiant knights of programming who toil away, without rest,
 // fixing our most awful code. To you, true saviors, kings of men,
 //
 // I say this: never gonna give you up, never gonna let you down,
 // never gonna run around and desert you. Never gonna make you cry,
 // never gonna say goodbye. Never gonna tell a lie and hurt you.
 //
*/

namespace App\Helper;

use Illuminate\Http\Request;
use Validator;

class FileUploader {
    const USER_PROFILE = 0;
    const SLIDER = 3;
    const BARANG = 4;

    private $field;
    private $type;
    private $mime;
    private $path;
    private $id;
    private $max_upload_size = 5120; // 5 MB

    public function getField () {
        return trim($this->field);
    }

    public function setField ($field) {
        $this->field = trim($field);

        return $this;
    }

    public function getType () {
        return $this->type;
    }

    public function setType ($type) {
        $this->type = trim($type);

        return $this;
    }

    public function getMime () {
        return $this->mime;
    }

    public function setMime (array $mime) {
        $this->mime = $mime;

        return $this;
    }

    public function getPath () {
        return $this->path;
    }

    public function setPath ($path) : void {
        $this->path = trim($path);
    }

    public function getId () {
        return $this->id;
    }

    public function setId ($id) {
        $this->id = $id;
    }

    public function getMaxUploadSize () : int {
        return $this->max_upload_size;
    }

    public function setMaxUploadSize (int $max_upload_size) {
        $this->max_upload_size = $max_upload_size;

        return $this;
    }

    public function __construct ($id, int $type = null, $field = 'file') {
        if ( !is_null($type) ) {
            $this->setType($type);
        };

        $this->setField($field);
        if ( trim($id) == "" ) {
            $this->setId(date("YmdHis"));
        }
        switch ( $type ) {
            case self::USER_PROFILE:
                $this->setPath(storage_path('app/public/profile/'));
                break;
            case self::USER_HEROBI:
                $this->setPath(storage_path('app/public/herobi/'));
                break;
            case self::USER_OPF:
                $this->setPath(storage_path('app/public/opf/'));
                break;
            case self::SLIDER:
                $this->setPath(storage_path('app/public/slider/'));
                break;
            case self::BARANG:
                $this->setPath(storage_path('app/public/barang/'));
                break;
            default:
                return [
                    'meta'    => 500,
                    'message' => 'Tipe belum di dukung.'
                ];
        }
    }

    public function __get_class_info () {
        return get_object_vars($this);
    }

    public function doUpload (Request $request) {
        if ( !$request->hasFile($this->getField()) ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Tidak ada file yang dapat di upload'
                ]
            ];
        }

        $file = $request->file($this->getField());
        if ( !in_array($file->getMimeType(), $this->getMime()) ) {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => 'Format file tidak sesuai.'
                ]
            ];
        }

        $file_path = md5(str_random(40) . $this->getId() . date('YmdHis')) . "." . $file->getClientOriginalExtension();
        $file->move($this->getPath(), $file_path);

        if ( $file->getError() == UPLOAD_ERR_OK ) {
            return [
                'meta' => [
                    'code'    => 200,
                    'message' => ''
                ],
                'data' => [
                    'path' => $file_path
                ]
            ];
        } else {
            return [
                'meta' => [
                    'code'    => 500,
                    'message' => $file->getErrorMessage()
                ]
            ];
        }
    }
}
