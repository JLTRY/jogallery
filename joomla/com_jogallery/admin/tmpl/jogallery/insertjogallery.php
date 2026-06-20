<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
                JL Tryoen 
/-------------------------------------------------------------------------------------------------------/

    @version		1.0.6
    @build			30th May, 2026
    @created		12th August, 2025
    @package		JOGallery
    @subpackage		default.php
    @author			Jean-Luc Tryoen <http://www.jltryoen.fr>	
    @copyright		Copyright (C) 2025. All Rights Reserved
    @license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html

/------------------------------------------------------------------------------------------------------*/

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper as Html;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JODirectoryHelper;

// No direct access to this file
defined('_JEXEC') or die;
define("JPATH_LAYOUTS", JPATH_ADMINISTRATOR . '/components/com_jogallery/layouts');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getDocument()->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate');
Html::_('bootstrap.tooltip');

//load languages
$language = Factory::getLanguage();
$language->load('com_media', JPATH_ADMINISTRATOR);
$language->load('com_media', JPATH_SITE);
$language->load('com_jogallery', JPATH_ADMINISTRATOR, null, true);
$urlroot = Uri::root(true);
JOGalleryHelper::loadLibrary(array( "jquery" => true,
                                    "jimages" => true, 
                                    "jthumbs" => true,
                                    "jogallery" => true,
                                    "insertjogallery" => true));
JOGalleryHelper::loadLibrary(array("inline" =>
                                array('(function($) {
                                    $(document).ready(function() {
                                        new insertjogallery($,"jform_folders", "jimages1", ' .
                                                       '"' . $urlroot . '"' .
                                    ')})})(jQuery);',
                                      ['position' => 'after'],
                                      [],
                                      ['com_jogallery.insertjogallery'])));
?>
<form name="adminForm" id="adminForm" class="form-validate timepair">
<div class="main-card">
    <button type="button" class="btn btn-success" onclick="insertJoGallery(jQuery, '<?php echo $this->editor ?>');">
        <?php echo Text::_('COM_JOGALLERY_INSERT_GALLERY'); ?>
    </button>
    <form method="post" name="adminForm" id="adminForm" class="form-validate">
        <?php 
        echo $this->form->renderFieldset('folders', array()); 
        echo $this->form->renderFieldset('details', array()); 
        echo LayoutHelper::render('jimages', array('id' => 1), JPATH_LAYOUTS);
        ?>
    </form>
</div>



