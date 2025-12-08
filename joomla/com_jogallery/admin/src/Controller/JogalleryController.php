<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Administrator\Controller;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Log\Log;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JOGalleryController
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 * @since       0.0.9
 */
class JOGalleryController extends FormController
{

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
        $id = isset( $data[ $key ] ) ? $data[ $key ] : 0;
        if( !empty( $id ) )
        {
            return Factory::getUser()->authorise( "core.edit", "com_jogallery.message." . $id );
        }
    }
    
    
    public function genthumbs() {
        $view = $this->getView( 'jogallery', 'html' );
        // sets the template to someview.php
        $input = Factory::getApplication()->input;
        $viewLayout = $input->getVar( 'tmpl', 'thumbs' );
        // tell the view which tmpl to use 
        $view->setLayout($viewLayout);
        $model = $this->getModel('jogallery');
        $view->setModel($model, true);
        // go off to the view and call the display method
        $view->display();
    }
    
    public function genrecthumbs() {
        $view = $this->getView( 'jogallery', 'html' );
        // sets the template to someview.php
        $input = Factory::getApplication()->input;
        $viewLayout  = $input->getVar( 'tmpl', 'recthumbs' );
        // tell the view which tmpl to use 
        $view->setLayout($viewLayout);
        $model = $this->getModel('jogallery');
        $view->setModel($model, true);
        // go off to the view and call the display method
        $view->display();
    }
    
    public function comments() {
        $view = $this->getView( 'jogallery', 'html' );
        // sets the template to someview.php
        $viewLayout  = Factory::getApplication()->input->getVar( 'tmpl', 'comments' );
        // tell the view which tmpl to use 
        $view->setLayout($viewLayout);
        $model = $this->getModel('jogallery');
        $view->setModel($model, true);
        // go off to the view and call the display method
        $view->display();
    }


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
    
    public function delete() {
        $input = Factory::getApplication()->input;
        //$input = new JInput($_POST);
        $directory64 = Factory::getApplication()->getInput()->getVar( 'directory64', '' );
        // tell the view which tmpl to use 
        $post_data = $input->getVar('images', array());
        $keep = $input->getVar('keep', 1);
        $errors = array();
        Log::add("delete" . utf8_decode(base64_decode($directory64)), Log::WARNING, 'com_jogallery');
        Log::add("delete" . print_r($post_data, true), Log::WARNING, 'com_jogallery');
        $ret = JOGalleryHelper::deleteimages(utf8_decode(base64_decode($directory64)), $post_data, $keep, $errors);
        Log::add("delete=>:" . print_r($ret, true), Log::WARNING, 'com_jogallery');
        JOGalleryHelper::json_answer($errors);
    }

    public function save($key = null, $urlVar = null)
    {
        $data = JOGalleryHelper::getVar('jform', array(), 'post', 'array');
        $data['catid'] = $data['jogallerycatid'];
        Factory::getApplication()->input->post->set('jform', $data);
        return parent::save('id', 'id');
    }
}
