<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JLTRYOEN. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * JOGallery Foldergroup Model
 *
 * @since  0.0.1
 */
class FoldergroupModel extends BaseDatabaseModel
{
    /**
     * @var object item
     */
    protected $item;
/**
     * Get the foldergroup
     * @return object The message to be displayed to the user
     */
    public function getItem($pk = null)
    {
        if (!isset($this->item)) {
            $id    = $this->getState('folder.id');
            $db    = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('name, h.published, folders, h.id, catid')->from('#__jogallery_foldergroups as h')
                  ->leftJoin('#__categories as c ON h.catid=c.id')
                  ->where('h.id=' . (int)$id);
            $db->setQuery((string)$query);
            if ($this->item = $db->loadObject()) {
            // Load the JSON string
                $folders = json_decode($this->item->folders, $associative = true);
                $this->item->folders = $folders;
            }
        }
        return $this->item;
    }
}
