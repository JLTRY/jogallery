<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\Controller;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\MVC\Controller\AdminController;
/**
 * JGalleries Controller
 *
 * @since  0.0.1
 */
class JOGalleriesController extends AdminController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Jogallery', $prefix = 'Administrator', $config = ['ignore_request' => true])
	{
		 return parent::getModel($name, $prefix, $config);
	} 
}
