<?php

namespace Bundle\SitemapBundle\Tests\Listener;

use Bundle\SitemapBundle\Listener\SitemapListener;
use Symfony\Component\EventDispatcher\Event;
use Bundle\SitemapBundle\Sitemap\Sitemap;
use Bundle\SitemapBundle\Sitemap\Storage\Memory;
use Bundle\SitemapBundle\Sitemap\Url;

/**
 * SitemapListenerTest
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class SitemapListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Bundle\SitemapBundle\Listener\SitemapListener
     */
    protected $listener;
    /**
     * @var Bundle\SitemapBundle\Sitemap\Sitemap
     */
    protected $sitemap;
    /**
     * @var Bundle\SitemapBundle\Dumper\Dumper
     */
    protected $dumper;

    public function setUp()
    {
        $this->sitemap = $this->getSitemap();
        $this->dumper = $this->getDumper();
        $this->listener = new SitemapListener($this->sitemap, $this->dumper);
    }

    public function tearDown()
    {
        unset($this->sitemap, $this->dumper, $this->listener);
    }

    public function testRegister()
    {
        $connectedEvents = array();

        $eventDispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher', array('connect'), array(), '', false, false);
        $eventDispatcher->expects($this->exactly(2))
            ->method('connect')
            ->will($this->returnCallback(function ($eventName, $callback) use (&$connectedEvents) {
                        $connectedEvents[$eventName] = $callback;
                    }));

        $this->listener->register($eventDispatcher);

        foreach (array('update', 'create') as $name) {
            $eventName = 'sitemap.' . $name;
            $this->assertArrayHasKey($eventName, $connectedEvents);
            $this->assertEquals(array($this->listener, $name), $connectedEvents[$eventName]);
        }
    }

    public function testCreate()
    {
        $this->dumper->expects($this->once())
            ->method('dump')
            ->with($this->sitemap);

        $urlData = array(
            'loc' => 'http://google.com',
            'changefreq' => 'monthly',
            'priority' => '1',
        );

        $this->listener->create(new Event($this, 'sitemap.create', $urlData));

        $this->assertTrue($this->sitemap->has($urlData['loc']));

        $url = $this->sitemap->get($urlData['loc']);

        $this->assertEquals($urlData, array(
            'loc' => $url->getLoc(),
            'changefreq' => $url->getChangefreq(),
            'priority' => $url->getPriority(),
        ));
        $this->assertEquals(date(Url::LASTMOD_FORMAT), $url->getLastmod());
    }

    public function testUpdate()
    {
        $url = 'http://google.com';

        $entry = $this->sitemap->add($url, array(
                'lastmod' => new \DateTime('-2 months'),
                'changefreq' => Url::YEARLY,
                'priority' => '0.8',
            ));

        $this->dumper->expects($this->once())
            ->method('dump')
            ->with($this->sitemap);

        $this->listener->update(new Event($this, 'sitemap.update', array('loc' => $url)));

        $this->assertEquals(date(Url::LASTMOD_FORMAT), $entry->getLastmod());
        $this->assertEquals(Url::MONTHLY, $entry->getChangefreq());
    }

    /**
     * @expectedException Bundle\SitemapBundle\Exception\OutOfBoundsException
     */
    public function testUpdateThrowsException()
    {
        $this->listener->update(new Event($this, 'sitemap.update', array('loc' => 'http://www.example.com')));
    }

    /**
     * @return Bundle\SitemapBundle\Sitemap\Sitemap
     */
    protected function getSitemap()
    {
        return new Sitemap(new Memory());
    }

    /**
     * @return Bundle\SitemapBundle\Dumper\Dumper
     */
    protected function getDumper()
    {
        return $this->getMock('Bundle\SitemapBundle\Dumper\Dumper', array('dump'), array(), '', false, false);
    }

}