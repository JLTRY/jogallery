<?php
/**
* @copyright Copyright (C) 2025 Jean-Luc TRYOEN. All rights reserved.
*
* Version 1.0.6
*
* @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* @link        https://www.jltryoen.fr
*/


namespace JLTRY\Plugin\EditorsXtd\JOGallery\Extension;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Editor\Button\Button;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Event\Editor\EditorButtonsSetupEvent;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Button that allows you to insert an {attachments} token into the text from the editor
 *
 * @package Attachments
 */
class JOGallery extends CMSPlugin implements SubscriberInterface
{
    /**
     * $db and $app are loaded on instantiation
     */
    protected ?DatabaseDriver $db = null;
    protected ?CMSApplication $app = null;

    /**
     * Load the language file on instantiation
     *
     * @var    boolean
     */
    protected $autoloadLanguage = true;

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     */
    public static function getSubscribedEvents(): array
    {
        return ['onEditorButtonsSetup' => 'onEditorButtonsSetup'];
    }

    public function onEditorButtonsSetup(EditorButtonsSetupEvent $event)
    {
        $subject  = $event->getButtonsRegistry();
        $disabled = $event->getDisabledButtons();

        if (\in_array($this->_name, $disabled)) {
            return;
        }

        $this->loadLanguage();

        $button = $this->onDisplay($event->getEditorId());

        if ($button) {
            $subject->add(new Button($this->_name, $button->getProperties()));
        }
    }
    /**
     * Insert attachments token button
     *
     * @param string $name The name of the editor form
     * @param int $asset The asset ID for the entity being edited
     * @param int $author The ID of the author of the entity
     *
     * @return a button
     */
    public function onDisplay($name)
    {

        $link = "/index.php?option=com_jogallery&tmpl=component&view=jogallery&layout=insertjogallery";
        $link .= '&amp;editor=' . $name;

        $button = new CMSObject();
        $button->modal = true;
        $button->class = 'btn';
        $button->text = Text::_('Insert JGallery');
        $button->name = 'jogallery';
        $button->link = $link;
        $button->options = "{handler: 'iframe', size: {x: 920, y: 530}}";


        return $button;
    }
}
