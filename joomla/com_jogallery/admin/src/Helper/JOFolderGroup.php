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
use JLTRY\Component\JOGallery\Administrator\Model\FoldergroupModel;

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


class JOFolderGroup extends JODirectory
{
    private $folders;

    public function __construct($dirname, $basename, $parent, $id, $tmpl)
    {
        parent::__construct(null, $dirname, $basename, $parent);
        $this->tmpl = $tmpl;
        $this->id = $id;
        $model = new FoldergroupModel();
    //$model->setstate($model->getName() . '.id', $id);
        $mod = $model->getItem($id);
        if ($mod !== null) {
            $this->folders = json_decode($mod->folders);
            $this->name = $mod->name;
        }
    }

    public function findDirs($sdir, $sdir1, $recurse = false)
    {
        if ($this->parentlevel > 0) {
            return parent::findDirs($sdir, $sdir1, false);
        }
        foreach ($this->folders as $folder) {
            $this->insertDir(new JODirectory($this, "", $folder));
        }
    }

    public function getRoute($parentdir, $dir, $parent, $id = 0, $tmpl = null)
    {
        $route = Uri::root(true) . "/index.php?option=com_jogallery&view=foldergroup&parent=" .
                 $parent . "&Itemid=0&id=" . $id . "&header=1&XDEBUG_SESSION_START=test";
        if ($dir !== null) {
            $route .= "&directory64=" . base64_encode(utf8_encode(JOGalleryHelper::joinPaths($parentdir, $dir)));
        }
        if ($tmpl != null) {
            $route .= "&tmpl=" . $tmpl;
        }
        if ($name != "") {
            $route .= "&name=" . $name;
        }
        return $route;
    }

    public function getjsondirectories()
    {
        $listdirs = array();
        if ($this->parentlevel > 0) {
            array_push($listdirs, array("name" => ($this->parentlevel == 1) ?
                                                   $this->name : basename(dirname($this->basename)),
                                    "parent" => $this->parentlevel - 1,
                                    "url" => self::getRoute($this->basename, ($this->parentlevel == 1) ?
                                                "." : "..", $this->parentlevel - 1, $this->id, $this->tmpl)));
            foreach ($this->children as $directory) {
                array_push($listdirs, array("name" => $directory->basename,
                                        "parent" => $this->parentlevel,
                                        "url" => self::getRoute(
                                            $directory->dirname,
                                            $directory->basename,
                                            $this->parentlevel + 1,
                                            $this->id,
                                            $this->tmpl
                                        )));
            }
        } else {
            foreach ($this->folders as $folder) {
                array_push($listdirs, array("name" => basename($folder),
                                            "parent" => $this->parentlevel,
                                            "url" => self::getRoute(
                                                dirname($folder),
                                                ($this->parentlevel == -1) ? null : basename($folder),
                                                $this->parentlevel + 1,
                                                $this->id,
                                                $this->tmpl
                                            )));
            }
        }
        return json_encode($listdirs);
    }
}
