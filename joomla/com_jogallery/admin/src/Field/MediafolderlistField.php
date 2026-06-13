<?php
/**
 * @package     JOCoaching
 * @subpackage  com_jocoaching
 * @author     JL Tryoen http://www.jltryoen.fr
 * @copyright   Copyright (C) 2011 - 2026 JL Tryoen, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace JLTRY\Component\JOGallery\Administrator\Field;
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\FolderlistField;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;
use JLTRY\Component\JOGallery\Administrator\Helper\FoldergroupHelper;

/**
 * JCoaching Form Field class for the JCoaching component
 *
 * @since  0.0.1
 */
class MediafolderlistField extends FolderlistField
{
	/**
	 * The field type.
	 *
	 * @var         string
	 */
	protected $type = 'Mediafolderlist';


    protected function getOptions()
    {
        $options = [];
        Log::add("Mediafolderlist:getOptions", Log::WARNING, 'com_jogallery');
        // Prepend some default options based on field attributes.
        if (!$this->hideNone) {
            $options[] = HTMLHelper::_('select.option', '-1', Text::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
        }

        if (!$this->hideDefault) {
            $options[] = HTMLHelper::_('select.option', '', Text::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
        }

        // Get a list of folders in the search path with the given filter.
        $folders = FoldergroupHelper::getFolders(".", $this->folderFilter, $this->recursive, true);

        // Build the options list from the list of folders.
        if (\is_array($folders)) {
            foreach ($folders as $folder) {
                // Remove the root part and the leading /
                $folder = $folder['relative'];

                // Check to see if the file is in the exclude mask.
                if ($this->exclude) {
                    if (preg_match(\chr(1) . $this->exclude . \chr(1), $folder)) {
                        continue;
                    }
                }

                $options[] = HTMLHelper::_('select.option', $folder, $folder);
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(ListField::getOptions(), $options);

        return $options;
    }
}
