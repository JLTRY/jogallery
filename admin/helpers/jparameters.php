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
use Joomla\CMS\Component\ComponentHelper as JComponentHelper;

abstract class JParametersHelper
{
	static $_default_parameters =
		array('rootdir' => 'phocagallery',
			 'thumb_small_format' => '/thumbs/phoca_thumb_s_%s',
			 'thumb_large_format' => '/thumbs/phoca_thumb_l_%s',
			 'thumb_small_width' => 80,			  
			 'thumb_small_height' => 80,
			 'thumb_large_width' => 2560,
			 'thumb_large_height' => 1920,
			 'thumb_quality' => 90
		);
	

	static function get($parameter) {
	  $value = JComponentHelper::getParams('com_jgallery')->get($parameter);
	  if ($value === null) {
		if (array_key_exists('thumb_small_width', self::$_default_parameters)){
			//JLog::add("Error parameter thumb_small_width exists !!!", JLog::ERROR, 'jerror');
		}else {	 
			JLog::add("Error parameter thumb_small_width does not exist !!!", JLog::WARNING, 'jerror');
		} 
		if (array_key_exists($parameter, self::$_default_parameters)) {
			$value = self::$_default_parameters[$parameter];
		} else {
			JLog::add("Error parameter $parameter does not exist !!!", JLog::ERROR, 'jerror');
			$value = False;
		 }
	  }
	  return $value;
	}
	
	static function getrootdir() {
		return "images/" . self::get('rootdir');
	}

	
}	