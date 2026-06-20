<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 Open Source Matters. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;

use JLTRY\Component\JOGallery\Administrator\Helper\JThumbsHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JODirectoryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JParametersHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

$urlroot = Uri::root(true);
HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.formvalidator');
define("JPATH_LAYOUTS", JPATH_ADMINISTRATOR . '/components/com_jogallery/layouts');

$bar = Toolbar::getInstance('toolbar');
$bar->appendButton(
    'Custom',
    JODirectoryHelper::display(
        1,
        array("dir" => JOGalleryHelper::joinPaths($this->directory),
              "rootdir" => JOGalleryHelper::joinPaths($this->rootdir),
              "type" => "selectdirsmenu")
    ),
    ""
);
$bar->appendButton(
    'Custom',
    LayoutHelper::render(
        'menuthumbs',
        array('id' => 1,
              'small_width' => JParametersHelper::get('thumb_small_width'),
              'large_width' => JParametersHelper::get('thumb_large_width'),
       )
    ),
    ""
);
JOGalleryHelper::loadLibrary(array("jimages" => true,
                                    "jthumbs" => true,
                                    "fancybox" => true,
                                    "jogallery" => true));
$id = '1';
$urlroot = Uri::root(true);
JOGalleryHelper::loadLibrary(array("inline" =>
                                array('(function($) {
                                    $(document).ready(function() {
                                        new thumbretriever($,"'. $id . '", ' .
                                                       '"' . $urlroot . '"' .
                                    ')})})(jQuery);',
                                      ['position' => 'after'],
                                      [],
                                      ['com_jogallery.jthumbs'])));
?>


<form action="<?php echo Route::_('index.php?option=com_jogallery&layout=thumbs&id=' . (int)$this->item->id); ?>"
        method="post" name="adminForm" id="adminForm" class="form-validate">
    <input type="hidden" name="task" value="jogallery.thumbs" />
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
<?php echo LayoutHelper::render('jimages', array('id' => 1), JPATH_LAYOUTS); ?>
