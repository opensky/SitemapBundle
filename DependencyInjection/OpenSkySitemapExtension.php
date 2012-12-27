<?php

namespace OpenSky\Bundle\SitemapBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * OpenSkySitemapExtension
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 */
class OpenSkySitemapExtension extends Extension
{

    /**
     * Loads OpenSky Sitemap configuration into the container:
     *
     *     opensky_sitemap:
     *         default_lastmod:    2010-06-01
     *         default_changefreq: monthly
     *         default_priority:   0.5
     *
     * @see Symfony\Component\DependencyInjection\Extension\ExtensionInterface::load()
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('sitemap.xml');

        $defaults = $container->getParameter('opensky.sitemap.defaults');
        foreach (Processor::normalizeKeys($configs) as $config) {
            foreach (array('changefreq', 'priority', 'lastmod') as $prop) {
                if (isset($config['default_' . $prop])) {
                    $defaults[$prop] = $config['default_' . $prop];
                }
            }
        }

        $container->setParameter('opensky.sitemap.defaults', $defaults);
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension\ExtensionInterface::getAlias()
     * @codeCoverageIgnore
     */
    public function getAlias()
    {
        return 'opensky_sitemap';
    }
}