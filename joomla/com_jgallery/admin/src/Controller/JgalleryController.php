<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2015 - 2025 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JGallery\Administrator\Controller;
use JLTRY\Component\JGallery\Administrator\Helper\JGalleryHelper;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Log\Log;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * JGalleryController
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 * @since       0.0.9
 */
class JGalleryController extends FormController
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
			return Factory::getUser()->authorise( "core.edit", "com_jgallery.message." . $id );
		}
	}
	
	
	public function genthumbs() {
		$view = $this->getView( 'jgallery', 'html' );
		// sets the template to someview.php
		$input = Factory::getApplication()->input;
		$viewLayout = $input->getVar( 'tmpl', 'thumbs' );
		// tell the view which tmpl to use 
		$view->setLayout($viewLayout);
		$model = $this->getModel('jgallery');
		$view->setModel($model, true);
		// go off to the view and call the display method
		$view->display();
	}
    
    public function genrecthumbs() {
		$view = $this->getView( 'jgallery', 'html' );
		// sets the template to someview.php
		$input = Factory::getApplication()->input;
		$viewLayout  = $input->getVar( 'tmpl', 'recthumbs' );
		// tell the view which tmpl to use 
		$view->setLayout($viewLayout);
		$model = $this->getModel('jgallery');
		$view->setModel($model, true);
		// go off to the view and call the display method
		$view->display();
	}
	
	public function comments() {
		$view = $this->getView( 'jgallery', 'html' );
		// sets the template to someview.php
		$viewLayout  = Factory::getApplication()->input->getVar( 'tmpl', 'comments' );
		// tell the view which tmpl to use 
		$view->setLayout($viewLayout);
		$model = $this->getModel('jgallery');
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
		$ret = JGalleryHelper::savecomments(utf8_decode(base64_decode($directory64)), $post_data);
		JGalleryHelper::json_answer($ret);
	}
	
	public function delete() {
		$input = Factory::getApplication()->input;
		//$input = new JInput($_POST);
		$directory64 = Factory::getApplication()->getInput()->getVar( 'directory64', '' );
		// tell the view which tmpl to use 
		$post_data = $input->getVar('images', array());
		$keep = $input->getVar('keep', 1);
		$errors = array();
        Log::add("delete" . utf8_decode(base64_decode($directory64)), Log::WARNING, 'com_jgallery');
        Log::add("delete" . print_r($post_data, true), Log::WARNING, 'com_jgallery');
		$ret = JGalleryHelper::deleteimages(utf8_decode(base64_decode($directory64)), $post_data, $keep, $errors);
        Log::add("delete=>:" . print_r($ret, true), Log::WARNING, 'com_jgallery');
		JGalleryHelper::json_answer($errors);
	}

	public function save($key = null, $urlVar = null)
	{
		$data = JGalleryHelper::getVar('jform', array(), 'post', 'array');
		$data['catid'] = $data['jgallerycatid'];
		Factory::getApplication()->input->post->set('jform', $data);
		return parent::save('id', 'id');
	}
}
