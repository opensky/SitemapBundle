<?php

namespace Bundle\SitemapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * SitemapExtension
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class SitemapExtension extends Extension
{
    protected $resources = array(
        'sitemap' => 'sitemap.xml'
    );

    public function configLoad($config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sitemap')) {
            $loader = new XmlFileLoader($container, __DIR__ . '/../Resources/config');
            $loader->load($this->resources['sitemap']);
        }
        if (isset($config['driver'])) {
            foreach (array('sitemap.dumper', 'sitemap.sitemap.storage') as $service) {
                $container->setAlias($service, $service . '.' . $config['driver']);
            }
        }
        $defaults = $container->getParameter('sitemap.defaults');
        foreach (array('changefreq', 'priority', 'lastmod') as $prop) {
            if (isset($config['default_' . $prop])) {
                $defaults[$prop] = $config['default_' . $prop];
            }
        }

        $container->setParameter('sitemap.defaults', $defaults);
        return $container;
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
        return __DIR__ . '/../Resources/config/';
    }

}