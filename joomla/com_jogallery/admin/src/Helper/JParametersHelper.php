<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2005 - 2025 JL TRYOEN. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\Helper;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Log\Log;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


abstract class JParametersHelper
{
    private static $default_parameters =
        array('rootdir' => 'phocagallery',
             'thumb_small_format' => '/thumbs/phoca_thumb_s_%s',
             'thumb_large_format' => '/thumbs/phoca_thumb_l_%s',
             'thumb_small_width' => 80,
             'thumb_small_height' => 80,
             'thumb_large_width' => 2560,
             'thumb_large_height' => 1920,
             'thumb_quality' => 90
        );

    public static function get($parameter)
    {
        $value = ComponentHelper::getParams('com_jogallery')->get($parameter);
        if ($value === null) {
            if (array_key_exists($parameter, self::$default_parameters)) {
                $value = self::$_default_parameters[$parameter];
            } else {
                Log::add("Error parameter $parameter does not exist !!!", Log::ERROR, 'jerror');
                $value = false;
            }
        }
        return $value;
    }

    public static function getrootdir()
    {
        return "images/" . self::get('rootdir');
    }
}
