<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 * @copyright   Copyright (C) 2005 - 2015 JL Tryoen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JGallery\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\CMS\Access\Rules;

// No direct access
defined('_JEXEC') or die('Restricted access');



/**
 * FoldergroupTable class
 *
 * @since  0.0.1
 */
class FoldergroupTable extends Table
{
	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__jgallery_foldergroups', 'id', $db);
	}
    
    /**
	 * Overloaded bind function
	 *
	 * @param       array           named array
	 * @return      null|string     null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['folders']) && is_array($array['folders']))
		{
			// Convert the params field to a string.
			$folders = new Registry;
			$folders->loadArray($array['folders']);
			$array['folders'] = (string)$folders;
		}
		return parent::bind($array, $ignore);
	}
}

