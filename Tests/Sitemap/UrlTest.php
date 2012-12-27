<?php

namespace Bundle\SitemapBundle\Tests\Sitemap;

use Bundle\SitemapBundle\Sitemap\Url;

/**
 * UrlTest
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class UrlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException Bundle\SitemapBundle\Exception\InvalidArgumentException
     */
    public function testThrowsOnInvalidUrl()
    {
        new Url('somdasdasd');
    }

    public function testGetLoc()
    {
        $url = new Url('http://www.example.org/');
        $this->assertEquals('http://www.example.org/', $url->getLoc());
    }

    public function testSetLastmod()
    {
        $url = new Url('http://www.example.org/');
        $now = new \DateTime();
        $url->setLastmod($now);
        $this->assertEquals(date(Url::LASTMOD_FORMAT, $now->getTimestamp()), $url->getLastmod());
    }

    /**
     * @expectedException Bundle\SitemapBundle\Exception\InvalidArgumentException
     */
    public function testThrowsOnInvalidChangefreq()
    {
        $url = new Url('http://www.example.org/');
        $url->setChangefreq('somefreq');
    }

    public function testGetChangefreq()
    {
        $url = new Url('http://www.example.org/');
        $url->setChangefreq(Url::DAILY);
        $this->assertEquals(Url::DAILY, $url->getChangefreq());
    }

    public function testGetPriority()
    {
        $url = new Url('http://www.example.org/');
        $url->setPriority(1.0);
        $this->assertEquals(1.0, $url->getPriority());
    }

    public function testGetImageloc()
    {
        $url = new Url('http://www.example.org/');
        $url->setImageloc('http://www.example.org/image.jpg');
        $this->assertEquals('http://www.example.org/image.jpg', $url->getImageloc());
    }

    public function testGetImageloc()
    {
        $url = new Url('http://www.example.org/');
        $url->setImagetitle('Image Title');
        $this->assertEquals('Image Title', $url->getImagetitle());
    }

    /**
     * @expectedException Bundle\SitemapBundle\Exception\InvalidArgumentException
     */
    public function testThrowsOnInvalidPriority()
    {
        $url = new Url('http://www.example.org/');
        $url->setPriority(2.0);
    }

}