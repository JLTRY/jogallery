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
JLoader::import('components.com_jgallery.helpers.jgallery', JPATH_ADMINISTRATOR);

/**
 * JGalleryController
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 * @since       0.0.9
 */
class JGalleryControllerJGallery extends JControllerForm
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
			return JFactory::getUser()->authorise( "core.edit", "com_jgallery.message." . $id );
		}
	}
	
	
	public function genthumbs() {
		$view = $this->getView( 'jgallery', 'html' );
		// sets the template to someview.php
		$input = JFactory::getApplication()->input;
		$viewLayout  = $input->getVar( 'tmpl', 'thumbs' );
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
		$input = JFactory::getApplication()->input;
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
		$viewLayout  = JFactory::getApplication()->input->getVar( 'tmpl', 'comments' );
		// tell the view which tmpl to use 
		$view->setLayout($viewLayout);
		$model = $this->getModel('jgallery');
		$view->setModel($model, true);
		// go off to the view and call the display method
		$view->display();
	}
	
	
	
	public function savecomments() {
		$input_options = JFilterInput::getInstance(
        array(
            'img','p','a','u','i','b','strong','span','div','ul','li','ol','h1','h2','h3','h4','h5',
            'table','tr','td','th','tbody','theader','tfooter','br'
        ),
        array(
            'src','width','height','alt','style','href','rel','target','align','valign','border','cellpading',
            'cellspacing','title','id','class'
        )
		);
		//$input = JFactory::getApplication()->input;
		$input = new JInput($_POST, array('filter' => $input_options));
		$directory64 = JFactory::getApplication()->getInput()->getVar( 'directory64', '' );
		// tell the view which tmpl to use 
		$post_data =$input->getVar('comments', array());
		$ret = JGalleryHelper::savecomments(utf8_decode(base64_decode($directory64)), $post_data);
		JGalleryHelper::json_answer($ret);
	}
	
	public function delete() {
		$input = JFactory::getApplication()->input;
		//$input = new JInput($_POST);
		$directory64 = JFactory::getApplication()->getInput()->getVar( 'directory64', '' );
		// tell the view which tmpl to use 
		$post_data = $input->getVar('images', array());
		$keep = $input->getVar('keep', 1);
		$errors = array();
        JLog::add("delete" . utf8_decode(base64_decode($directory64)), JLog::WARNING, 'com_jgallery');
        JLog::add("delete" . print_r($post_data, true), JLog::WARNING, 'com_jgallery');        
		$ret = JGalleryHelper::deleteimages(utf8_decode(base64_decode($directory64)), $post_data, $keep, $errors);
        JLog::add("delete=>:" . print_r($ret, true), JLog::WARNING, 'com_jgallery');
		JGalleryHelper::json_answer($errors);
	}
}
