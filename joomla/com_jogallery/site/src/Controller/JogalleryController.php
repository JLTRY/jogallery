<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JLTRYOEN. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Site\Controller;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JOGalleryController
 *
 * @package     Joomla.Site
 * @subpackage  com_jogallery
 * @since       0.0.9
 */
class JOGalleryController extends FormController
{
    public function savecomments() {
        $input = new InputFilter(
                    array(
                        'img','p','a','u','i','b','strong','span','div','ul','li','ol','h1','h2','h3','h4','h5',
                        'table','tr','td','th','tbody','theader','tfooter','br'
                        ),
                    array(
                        'src','width','height','alt','style','href','rel','target','align','valign','border','cellpading',
                        'cellspacing','title','id','class'
                        )
                    );
        $directory64 = Factory::getApplication()->getInput()->getVar( 'directory64', '' );
        // tell the view which tmpl to use 
        $post_data = Factory::getApplication()->getInput()->getVar('comments', array());
        $ret = JOGalleryHelper::savecomments(utf8_decode(base64_decode($directory64)), $post_data);
        JOGalleryHelper::json_answer($ret);
    }
}