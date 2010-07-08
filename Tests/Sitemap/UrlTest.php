<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Tests\Sitemap;

use Bundle\SitemapBundle\Sitemap\Url;

/**
 * Description of UrlTest
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class UrlTest extends \PHPUnit_Framework_TestCase {
    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsOnInvalidUrl() {
        new Url('somdasdasd');
    }
    public function testGetLoc() {
        $url = new Url('http://www.example.org/');
        $this->assertEquals('http://www.example.org/', $url->getLoc());
    }
    public function testSetLastmod() {
        $url = new Url('http://www.example.org/');
        $now = new \DateTime();
        $url->setLastmod($now);
        $this->assertEquals($now, $url->getLastmod());
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsOnInvalidChangefreq() {
        $url = new Url('http://www.example.org/');
        $url->setChangefreq('somefreq');
    }
    public function testGetChangefreq() {
        $url = new Url('http://www.example.org/');
        $url->setChangefreq(Url::DAILY);
        $this->assertEquals(Url::DAILY, $url->getChangefreq());
    }
    public function testGetPriority() {
        $url = new Url('http://www.example.org/');
        $url->setPriority(1.0);
        $this->assertEquals(1.0, $url->getPriority());
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsOnInvalidPriority() {
        $url = new Url('http://www.example.org/');
        $url->setPriority(2.0);
    }
}