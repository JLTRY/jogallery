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
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * JOGallery Model
 *
 * @since  0.0.1
 */
class JOGalleryModel extends BaseDatabaseModel
{
    /**
     * @var object item
     */
    protected $item;

    /**
     * Method to auto-populate the model state.
     *
     * This method should only be called once per instantiation and is designed
     * to be called on the first call to the getState() method unless the model
     * configuration flag to ignore the request is set.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return	void
     * @since	2.5
     */
    protected function populateState()
    {
        // Get the message id
        $jinput = Factory::getApplication()->input;
        $id     = $jinput->get('id', 1, 'INT');
        $this->setState('message.id', $id);

        // Load the parameters.
        $this->setState('params', Factory::getApplication()->getParams());
        parent::populateState();
    }


    /**
     * Get the message
     * @return object The message to be displayed to the user
     */
    public function getItem($pk=NULL)
    {
        if (!isset($this->item)) 
        {
            $id    = $pk;
            $db    = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('h.directory, h.params, c.id as catid')
                  ->from('#__jogallery as h')
                  ->leftJoin('#__categories as c ON h.catid=c.id')
                  ->where('h.id=' . (int)$id);
            $db->setQuery((string)$query);
        
            if ($this->item = $db->loadObject()) 
            {
                // Load the JSON string
                $params = new Registry;
                $params->loadString($this->item->params, 'JSON');
                $this->item->params = $params;

                // Merge global params with item params
                $params = clone $this->getState('params');
                $params->merge($this->item->params);
                $this->item->params = $params;
            }
        }
        return $this->item;
    }
}
