<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jgallery
 *
 * @copyright   Copyright (C) 2005 - 2015 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JLoader::import('components.com_jgallery.helpers.jparameters', JPATH_ADMINISTRATOR);
JLoader::import('components.com_jgallery.helpers.jgallery', JPATH_ADMINISTRATOR);
JLoader::import('components.com_jgallery.helpers.jdirectory', JPATH_ADMINISTRATOR);

use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Uri\Uri as JUri;


abstract class JThumbsHelper
{
	static $_formats = array("small" , "large");
	
	static function getthumbformat($mode, $img) {
		$format = False;
		switch ($mode) {
			case "small":
			default:
				$format = JParametersHelper::get("thumb_small_format");
				break;
			case "large":
				$format = JParametersHelper::get("thumb_large_format");
				break;
		}
		if ($format === False) {
			return False;
		} else {
			return sprintf($format, $img);
		}
	}

	static function getthumb($directory, $mode, $img) {
		return  $directory . DIRECTORY_SEPARATOR . self::getthumbformat($mode, $img);
	}

	static function getthumbURL($rootdir, $mode, $img) {
		return JUri::root(true) . "/" . str_replace(DIRECTORY_SEPARATOR, "/" ,  JThumbsHelper::getthumb($rootdir, $mode, basename($img)));
	}

