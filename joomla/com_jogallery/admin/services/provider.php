<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_jogallery
 *
 * @copyright   Copyright (C) 2015 - 2025 JL TRYOEN All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->registerServiceProvider(
            new MVCFactory("\\JLTRY\\Component\\JOGallery")
        );
        $container->registerServiceProvider(
            new ComponentDispatcherFactory(
                "\\JLTRY\\Component\\JOGallery"
            )
        );
        $container->set(ComponentInterface::class, function (
            Container $container
        ) {
            $component = new MVCComponent(
                $container->get(ComponentDispatcherFactoryInterface::class)
            );
            $component->setMVCFactory(
                $container->get(MVCFactoryInterface::class)
            );

            return $component;
        });
    }
};