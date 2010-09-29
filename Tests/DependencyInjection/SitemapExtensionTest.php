<?php

namespace Bundle\SitemapBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Bundle\SitemapBundle\DependencyInjection\SitemapExtension;

/**
 * SitemapExtensionTest
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class SitemapExtensionTest extends \PHPUnit_Framework_TestCase
{

    public function testSitemapLoad()
    {
        $configuration = new ContainerBuilder();
        $loader = new SitemapExtension('testkernel');
        $configuration = $loader->configLoad(array('driver' => 'odm.mongodb'), $configuration);
        $this->assertEquals('Bundle\SitemapBundle\Sitemap\Sitemap', $configuration->getParameter('sitemap.class'), '->sitemapLoad() merges default configration parameters');
        $this->assertHasDefinition('sitemap.sitemap', $configuration, '->sitemapLoad() defines sitemap service');
    }

    public function testSitemapLoadWithDefaults()
    {
        $configuration = new ContainerBuilder();
        $loader = new SitemapExtension('testkernel');
        $configuration = $loader->configLoad(array(
                'driver' => 'odm.mongodb',
                'default_changefreq' => 'daily',
                'default_lastmod' => strtotime('2006-01-01'),
                'default_priority' => 0.3,
                ), $configuration);
        $this->assertEquals(array(
            'changefreq' => 'daily',
            'lastmod' => strtotime('2006-01-01'),
            'priority' => 0.3,
            ), $configuration->getParameter('sitemap.defaults'), '->sitemapLoad() merges default configration parameters');
        $this->assertHasDefinition('sitemap.dumper', $configuration, '->sitemapLoad() defines sitemap.dumper service');
    }

    public function assertHasDefinition($id, ContainerBuilder $configuration, $message = '')
    {
        $this->assertTrue(($configuration->hasDefinition($id) ? : $configuration->hasAlias($id)), $message);
    }

}