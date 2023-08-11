<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 * @copyright   Copyright (C) 2005 - 2015 JL Tryoen, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Hello Table class
 *
 * @since  0.0.1
 */
class JGalleryTableFolderGroups extends JTable
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
			$folders = new JRegistry;
			$folders->loadArray($array['folders']);
			$array['folders'] = (string)$folders;
		}

	
		return parent::bind($array, $ignore);
	}
}

	
