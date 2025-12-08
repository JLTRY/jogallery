<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL Tryoen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace JLTRY\Component\JOGallery\Administrator\Controller;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JOGallerys Controller
 *
 * @since  0.0.1
 */
class FoldergroupsController extends AdminController
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
    public function getModel($name = 'Foldergroups', $prefix = 'Administrator', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
