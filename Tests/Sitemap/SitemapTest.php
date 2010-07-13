<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Tests\Sitemap;

use Bundle\SitemapBundle\Sitemap\Sitemap;
use Bundle\SitemapBundle\Sitemap\Url;

/**
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright The OpenSky Project Inc. 2010
 * @link http://www.theopenskyproject.com/
 */
class SitemapTest extends \PHPUnit_Framework_TestCase {
    public function testNoUrls() {
        $sitemap = $this->getSitemap();
        $this->assertEquals(0, count($sitemap->getUrls()));
    }
    public function testAddUrl() {
        $sitemap = $this->getSitemap();
        $sitemap->add('http://www.example.org/', array(
            'lastmod' => new \DateTime(),
            'changefreq' => Url::DAILY,
            'priority' => 1.0,
        ));
        $this->assertEquals(1, count($sitemap->getUrls()));
    }
    public function testAddGetsUrl() {
        $sitemap = $this->getSitemap();
        $lastMod = new \DateTime();
        $url = $sitemap->add('http://www.example.org/', array(
            'lastmod' => $lastMod,
            'changefreq' => Url::DAILY,
            'priority' => 1.0,
        ));
        $this->assertUrlProperties(array(
            'loc'        => 'http://www.example.org/',
            'lastmod'    => $lastMod,
            'changefreq' => 'daily',
            'priority'   => 1.0,
        ), $url);
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsOnInvalidUrlClass() {
        $sitemap = $this->getSitemap();
        $sitemap->setUrlClass(get_class($this));
    }
    public function testGetUrlClass() {
        $sitemap = $this->getSitemap();
        $sitemap->setUrlClass('Bundle\SitemapBundle\Sitemap\Url');
        $this->assertEquals('Bundle\SitemapBundle\Sitemap\Url', $sitemap->getUrlClass());
    }
    public function testAddsDefaultInfo() {
        $sitemap = $this->getSitemap(array(
            'changefreq' => 'monthly',
            'lastmod'    => new \DateTime('2006-01-01'),
            'priority'   => 0.1,
        ));
        $url = $sitemap->add('http://www.google.com/', array());
        $this->assertUrlProperties(array(
            'loc'        => 'http://www.google.com/',
            'changefreq' => 'monthly',
            'lastmod'    => new \DateTime('2006-01-01'),
            'priority'   => 0.1,
        ), $url);
    }
    public function testAppendsDefaultInfo() {
        $sitemap = $this->getSitemap(array(
            'changefreq' => 'monthly',
            'lastmod'    => new \DateTime('2006-01-01'),
            'priority'   => 0.1,
        ));
        $url = $sitemap->add('http://www.google.com/', array(
            'lastmod'    => new \DateTime('2010-01-01'),
            'priority'   => 0.6,
        ));
        $this->assertUrlProperties(array(
            'loc'        => 'http://www.google.com/',
            'changefreq' => 'monthly',
            'lastmod'    => new \DateTime('2010-01-01'),
            'priority'   => 0.6,
        ), $url);
    }
    public function testGetUrl() {
        $now = new \DateTime();
        $sitemap = $this->getSitemap();
        $sitemap->add('http://www.example.org/', array(
            'lastmod' => $now,
            'changefreq' => Url::MONTHLY,
            'priority' => 1.0,
        ));
        $sitemap->add('http://www.example.org/index', array(
            'lastmod' => $now,
            'changefreq' => Url::DAILY,
            'priority' => 0.8,
        ));
        $this->assertUrlProperties(array(
            'loc'        => 'http://www.example.org/',
            'lastmod'    => $now,
            'changefreq' => Url::MONTHLY,
            'priority'   => 1.0,
        ), $sitemap->get('http://www.example.org/'));
    }
    public function testGetUrlReturnsNullIfNoFound() {
        $sitemap = $this->getSitemap();
        $this->assertNull($sitemap->get('http://www.example.org'));
    }
    public function testHasReturnsFalseOnNonExistentRoute() {
        $this->assertFalse($this->getSitemap()->has('http://www.google.com/'));
    }
    public function testReturnsTrueOnExistentUrl() {
        $sitemap = $this->getSitemap();
        $sitemap->add('http://www.google.com/', array(
            'lastmod'    => new \DateTime('2010-01-01'),
            'priority'   => 0.6,
        ));
        $this->assertTrue($sitemap->has('http://www.google.com/'));
    }
    public function getSitemap(array $options = array()) {
        return new Sitemap($options);
    }
    public function assertUrlProperties(array $properties, Url $url, $message = 'Url properties didn\'t match specification') {
        foreach (array('loc', 'lastmod', 'changefreq', 'priority') as $prop) {
            if (isset ($properties[$prop]) && $properties[$prop] != $url->{'get' . ucfirst($prop)}()) {
                $this->fail($message . PHP_EOL .'property: ' . $prop . PHP_EOL . 'expected: ' . print_r($properties[$prop], true) . PHP_EOL . 'actual: ' . print_r($url->{'get' . ucfirst($prop)}(), true));
            }
        }
    }
}