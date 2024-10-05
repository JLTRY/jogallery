<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2015 JL Tryoen, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_PLATFORM') or die;
JFormHelper::loadFieldClass('list');
/**
 * Field to load a drop down list of available users
 *
 * @since  3.2
 */
class JFormFieldFolderList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   3.2
	 */
	protected $type = 'FolderList';
	/**
	 * Cached array of the users
	 *
	 * @var    array
	 * @since  3.2
	 */
	protected static $options = array();
	/**
	 * Method to get the options to populate list
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.2
	 */
	protected function getOptions()
	{
		// Hash for caching
		$options = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('a.id AS value')
			->select('a.username AS text')
			->from('#__users as a');
		$db->setQuery($query);
		$users = $db->loadObjectList();
		return $users;
	}
	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// value is a Jobject convert it to an array of values
		$this->value = array_values((array)$this->value);
		return '<table><tr><td><a class="btn btn-primary button-select" style="margin:-6px;border-radius: 0;"><span class="icon-user" ></span></a></td><td width="90%">' . parent::getInput() . "</td></table>";
	}
}