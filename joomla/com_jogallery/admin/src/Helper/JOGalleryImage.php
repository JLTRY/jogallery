<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\Helper;

use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JODirectory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Log\Log;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Layout\LayoutHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects;

class JOGalleryImage
{
    public static $IMG_EXTENSIONS = array("jpg", "JPG", "jpeg", "JPEG", "png");
    public static $VIDEO_EXTENSIONS = array("mp4", "m2ts", "MOV", "mov");
    public $filename;
    public $basename;
    public $moddate;
    public $urlfilename;
    public $urlshortfilename;
    public $comment;
    public $dirname;
    public $relative;
    public $video;

    public function __construct(
        $dirname,
        $filename,
        $basename,
        $moddate,
        $comment = "",
        $urlfilename = "",
        $urlshortfilename = "",
        $relative = false
    ) {
        $this->dirname =  str_replace(JPATH_SITE, "", $dirname);
        $this->filename = $filename;
        $this->basename = $basename;
        $this->moddate = $moddate;
        $this->comment = $comment;
        $this->relative = $relative;
        $this->urlfilename = $urlfilename;
        $this->urlshortfilename = $urlshortfilename;
        $this->video = false;
        $fileext = pathinfo($basename)['extension'];
        foreach (self::$VIDEO_EXTENSIONS as $ext) {
            if ($fileext == $ext) {
                $this->video = true;
                $this->urlfilename = $basename;
                break;
            }
        }
    }


    public static function createfrom($object, $dirname)
    {
        if (isset($object->relative) && $object->relative) {
            $relative = true;
        } else {
            $relative = false;
        }
        return new JOGalleryImage(
            $dirname,
            $object->filename,
            $object->basename,
            $object->moddate,
            $object->comment,
            $object->urlfilename,
            $object->urlshortfilename,
            $relative
        );
    }

    public static function geturlfilename($dirname, $url, $relative)
    {
        if (($relative == false) || (strstr($url, $dirname) !== false)) {
            return $url;
        } else {
            return str_replace("//", "/", Uri::root(true) . "/" .
                    str_replace(
                        DIRECTORY_SEPARATOR,
                        "/",
                        $dirname . "/" . $url
                    ));
        }
    }


    public function getthumbbounds($mode, &$width, &$height)
    {
        $thumbimage = JThumbsHelper::getthumb(
            JOGalleryHelper::joinPaths(JPATH_SITE, $this->dirname),
            $mode,
            $this->basename
        );
        $imageInfo = getimagesize($thumbimage);
        $width = $imageInfo[0];
// Largeur en pixels
        $height = $imageInfo[1]; // Hauteur en pixels
    }


    public static function toArray($object, $full = true)
    {
        $ar = get_object_vars($object);
        if ($full) {
            $ar["urlfilename"] = self::geturlfilename(
                $object->dirname,
                $object->urlfilename,
                $object->relative
            );
            $ar["urlshortfilename"] = self::geturlfilename(
                $object->dirname,
                $object->urlshortfilename,
                $object->relative
            );
            $width = $height = "auto";
            $object->getthumbbounds("large", $width, $height);
            $ar["width"] = $width;
            $ar["height"] = $height;
        }
        return $ar;
    }


    public static function savecsv($file, $results, $isarray = false)
    {
        $fp = fopen($file, 'w');
        if ($fp) {
            if (count($results)) {
                fputcsv($fp, array_keys(get_object_vars((object)$results[0])), ";");
            }
            foreach ($results as $fields) {
                $vars = get_object_vars((object)$fields);
                fputcsv($fp, array_map(function($value) {
                                        return $value === false ? '0' : $value;
                                        }, get_object_vars((object)$fields)), ";");
            }
            fflush($fp);
            fclose($fp);
        } else {
            print "error writing to file" . $file;
        }
    }

    public static function readcsv($file)
    {
        $results = array();
        $fp = fopen($file, 'r');
        if ($fp) {
            $header =  fgetcsv($fp, 1024, ";");
            while (!feof($fp)) {
                $array = fgetcsv($fp, 1024, ";");
                if (is_array($array)) {
                    $obj = new \stdClass();
                    foreach ($header as $field) {
                        if (is_array($array) && count($array)) {
                                $obj->$field = array_shift($array);
                        }
                    }
                    array_push($results, self::createfrom($obj, dirname($file)));
                }
            }
            fclose($fp);
        }
        return $results;
    }
}
