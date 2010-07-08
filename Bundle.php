<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle;

use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Foundation\Bundle\Bundle as BaseBundle;
use Symfony\Components\DependencyInjection\Loader\Loader;
use Bundle\SitemapBundle\DependencyInjection\SitemapExtension;
use Bundle\SitemapBundle\Sitemap\Sitemap;

/**
 * Description of Bundle
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class Bundle extends BaseBundle {
    public function buildContainer(ContainerInterface $container) {
        Loader::registerExtension(new SitemapExtension());
    }
    public function boot(ContainerInterface $container) {
        if ( ! file_exists($container->getParameter('sitemap_dumper.file.name'))) {
            return $container->setService('sitemap', $container->getService('sitemap.sitemap'));
        }
        require_once $container->getParameter('sitemap_dumper.file.name');
        return $container->setService('sitemap', $container->getService('sitemap.sitemap.dump'));
    }
}