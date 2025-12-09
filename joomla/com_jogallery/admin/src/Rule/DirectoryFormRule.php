<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2005 - 2025 JL TRYOEN. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\Rule;

use Joomla\CMS\Form\FormRule;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Form Rule class for the Joomla Framework.
 */
class DirectoryFormRule extends FormRule
{
    /**
     * The regular expression.
     *
     * @access  protected
     * @var     string
     * @since   2.5
     */
    protected $regex = '^.*$';
}
