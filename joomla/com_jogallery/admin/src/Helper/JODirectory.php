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
// phpcs:enable PSR1.Files.SideEffects

define("JPATH_LAYOUTS", JPATH_ADMINISTRATOR . '/components/com_jogallery/layouts');

class JODirectory
{
    protected $parent;
    protected $basename;
    protected $dirname;
    protected $children;
    protected $id;
    protected $tmpl;
    private static $excludes = array("thumbs", "jw_sig", "th", "selection");

    //dirname is the full path basename is the name
    public function __construct($parent, $dirname, $basename, $parentlevel = 0, $id = 0, $tmpl = "")
    {
        $this->parent = $parent;
        $this->dirname = $dirname;
        $this->basename = $basename;
        $this->parentlevel = $parentlevel;
        $this->id = $id;
        $this->tmpl = $tmpl;
        $this->children = array();
    }

    public function getbasename()
    {
        return $this->basename;
    }

    public function getrelativepath()
    {
        if ($this->parent == null) {
            return "";
        } else {
            $parentpath = $this->parent->getrelativepath();
            if ($parentpath != "") {
                return $parentpath . "/" . $this->basename;
            } else {
                return $this->basename;
            }
        }
    }


    public function getbase64path()
    {
        return base64_encode(utf8_encode(JOGalleryHelper::joinPaths($this->dirname, $this->basename)));
    }

    public function insertDir($elem)
    {

        $startIndex = 0;
        $stopIndex = count($this->children) - 1;
        $middle = 0;
        while ($startIndex < $stopIndex) {
            $middle = ceil(($stopIndex + $startIndex) / 2);
            if ($elem->basename < $this->children[$middle]->basename) {
                $stopIndex = $middle - 1;
            } elseif ($elem->basename >= $this->children[$middle]->basename) {
                $startIndex = $middle;
            }
        }
        if (!count($this->children)) {
            $offset = 0;
        } else {
            $offset = $elem->basename <= $this->children[$startIndex]->basename ? $startIndex : $startIndex + 1;
        }
        array_splice($this->children, $offset, 0, array($elem));
    }

    /* https://rosettacode.org/wiki/Walk_a_directory/Recursively#PHP */
    public function findDirs($sdir, $sdir1, $recurse = false)
    {
        $subdirs = array();
        $dirs = array();
        if (!is_dir($sdir)) {
            return(-1);
        }
        foreach (new \DirectoryIterator($sdir) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            $filename = $fileInfo->getFileName();
            $dir1 = JOGalleryHelper::joinPaths($sdir, $filename);
            if (is_dir($dir1) && !(in_array($filename, self::$excludes,))) {
                $this->insertDir(new JODirectory($this, $sdir1, $filename, 0, $this->id));
            }
        }
        if ($recurse) {
            foreach ($this->children as $subdir) {
                $subdir->findDirs(
                    JOGalleryHelper::joinPaths($sdir, $subdir->getbasename()),
                    JOGalleryHelper::joinPaths($sdir1, $subdir->getbasename()),
                    $recurse
                );
            }
        }
        return 0;
    }

    public function getRoute($parentdir, $directory, $parentlevel, $id = 0, $tmpl = null)
    {
        return Uri::root(true) .
                "/index.php?option=com_jogallery&view=jogallery&directory64=" .
               base64_encode(utf8_encode("{$parentdir}/{$directory}")) .
                    "&parent=" . $parentlevel . "&Itemid=0" . "&id=" . $id;
    }

    public function getjsondirectories()
    {
        $directories = array();
        if ($this->parentlevel > 0) {
            array_push($directories, array("name" => "..",
                                        "parent" => $this->parentlevel - 1,
                                        "url" => self::getRoute(
                                            $this->basename,
                                            "..",
                                            $this->parentlevel - 1,
                                            $this->id
                                        )));
        }
        foreach ($this->children as $directory) {
            array_push($directories, array("name" => basename($directory->basename),
                                        "parent" => $this->parentlevel,
                                        "url" => self::getRoute(
                                            $directory->dirname,
                                            $directory->basename,
                                            $this->parentlevel + 1,
                                            $this->id
                                        )));
        }
        return json_encode($directories);
    }

