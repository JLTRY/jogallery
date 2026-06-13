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

use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JODirectoryHelper;


/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getDocument()->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate');
Html::_('bootstrap.tooltip');

// No direct access to this file
defined('_JEXEC') or die;

?>



<?php
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getDocument()->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate');
Html::_('bootstrap.tooltip');
?>
<form  action="<?php echo Route::_('index.php?option=com_jofacebook&view=jofbkpost&layout=' . $layout . $tmpl . '&id='. (int) $this->item->id . $this->referral); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

<div class="main-card">
    <button type="button" class="btn btn-success" onclick="insertJoGallery(jQuery, '<?php echo $this->editor ?>');">
        <?php echo Text::_('COM_JOGALLERY_INSERT_GALLERY'); ?>
    </button>
    <form method="post" name="adminForm" id="adminForm" class="form-validate">
        <?php 
        echo $this->form->renderFieldset('details', array()); 
        echo JODirectoryHelper::display(1, array("dir" => JOGalleryHelper::joinPaths(""),
                                        "rootdir" => JOGalleryHelper::joinPaths($this->rootdir),
                                        "type" => "insertjogallery")); 
        ?>
    </form>
</div>



