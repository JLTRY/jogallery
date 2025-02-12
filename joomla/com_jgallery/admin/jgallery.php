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
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Log\Log as JLog;
use Joomla\CMS\MVC\Controller\BaseController as JControllerLegacy;

// Set some global property
$document = JFactory::getDocument();
$document->addStyleDeclaration('.icon-jgallery {background-image: url(../media/com_jgallery/images/tux-16x16.png);}');

// Access check: is this user allowed to access the backend of this component?
if (!JFactory::getUser()->authorise('core.manage', 'com_jgallery'))
{
	//return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// require helper file
JLoader::import('components.com_jgallery.jgalleryregister', JPATH_ADMINISTRATOR);

// Get an instance of the controller prefixed by JGallery
$controller = JControllerLegacy::getInstance('JGallery');

// Perform the Request task
$input = JFactory::getApplication()->input;
JLog::addLogger(
    array(
         // Sets file name
         'text_file' => 'com_jgallery.log.php'
    ),
    // Sets messages of all log levels to be sent to the file.
    JLog::ALL,
    // The log category/categories which should be recorded in this file.
    // In this case, it's just the one category from our extension.
    // We still need to put it inside an array.
    array('com_jgallery')
);
$controller->execute($input->getCmd('task'));


// Redirect if set by the controller
$controller->redirect();
