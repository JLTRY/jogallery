<?php
/**
 * @package    JOGallery
 * @author     JLTryoen http://www.jltryoen.fr
 * @copyright  Copyright (C) 2015 - 2025 JLTryoen . All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
$data = JOGalleryHelper::getFiles($this->rootdir , $this->directory, $this->media);
JOGalleryHelper::json_answer($data);