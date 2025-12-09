<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Filter\OutputFilter;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Access\Access;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 *
 *
 * @package     Joomla.Admin
 * @subpackage  com_jogallery
 * @since       0.1
 */
class JOGalleryModelCategories extends ListModel
{
    private $categoriesbyid;

    public static function getInstance($type = 'Categories', $prefix = "JOGalleryModel", $config = array())
    {
        return ListModel::getInstance($type, $prefix, array('ignore_request' => true));
    }

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_categoriesbyid = array();
    }

    public function getItems()
    {
        $categories = parent::getItems();
        if (! $categories) {
            return $categories;
        }

        foreach ($categories as $category) {
            if (is_string($category->params)) {
                $category->params = json_decode($category->params, true);
            }
            $this->_categoriesbyid[$category->id] = $category;
        }
        return $categories;
    }

    public function getItem($id)
    {
        if (empty($this->_categoriesbyid)) {
            $this->getItems();
        }
        if (array_key_exists($id, $this->_categoriesbyid)) {
            return $this->_categoriesbyid[$id];
        } else {
            return null;
        }
    }

    public function getcategoryandchildren($id)
    {
        $allcat = $this->getItems();
        $children = array($id);
        foreach ($allcat as $cat) {
            if ($cat->parent_id == $id) {
                $children  = array_merge($children, $this->getcategoryandchildren($cat->id));
            }
        }
        return $children;
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  string    An SQL query
     * @since   0.1
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        // Select required fields from the categories.
        $query->select($this->getState(
            'list.select',
            'a.id, a.asset_id, a.extension, a.parent_id, a.params, a.published, a.title, a.access'
        ))
            ->from($db->quoteName('#__categories') . ' AS a');
        // Filter by category.
        $query->where('a.extension = ' . $db->quote('com_jogallery'));
        //Filter by published category
        $cpublished = $this->getState('filter.c.published');
        if (is_numeric($cpublished)) {
            $query->where('c.published = ' . (int) $cpublished);
        }
        return $query;
    }



    public function usercanwritecategory($user, $category)
    {
        if (!$user->guest) {
            $userid = $user->id;
        } else {
            $userid = -1;
        }
        if (is_int($category) || is_string($category)) {
            $category = $this->getItem($category);
        }
        if (
            $category != null &&
            ($user->authorise('core.admin') ||
            !property_exists($category, 'params') ||
            !array_key_exists('userlist', $category->params) ||
            in_array($userid, $category->params['userlist']))
        ) {
            return true;
        } else {
            return false;
        }
    }



    public function usercanviewcategory($user, $catid)
    {
        $levels = $user->getAuthorisedViewLevels();
        if (is_int($catid) || is_string($catid)) {
            $category = $this->getItem($catid);
        }
        $ok = false;
        if ($category != null) {
            $access = $category->access;
            $ok = in_array($access, $levels);
        } else {
            print_r("category is null view!!!");
        }
        return $ok;
    }
}
