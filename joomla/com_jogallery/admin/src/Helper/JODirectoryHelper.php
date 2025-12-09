<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\Helper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


use JLTRY\Component\JOGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JODirectory;
use JLTRY\Component\JOGallery\Administrator\Helper\JORootDirectory;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Log\Log;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Layout\LayoutHelper;






abstract class JODirectoryHelper
{
    public function sortDir($a, $b)
    {
        if ($a[0] == $b[0]) {
            return 0;
        }
        return ($a[0] < $b[0]) ? 1 : -1;
    }


    // $dir is the full path sdir is the directory as parameter
    public static function outputdirs($id, $dir, $directory, &$content, $type = 'radio')
    {
        $document = Factory::getDocument();
        JOGalleryHelper::loadLibrary(array('jogallery' => true));
        $jroot = new JORootDirectory($dir, $directory);
        list($ret, $count) = $jroot->findDirs($dir, $directory, true);
        if ($ret != 0) {
            $content = $count;
        } else {
            $jroot->outputdirs($type, $id, $content);
        }
    }

    public static function display($id, $_params)
    {
        $content = "";
        if (is_array($_params) == false) {
            return  "errorf:" . print_r($_params, true);
        }
        if (! array_key_exists('dir', $_params)) {
            return  "errorf: missing dir param" . print_r($_params, true);
        }
        if (array_key_exists('rootdir', $_params)) {
            $rootdir = $_params['rootdir'];
        } else {
            $rootdir =  JParametersHelper::getrootdir();
        }
        if (array_key_exists('type', $_params)) {
            $type = $_params['type'];
        } else {
            $type = 'radio';
        }
        $directory = $_params['dir'];
        $dir = utf8_decode(html_entity_decode(JOGalleryHelper::joinPaths(JPATH_SITE, $rootdir, $directory)));
        if (!is_dir($dir)) {
            $content .= "Directory does not exists :" . $dir;
        } else {
            JOGalleryHelper::loadLibrary(array("fancybox" => true));
            self::outputDirs($id, $dir, $directory, $content, $type);
        }
        return $content;
    }
}
