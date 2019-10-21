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

use File;
use Response;
use Image;

class ObbiAssets {
    const USER_PROFILE = 0;
    const USER_HEROBI = 1;
    const USER_OPF = 2;
    const SLIDER = 3;
    const BARANG = 4;

    private static function clear_db_path ($location) {
        $location = str_replace("https://api.obbi.id/", "", $location);
        $location = str_replace("http://api.obbi.id/", "", $location);
        $location = str_replace("http://api.devobbi.com/", "", $location);
        $location = str_replace("https://api.devobbi.id/", "", $location);
        $location = str_replace("https://manage.obbi.id/", "", $location);
        $location = str_replace("http://manage.obbi.id/", "", $location);
        $location = str_replace("storage/profile/", "", $location);
        $location = str_replace("storage/herobi/", "", $location);
        $location = str_replace("storage/opf/", "", $location);
        $location = str_replace("//opf/", "", $location);
        $location = str_replace("storage/slider/", "", $location);
        $location = str_replace("storage/barang/", "", $location);
        $location = str_replace("/barang/", "", $location);
        $location = str_replace("img/profile/", "", $location);
        $location = str_replace("img/herobi/", "", $location);
        $location = str_replace("/opf/", "", $location);

        return trim($location);
    }

    public static function getPath ($type) {
        switch ( $type ) {
            case self::USER_PROFILE:
                return 'storage/profile/';
                break;
            case self::USER_HEROBI:
                return 'storage/herobi/';
                break;
            case self::USER_OPF:
                return 'storage/opf/';
                break;
            case self::SLIDER:
                return 'storage/slider/';
                break;
            case self::BARANG:
                return 'storage/barang/';
                break;
            default:
                return [
                    'meta'    => 500,
                    'message' => 'Tipe belum di dukung.'
                ];
        }
    }

    public static function delete_asset ($type, $location) {
        $location = self::clear_db_path($location);
        $path = trim(self::getPath($type) . $location);

        if ( File::exists($path) ) {
            File::delete($path);
        }
    }

    public static function get_asset ($type, $location) {
        $location = self::clear_db_path($location);
        if ( trim($location) == "" )
            return "";
        $path = asset(self::getPath($type) . $location);

        return $path;
    }
}
