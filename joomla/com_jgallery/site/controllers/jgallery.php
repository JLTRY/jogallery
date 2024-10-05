<?php

JLoader::import('components.com_jgallery.helpers.jgallery', JPATH_ADMINISTRATOR);

class JGalleryControllerJGallery extends JControllerForm
{
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
}