    public function outputselectdirsmenu($id, &$content)
    {
        $sid = 'jogalleryselect' . $id;
        $urlroot = Uri::root(true);
        $selectdirs = array();
// no line for folder
        if ($this->basename != null) {
            array_push($selectdirs, array($this->getbase64path(), JOGalleryHelper::joinPaths($this->basename)));
        }
        foreach ($this->children as $child) {
            array_push($selectdirs, array($child->getbase64path(),
                JOGalleryHelper::joinPaths($child->dirname, $child->basename)));
        }
        $content .= LayoutHelper::render(
            'selectdirs',
            array('sid' => $sid, 'selectdirs' => $selectdirs),
            JPATH_LAYOUTS
        );
    }


    public function outputselectdirs($id, &$content, $media, $lightbox)
    {
        $sid = 'jogalleryselect' . $id;
        $imagesid = 'jogallery' . $id;
        $urlroot = Uri::root(true);
        $selectdirs = array();
        // no line for folder
        if ($this->basename != null) {
            array_push($selectdirs, array($this->getbase64path(),
                JOGalleryHelper::joinPaths($this->basename)));
        }
        foreach ($this->children as $child) {
            array_push($selectdirs, array($child->getbase64path(),
                JOGalleryHelper::joinPaths($child->dirname, $child->basename)));
        }
        $content .= LayoutHelper::render(
            'selectdirs',
            array('sid' => $sid, 'selectdirs' => $selectdirs),
            JPATH_LAYOUTS
        );
        $content .= LayoutHelper::render('jogallery', array('id' => $id), JPATH_LAYOUTS);
        JOGalleryHelper::loadLibrary(array("jselectdirs" => true));
        if ($lightbox == "fancybox") {
            JOGalleryHelper::loadLibrary(array("jimages" => true, "fancybox" => true));
        } else {
            JOGalleryHelper::loadLibrary(array("psw_images" => true, "photoswipe" => true));
        }
        JOGalleryHelper::loadLibrary(array("inline" =>
                                    array('import {jselectdirs_getimages} from "' .
                                            Uri::root() .
                                            '/media/com_jogallery/js/jselectdirs.js";
                                            (function($) {
                                                $(document).ready(function() {
                                                    jselectdirs_getimages($, "' . $sid . '", "' .
                                                        $imagesid . '", "' .
                                                        Uri::root(false) . '","' .
                                                        $media . '","' .
                                                        $lightbox . '");
                                                    })})(jQuery);',
                                            ['position' => 'after'],
                                            ['type' => 'module'],
                                            ['com_jogallery.jselectdirs'])));
    }


    public function outputselectthumbs($id, &$content)
    {
        $sid = 'jogalleryselect' . $id;
        $urlroot = Uri::root(true);
        $content .= LayoutHelper::render('jimages', array('id' => $id), JPATH_LAYOUTS);
        JOGalleryHelper::loadLibrary(array("jimages" => true, "jthumbs" => true,
            "fancybox" => true, "jogallery" => true));
        JOGalleryHelper::loadLibrary(array("inline" =>
                                        array('(function($) {
                                            $(document).ready(function() {
                                                jthumbs_getimages($, "' . $sid . '", "' .
                                                $id . '", "' .
                                                $urlroot . '", false);
                                                })})(jQuery);')));
    }


    public function outputselectcomments($id, &$content)
    {
        $sid = 'jogalleryselect' . $id;
        $urlroot = Uri::root(true);
        $app = Factory::getApplication();
        if ($app->isClient('administrator')) {
            $urlroot .= "/administrator";
        }
        $content .= LayoutHelper::render('jimages', array('id' => $id), JPATH_LAYOUTS);
        JOGalleryHelper::loadLibrary(array("jimages" => true, "jcomments" => true,
            "fancybox" => true, "jogallery" => true));
        JOGalleryHelper::loadLibrary(array("inline" =>
                                            array('(function($) {
                                                    $(document).ready(function() {
                                                         $("#toolbar").css("height", "5em");
                                                         jcomments_getimages($, "' .
                                                         $sid . '", "' .
                                                         $id . '", "' .
                                                         $urlroot . '", false);
                                                    })})(jQuery);')));
    }


    public function outputarray(&$arr)
    {
        if (($this->parent != null) || (!count($this->children))) {
            array_push($arr, array("name" => JOGalleryHelper::joinPaths($this->dirname, $this->basename),
                                "relative" => $this->getrelativepath(),
                                "value" => $this->getbase64path()));
        }
        foreach ($this->children as $child) {
            $child->outputarray($arr);
        }
    }

    public function outputjson(&$json)
    {
        $arr = array();
        $this->outputarray($arr);
        $json = json_encode($arr);
    }

    public function outputradio($id, &$content, $media, $lightbox)
    {
        $json = "";
        $this->outputjson($json);
        $sid = "findir" . $id;
        $sidg = "jogallery" . $id;
        $content .= LayoutHelper::render('radiobox', array('sid' => $sid), JPATH_LAYOUTS);
        $content .= LayoutHelper::render('jogallery', array('id' => $id), JPATH_LAYOUTS);
        JOGalleryHelper::loadLibrary(array("radiobox" => true));
        JOGalleryHelper::loadLibrary(array("inline" =>
                                            array('$(document).ready(function() {
                                                        initradiobox($, "#' .
                                                        $sid . '" ,' .
                                                        $json .
                                                        ',  fillgallery, [ "#' .
                                                        $sidg . '", '.
                                                        '"' . Uri::root() . '", ' .
                                                        '"' . $media . '", '.
                                                        '"' . $lightbox . '"]);' .
                                                    '});',
                                                    ['position' => 'after'],
                                                    [],
                                                    ['com_jogallery.radiobox'])));
    }


    public function outputrecthumbs($id, &$content)
    {
        $json = "";
        $this->outputjson($json);
        $sid = "findir" . $id;
        $content .= LayoutHelper::render('jimages', array('id' => $id), JPATH_LAYOUTS);
        JOGalleryHelper::loadLibrary(array("jrecthumbs" => true, "multicheckbox" => true));
        JOGalleryHelper::loadLibrary(array("inline" =>
                                        array(
                                            '$(document).ready(function() {
                                                jrecthumbs_getdirectories($, "#' .
                                                $sid . '" , ' .
                                                $id . ' , "' . Uri::root() . '",' . $json . ');
                                            });',
                                            ['position' => 'after'],
                                            [],
                                            ['com_jogallery.jrecthumbs'])));
    }

    public function outputdirectories($id, &$content)
    {
        $sid = "jogallerydir"  . $id;
        $json = $this->getjsondirectories();
        $icon = "/media/com_jogallery/images/icon-folder-medium.png";
        $content .= LayoutHelper::render('directories', array('id' => $id), JPATH_LAYOUTS);
        JOGalleryHelper::loadLibrary(array("jdirectories" => true, "bootstrap.tooltip" => true));
        JOGalleryHelper::loadLibrary(array("inline" =>
                                        array('(function($) {
                                                    $(document).ready(function() {
                                                        jdirectories_show($, "' .
                                                        $sid . '","' .
                                                        $icon . '",' . $json . ');
                                                    })
                                                })(jQuery);',
                                                ['position' => 'after'],
                                                [],
                                                ['com_jogallery.jdirectories'])));
    }

    public function outputdirs($type, $id, &$content, $media = "ALL", $lightbox = "fancybox")
    {
        $content .= "<!--" . $type . "-->";
        switch ($type) {
            case 'selectthumbs':
                $this->outputselectthumbs($id, $content);
                break;
            case 'selectcomments':
                $this->outputselectcomments($id, $content);
                break;
            case 'selectdirs':
                $this->outputselectdirs($id, $content, $media, $lightbox);
                break;
            case 'selectdirsmenu':
                $this->outputselectdirsmenu($id, $content);
                break;
            case 'recthumbs':
                $this->outputrecthumbs($id, $content);
                break;
            case 'directories':
                $this->outputdirectories($id, $content);
                break;
            default:
                $this->outputradio($id, $content, $media, $lightbox);
                break;
        }
        $content .= "<!--" . $type . "end -->";
    }
}
