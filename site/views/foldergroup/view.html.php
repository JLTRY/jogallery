<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JLoader::import('components.com_jgallery.helpers.jparameters', JPATH_ADMINISTRATOR);
JLoader::import('components.com_jgallery.helpers.foldergroup', JPATH_ADMINISTRATOR);
/**
 * HTML View class for the JGalleryComponent
 *
 * @since  0.0.1
 */
class JGalleryViewFolderGroup extends JViewLegacy
{
	static $_id;
	function getparam($name, $param) {
		$found = false;
		$app     = JFactory::getApplication();
		$input   = $app->getInput();
		$params  = $app->getParams();
		if ($params->get($param) !== null) {
			$this->{$name} = $params->get($param);
			$found = true;
		} elseif ($input->get($param) !== null) {
			$this->{$name} = $input->get($param);
			$found = true;
		}
		return $found;
	}
	/**
	 * Display the Gallery view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
       
		// Assign data to the view
		
        $this->getparam('parent', 'parent');
        $this->getparam('id', 'id');
        $model = new JGalleryModelFolderGroup;
        $model->setstate('folder.id', $this->id);
        $this->item = $model->getItem();
        $this->getparam('tmpl', 'tmpl');
        $this->directory = null;
        if (!$this->getparam('directory', 'directory'))
		{
			if ($this->getparam('directory64', 'directory64')) {
				$this->directory = base64_decode($this->directory64);
			}
		}
		if ($this->item){
			$this->folders = $this->item->folders;
            $this->name = $this->item->name;
            $this->id = $this->item->id;
		}        
        $user = JFactory::getUser();
        if (!FolderGroupHelper::usercanviewcategory($user, $this->item->catid)) {
            $errors =[ "No rights acces to view " . $this->name];
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');
            return;
        }
		$this->rootdir = JParametersHelper::getrootdir();		
		// Display the view
		parent::display($tpl);
	}
}
