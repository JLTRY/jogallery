<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JLTRYOEN. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace JLTRY\Component\JOGallery\Site\View\JOGallery;

use JLTRY\Component\JOGallery\Site\Model\JOGalleryModel;
use JLTRY\Component\JOGallery\Administrator\Helper\JParametersHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryHelper;
use JLTRY\Component\JOGallery\Administrator\Helper\JOGalleryCategoryHelper;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory as Factory;
use Joomla\CMS\Language\Text as Text;
use Joomla\CMS\Helper\ModuleHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * JOGallery View
 *
 * @since  0.0.1
 */
class HtmlView extends BaseHtmlView
{
    public function getparam($param, $default = null)
    {
        $found = false;
        $app     = Factory::getApplication();
        $input   = $app->getInput();
        $params  = $app->getParams();
        if ($params->get($param) !== null) {
            $this->{$param} = $params->get($param);
            $found = true;
        } elseif ($input->get($param, $default) !== null) {
            $this->{$param} = $input->get($param);
            $found = true;
        }
        if (!$found) {
            $this->{$param} = $default;
        }
        return $found;
    }
    /**
     * Display the Gallery view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $canview = false;
        $catid = -1;
        $this->getparam('id', '-1');
        $this->getparam('media', "ALL");
        $this->getparam('lightbox', "fancybox"); 
        $this->getparam('fullscreen', "0"); 
        if ($this->item == null) {
            if ($this->id !== -1) {
                $model = new JOGalleryModel();
                $this->setModel($model);
                $this->item = $model->getItem((int)$this->id);
                $catid = $this->item->catid;
            }
        }
        if (!$this->getparam('directory')) {
            if ($this->getparam('directory64')) {
                $this->directory = utf8_decode(base64_decode($this->directory64));
            }
        }
        if ($this->item && !$this->directory) {
            $this->directory = $this->item->directory;
        }
        $user = Factory::getApplication()->getSession()->get('user');
        if (($catid == -1) || JOGalleryCategoryHelper::usercanviewcategory($user, $catid)) {
            $canview = true;
        } else {
            Factory::getLanguage()->load('com_content', JPATH_SITE, null, true);
            echo "<jdoc:include type=\"message\" />";
            Factory::getApplication()->enqueueMessage(Text::_('COM_CONTENT_ERROR_LOGIN_TO_VIEW_ARTICLE'), 'error');
            $document = Factory::getDocument();
            $renderer = $document->loadRenderer('module');
            $Module = ModuleHelper::getModule('mod_login');
            $uri = Uri::getInstance();
            $Module->params = "return=" . base64_encode($uri->toString());
            echo $renderer->render($Module);
        }
        if ($canview) {
            $this->rootdir = JParametersHelper::getrootdir();
            if (!is_string($this->directory)) {
                $errors = array("directory not defined");
            } else {
                $errors = $this->get('Errors');
            }
            $this->getparam('image', null);
            $this->getparam('page', '-1');
            if ($this->getparam('image64', null)) {
                $this->image = utf8_decode(base64_decode($this->image64));
            }
            $this->getparam('parent', false);
// Check for errors.
            if (is_array($errors) && count($errors)) {
                Log::add(implode('<br />', $errors), Log::WARNING, 'jerror');
                return false;
            }
            // Display the view
            parent::display($tpl);
        }
    }
}
