<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Tests\DependencyInjection;

use Symfony\Components\DependencyInjection\BuilderConfiguration;
use Bundle\SitemapBundle\DependencyInjection\SitemapExtension;

/**
 * Description of SitemapExtensionTest
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright The OpenSky Project Inc. 2010
 * @link http://www.theopenskyproject.com/
 */
class SitemapExtensionTest extends \PHPUnit_Framework_TestCase {
    public function testSitemapLoad() {
        $configuration = new BuilderConfiguration();
        $loader = new SitemapExtension('testkernel');
        $configuration = $loader->configLoad(array(), $configuration);
        $this->assertEquals('Bundle\SitemapBundle\Sitemap\Sitemap', $configuration->getParameter('sitemap.class'), '->sitemapLoad() merges default configration parameters');
        $this->assertHasDefinition('sitemap.sitemap', $configuration, '->sitemapLoad() defines sitemap service');
    }
    public function testSitemapLoadWithDefaults() {
        $configuration = new BuilderConfiguration();
        $loader = new SitemapExtension('testkernel');
        $configuration = $loader->configLoad(array(
            'default_changefreq' => 'daily',
            'default_lastmod'    => strtotime('2006-01-01'),
            'default_priority'   => 0.3,
        ), $configuration);
        $this->assertEquals(array(
            'changefreq' => 'daily',
            'lastmod'    => new \DateTime('2006-01-01'),
            'priority'   => 0.3,
        ), $configuration->getParameter('sitemap.defaults'), '->sitemapLoad() merges default configration parameters');
    }
    public function assertHasDefinition($id, BuilderConfiguration $configuration, $message = '') {
        $this->assertTrue($configuration->hasDefinition($id), $message);
    }
}