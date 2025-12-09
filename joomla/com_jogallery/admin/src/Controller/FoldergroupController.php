<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL Tryoen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\Controller;

use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * JOGallery Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 * @since       1.0.0
 */
class FoldergroupController extends FormController
{
    /**
    * Implement to allowAdd or not
    *
    * Not used at this time (but you can look at how other components use it....)
    * Overwrites: JControllerForm::allowAdd
    *
    * @param array $data
    * @return bool
    */
    protected function allowAdd($data = array())
    {
        return parent::allowAdd($data);
    }
    /**
    * Implement to allow edit or not
    * Overwrites: JControllerForm::allowEdit
    *
    * @param array $data
    * @param string $key
    * @return bool
    */
    protected function allowEdit($data = array(), $key = 'id')
    {
        $id = isset($data[ $key ]) ? $data[ $key ] : 0;
        if (!empty($id)) {
            return JOGalleryHelper::authorise("core.edit", "com_jogallery");
        }
    }
}
