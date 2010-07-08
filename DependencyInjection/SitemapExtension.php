<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\DependencyInjection;

use Symfony\Components\DependencyInjection\Loader\LoaderExtension;
use Symfony\Components\DependencyInjection\BuilderConfiguration;
use Symfony\Components\DependencyInjection\Loader\XmlFileLoader;

/**
 * The wiring behind the sitemap, this is what creates the 'sitemap' service.
 *
 * To enable the sitemap service you would do something like:
 *
 * sitemap.sitemap: ~
 *
 * Configuration options are also available, here is the fully configured example:
 *
 * sitemap.sitemap:
 *   default_lastmod:    2006-05-05
 *   default_changefreq: monthly
 *   default_priority:   0.2
 *
 * The defaults are good to have, but sitemap providers should be setting
 * the correct values themselves.
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class SitemapExtension extends LoaderExtension {

    protected $resources = array(
        'sitemap' => 'sitemap.xml'
    );

    public function configLoad($config, BuilderConfiguration $configuration) {
        if ( ! $configuration->hasDefinition('sitemap')) {
            $loader = new XmlFileLoader(__DIR__.'/../Resources/config');
            $configuration->merge($loader->load($this->resources['sitemap']));
        }
        $defaults = $configuration->getParameter('sitemap.defaults');
        $defaults['lastmod'] = new \DateTime((isset($defaults['lastmod']) ? '@' . $defaults['lastmod'] : null));
        if (isset($config['default_lastmod'])) {
            $defaults['lastmod']->setTimestamp($config['default_lastmod']);
        }
        foreach(array('changefreq', 'priority') as $prop) {
            if (isset($config['default_' . $prop])) {
                $defaults[$prop] = $config['default_' . $prop];
            }
        }

        $configuration->setParameter('sitemap.defaults', $defaults);

        return $configuration;
    }

    /**
     * Returns the recommended alias to use in XML.
     *
     * This alias is also the mandatory prefix to use when using YAML.
     *
     * @return string The alias
     */
    public function getAlias()
    {
        return 'sitemap';
    }

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     */
    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/sitemap';
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/';
    }
}