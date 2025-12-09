<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2005 - 2025 JL TRYOEN. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Log\Log;
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

Log::addLogger(
    array(
         // Sets file name
         'text_file' => 'com_jogallery.log.php'
    ),
    // Sets messages of all log levels to be sent to the file.
    Log::ALL,
    // The log category/categories which should be recorded in this file.
    // In this case, it's just the one category from our extension.
    // We still need to put it inside an array.
    array('com_jogallery')
);

// Execute the requested task
$mvc = Factory::getApplication()
    ->bootComponent("com_jogallery")
    ->getMVCFactory();
$controller = $mvc->createController('JOGalleries');
$controller->execute(Factory::getApplication()->getInput()->get('task'));
$controller->redirect();