	static function read_image($original_file)
	{
		$original_extension = strtolower(pathinfo($original_file, PATHINFO_EXTENSION));
		$exif_data = exif_read_data($original_file);
		$exif_orientation = $exif_data['Orientation'];
		// load the image
		if($original_extension == "jpg" or $original_extension == "jpeg"){
			$original_image = imagecreatefromjpeg($original_file);
		}
		if($original_extension == "gif"){
			$original_image = imagecreatefromgif($original_file);
		}
		if($original_extension == "png"){
			$original_image = imagecreatefrompng($original_file);
		}
		 if($exif_orientation=='3'  or $exif_orientation=='6' or $exif_orientation=='8'){
			$new_angle[3] = 180;
			$new_angle[6] = -90;
			$new_angle[8] = 90;
			imagesetinterpolation($original_image, IMG_MITCHELL);
			$rotated_image = imagerotate($original_image, $new_angle[$exif_orientation], 0);
			imagedestroy($original_image); 
		}else {
			$rotated_image  = $original_image;
		}	 
		return $rotated_image;
	}
	 // Calculate thumbnail dimensions
    private static function thumbDimCalc($width, $height, $thb_width, $thb_height, $smartResize)
    {
        if ($smartResize) {
            // thumb ratio bigger that container ratio
            if ($width / $height > $thb_width / $thb_height) {
                // wide containers
                if ($thb_width >= $thb_height) {
                    // wide thumbs
                    if ($width > $height) {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                    // high thumbs
                    else {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                    // high containers
                } else {
                    // wide thumbs
                    if ($width > $height) {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                    // high thumbs
                    else {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                }
            } else {
                // wide containers
                if ($thb_width >= $thb_height) {
                    // wide thumbs
                    if ($width > $height) {
                        $thumb_width = $thb_width;
                        $thumb_height = $thb_width * $height / $width;
                    }
                    // high thumbs
                    else {
                        $thumb_width = $thb_width;
                        $thumb_height = $thb_width * $height / $width;
                    }
                    // high containers
                } else {
                    // wide thumbs
                    if ($width > $height) {
                        $thumb_width = $thb_height * $width / $height;
                        $thumb_height = $thb_height;
                    }
                    // high thumbs
                    else {
                        $thumb_width = $thb_width;
                        $thumb_height = $thb_width * $height / $width;
                    }
                }
            }
        } else {
            if ($width > $height) {
                $thumb_width = $thb_width;
                $thumb_height = $thb_width * $height / $width;
            } elseif ($width < $height) {
                $thumb_width = $thb_height * $width / $height;
                $thumb_height = $thb_height;
            } else {
                $thumb_width = $thb_width;
                $thumb_height = $thb_height;
            }
        }

        $thumbnail = array();
        $thumbnail['width'] = round($thumb_width);
        $thumbnail['height'] = round($thumb_height);

        return $thumbnail;
    }

	public static function generatethumb($filename, $thumbimage, $thb_width, $thb_height, $jpg_quality, $forced = false, &$errors=array()) {
		if (file_exists($thumbimage)&& ($forced == False)){
			array_push($errors, "The file already exists Exists $thumbimage");
			$ret = false;
		} else {
			mkdir(dirname($thumbimage));
			$fileTypes = array('gif', 'jpg', 'jpeg', 'png', 'webp');

			// Create an array of file types
			$found = array();
			$fileInfo = pathinfo($filename);
			if (array_key_exists('extension', $fileInfo) && 
								in_array(strtolower($fileInfo['extension']), $fileTypes)) {
				// Begin by getting the details of the original
				list($originalwidth, $originalheight, $type) = getimagesize($filename);

				// Create an image resource for the original
				switch ($type) {
					case 1:    
					case 2:
					case 3:
					//take into account orientation see https://www.php.net/manual/en/function.exif-read-data.php#121742
						$source = self::read_image($filename);
						break;
					case 18:
						// WEBP
						if (version_compare(PHP_VERSION, '7.1.0', 'ge')) {
							$source = imagecreatefromwebp($filename);
						} else {
							$source = null;
						}
						break;
					default:
						$source = null;
				}
				// Bail out if the image resource is not OK
				if (!$source) {
					array_push($errors, "Error in source");
					$ret = false;
				}
				else {
					$width  = imagesx($source);
					$height = imagesy($source);
					if ($thb_width > $originalwidth) {
						$thb_width = $originalwidth;
					}
					if ($thb_height > $originalheight) {
						$thb_height = $originalheight;
					}
					// Calculate thumbnails
					$thumbnail = self::thumbDimCalc($width, $height, ($thb_width * $width)/$originalwidth, ($thb_height * $height) /$originalheight, $smartResize);
					$thumb_width = $thumbnail['width'];
					$thumb_height = $thumbnail['height'];

					// Create an image resource for the thumbnail
					$thumb = imagecreatetruecolor($thumb_width, $thumb_height);

					// Create the resized copy
					imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $width, $height);

					// Convert and save all thumbs to .jpg
					$success = imagejpeg($thumb, $thumbimage, $jpg_quality);

					// Bail out if there is a problem in the GD conversion
					if (!$success) {
						array_push($errors, "Error jpeg generation");
						$ret = false;
					}

					// Remove the image resources from memory
					imagedestroy($source);
					imagedestroy($thumb);
					$ret = true;
				}
			}
		}
		return $ret;
	}

	function generatethumbs($rootdir, $directory) {
		$errors = array();
		foreach (array("jpg", "JPG") as $ext) {
			$dir = JGalleryHelper::join_paths(JPATH_ROOT, $rootdir,  $directory);
			foreach (glob($dir . "/*.$ext") as $filename) {
				foreach (self::$_formats as $format) {
					$thumb = self::getthumb($dir, $format, basename($filename));
					$width = JParametersHelper::get('thumb_' . $format .'_width');
					$height = JParametersHelper::get('thumb_' . $format . '_height');
					$quality = JParametersHelper::get('thumb_quality');
					self::generatethumb($filename, $thumb, $width, $height, $quality, False, $errors);
				}
			}
		}
	}
	
	
		
	
	public static function deletethumbs($rootdir, $directory, $image, &$errors) {
        JLog::add("deletethumbs:" . $image, JLog::WARNING, 'com_jgallery');
		$dir = JGalleryHelper::join_paths(JPATH_ROOT, $rootdir,  $directory);
        JLog::add("deletethumbs:2:" . $image, JLog::WARNING, 'com_jgallery');
		foreach (self::$_formats as $format) {
			$filename = self::getthumb($dir, $format, $image);
			JLog::add("deletethumbs:" . $filename, JLog::WARNING, 'com_jgallery');
			if (file_exists($filename)){
				array_push($errors, "success deleting " . $filename);
                JLog::add("deletethumbs:delete:" . $filename, JLog::WARNING, 'com_jgallery');
				unlink($filename);
			} else {
                JLog::add("deletethumbs:dos not exist:" . $filename, JLog::WARNING, 'com_jgallery');
				array_push($errors, "file does not exist " . $filename);
			}
		}
	}

	public static function generatethumbimage($rootdir, $directory, $filename, $forced, $small_width, $large_width) {
		$error = false;
		$errors = array();
		$dir = JGalleryHelper::join_paths(JPATH_ROOT, $rootdir,  $directory);        
		foreach (self::$_formats as $format) {
			$thumb = self::getthumb($dir, $format, basename($filename));
			$width = JParametersHelper::get('thumb_' . $format .'_width');
			$height = JParametersHelper::get('thumb_' . $format . '_height');
			if ($format == "small" && $small_width != 0) {
				$height = $height * $small_width/ $width;
				$width = $small_width;
			}
			if ($format == "large" && $large_width != 0) {
				$height = $height * $large_width/ $width;
				$width = $large_width;
			}
			$quality = JParametersHelper::get('thumb_quality');
			if (!self::generatethumb(JGalleryHelper::join_paths($dir, $filename), $thumb, $width, $height, $quality, $forced, $errors)) {
				array_push($errors , "Error in generation of $filename => $thumb");
				$error = true;
			}
		}
		if ($error == false) {
			return array($filename, "OK", ["Generation of thumb for <img src=\"". self::getthumbURL($rootdir, $directory,"small", $filename ) ."\"></img> is OK"]);
		}else {
			return array($filename, "ERR",$errors);
		}

	}
	public static function display($id, $_params, $recurse = false)
	{
		$content = "";
		if (is_array( $_params )== false)
		{
			return  "errorf:" . print_r($_params, true);
		}
		if (! array_key_exists('dir', $_params))
		{
			return  "errorf: missing dir param" . print_r($_params, true);
		}
		if ( array_key_exists('rootdir', $_params))
		{
			$rootdir = $_params['rootdir'];
		} else {
			$rootdir = ".";
		}
		$directory = $_params['dir'];
		$dir = utf8_decode(html_entity_decode(JGalleryHelper::join_paths(JPATH_ROOT, $rootdir,  $directory)));
		if (!is_dir($dir)) {
			$content .= "Directory does not exists :". $dir;
		} else {
			if ($recurse) {
				$scripts = array();
				JDirectoryHelper::outputdirs($id, $dir, $directory, $content, 'recthumbs');
			} else {
				$scripts = array('jgallery.js');
				JDirectoryHelper::outputdirs($id, $dir, $directory, $content, 'selectthumbs');
			}
		}
		
		return $content;
	}
}	