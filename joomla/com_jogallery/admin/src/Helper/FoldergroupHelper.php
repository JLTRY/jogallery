<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\Helper;

use JLTRY\Component\JOGallery\Administrator\Model\FoldergroupModel;
use JLTRY\Component\JOGallery\Administrator\Helper\JODirectoryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JODirectory;
use JLTRY\Component\JOGallery\Administrator\Helper\JOFolderGroup;
use JLTRY\Component\JOGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use JLTRY\Component\JOGallery\Administrator\Table\Foldergroup;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Foldergroup component helper.
 *
 * @param   string  $submenu  The name of the active view.
 *
 * @return  void
 *
 * @since   1.6
 */
abstract class FoldergroupHelper
{
    public static function getCategoryAccess($catID)
    {
        $db = Factory::getDBO();
        $db->setQuery("SELECT access FROM #__categories WHERE id = " . $catID . " LIMIT 1;");
        $access = $db->loadResult();
        return $access;
    }

    public static function usercanviewcategory($user, $catid)
    {
        $levels = $user->getAuthorisedViewLevels();
        $access = self::getCategoryAccess($catid);
        $ok = in_array($access, $levels);
        return $ok;
    }

    /**
    **
    * @param
    */
    public static function display($_params)
    {
        $content = "";
        if (is_array($_params) == false) {
            return  "errorf:" . print_r($_params, true);
        }
        if (array_key_exists('parent', $_params)) {
            $parent = $_params['parent'];
        } else {
            $parent = 0;
        }
        if (array_key_exists('rootdir', $_params)) {
            $rootdir = $_params['rootdir'];
        } else {
            $rootdir = ".";
        }
        if (array_key_exists('directory', $_params)) {
            $directory = $_params['directory'];
            if ($parent > 1) {
                $name = $directory;
            }
        } else {
            $directory = null;
        }
        if (array_key_exists('id', $_params)) {
            $id = $_params['id'];
        } else {
            $id = -1;
        }
        if (array_key_exists('tmpl', $_params)) {
            $tmpl = $_params['tmpl'];
        } else {
            $tmpl = null;
        }
        if (array_key_exists('type', $_params)) {
            $type = $_params['type'];
        } else {
            $type = 'directories';
        }
        if (array_key_exists('media', $_params)) {
            $media = $_params['media'];
        } else {
            $media = "IMAGES";
        }
        if ($parent == 0) {
            $content .= "<h2>" . $name . "</h2>";
        }
        JOGalleryHelper::loadLibrary(array("lazyload" => true, "fancybox" => true, "jogallery" => true));
        $dir = utf8_decode(html_entity_decode(JOGalleryHelper::joinPaths(JPATH_SITE, $rootdir, $directory)));
        $jroot = new JOFolderGroup($dir, $directory, $parent, $id, $tmpl);
        $jroot->findDirs($dir, $directory, true);
        $jroot->outputdirs($type, $id, $content);
        if ($directory != null && $parent != 0) {
            $content .= "<hr/>";
            $id = rand(1, 1024);
            $listfilteredfiles = JOGalleryHelper::getFiles($rootdir, $directory, $media, -1, -1);
            $content .= LayoutHelper::render(
                'jogallery',
                array('id' => $id),
                JPATH_ADMINISTRATOR .
                '/components/com_jogallery/layouts'
            );
            JOGalleryHelper::outputfiles($id, $directory, $listfilteredfiles, -1, $content);
        }
        return $content;
    }
}
