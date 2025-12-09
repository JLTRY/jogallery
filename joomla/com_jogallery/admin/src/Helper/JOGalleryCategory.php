<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\Helper;

use Joomla\CMS\Categories\Categories;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


class JOGalleryCategories extends Categories
{
    public function __construct($options = array())
    {
        $options['table'] = '#__content';
        $options['extension'] = 'com_jogallery';
        $options['statefield'] = 'published';
        parent::__construct($options);
    }
}
