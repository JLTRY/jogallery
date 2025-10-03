<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2025 JL TRYOEN. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Log\Log;
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

Log::addLogger(
    array(
         // Sets file name
         'text_file' => 'com_jgallery.log.php'
    ),
    // Sets messages of all log levels to be sent to the file.
    Log::ALL,
    // The log category/categories which should be recorded in this file.
    // In this case, it's just the one category from our extension.
    // We still need to put it inside an array.
    array('com_jgallery')
);

// Execute the requested task
$mvc = Factory::getApplication()
    ->bootComponent("com_jgallery")
    ->getMVCFactory();
$controller = $mvc->createController('JGalleries');
$controller->execute(Factory::getApplication()->getInput()->get('task'));
$controller->redirect();


