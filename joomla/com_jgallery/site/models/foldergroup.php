<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file

/**
 * JGallery Model
 *
 * @since  0.0.1
 */

class JGalleryModelFolderGroup extends JModelItem
{
	/**
	 * @var object item
	 */
	protected $item;

	


	/**
	 * Get the foldergroup
	 * @return object The message to be displayed to the user
	 */
	public function getItem($pk=NULL)
	{
		if (!isset($this->item)) 
		{
			$id    = $this->getState('folder.id');
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('name, h.published, folders, h.id, catid')->from('#__jgallery_foldergroups as h')
				  ->leftJoin('#__categories as c ON h.catid=c.id')
				  ->where('h.id=' . (int)$id);		  
			$db->setQuery((string)$query);
		
			if ($this->item = $db->loadObject()) 
			{
				// Load the JSON string
				$folders = json_decode($this->item->folders, $associative=true);
				$this->item->folders = $folders;
			}
		}
		return $this->item;
	}
}