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

class JORootDirectory extends JODirectory
{
    public function __construct($dirname, $basename, $parent = 0, $id = 0, $tmpl = null)
    {
        parent::__construct(null, $dirname, $basename, $parent, $id, $tmpl);
    }

    public function getbase64path()
    {
        return base64_encode(utf8_encode(JOGalleryHelper::joinPaths(".", $this->basename)));
    }
}
