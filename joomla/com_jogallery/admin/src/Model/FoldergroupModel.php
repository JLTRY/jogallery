<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL Tryoen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JOGallery Foldergroup Model
 *
 * @since  0.0.1
 */
class FoldergroupModel extends AdminModel
{
	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A JTable object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'Foldergroup', $prefix = 'Administrator', $config = array())
	{
		/** @var \Joomla\CMS\MVC\Factory\MVCFactory $mvc */
		$mvc = Factory::getApplication()
				->bootComponent("com_jogallery")
				->getMVCFactory();
		return $mvc->createTable($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed    A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_jogallery.foldergroup',
			'foldergroup',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState(
			'com_jogallery.edit.foldergroup.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
	/**
	 * Method to check if it's OK to delete a message. Overwrites JModelAdmin::canDelete
	 */
	protected function canDelete($record)
	{
		if( !empty( $record->id ) )
		{
			return Factory::getUser()->authorise( "core.delete", "com_jogallery.message." . $record->id );
		}
	}

}
