<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\Helper;

use Joomla\CMS\Factory;
use JLTRY\Component\JOGallery\Administrator\Helper\JGalleryCategory;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects




abstract class JOGalleryCategoryHelper
{
    public static function getcategorytitle($catID)
    {
        $db = Factory::getDBO();
        $db->setQuery("SELECT title FROM #__categories WHERE id = " . $catID);
        $title = $db->loadResult();
        return $title;
    }

    public static function getcategoryaccess($catID)
    {
        $db = Factory::getDBO();
        $db->setQuery("SELECT access FROM #__categories WHERE id = " . $catID);
        $access = $db->loadResult();
        return $access;
    }


    public static function getcategoryparams($catID)
    {
        $jmodelcategories = JOGalleryModelCategories::getInstance();
        $category = $jmodelcategories->getItem($catID);
        return json_decode($category->params);
    }


    public static function getcategoryandchildren($catID, $type = 'Content', $recurse = true)
    {
        $categories = Categories::getInstance($type);
        $cat = $categories->get($catID);
        $catchildren = array($catID);
        if ($cat != null) {
            $children = $cat->getChildren();
            foreach ($children as $child) {
                $catchildren = array_merge(
                    $catchildren,
                    self::getcategoryandchildren($child->id, $type, true)
                );
            }
        }
        return $catchildren;
    }


    public static function usercanwritecategory($user, $category, $writeaccess = false)
    {
        $jmodelcategories = JOGalleryModelCategories::getInstance();
        return $jmodelcategories->usercanwritecategory($user, $category);
    }


    public static function usercanreadcategory($user, $category)
    {
        $jmodelcategories = JOGalleryModelCategories::getInstance();
        return $jmodelcategories->usercanreadcategory($user, $category);
    }

    public static function usercanviewcategory($user, $catid)
    {
        if ($catid == null) {
            $ok = true;
        } else {
            $levels = $user->getAuthorisedViewLevels();
            $access = self::getcategoryaccess($catid);
            $ok = in_array($access, $levels);
        }
        return $ok;
    }
}
