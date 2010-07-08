<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Tests\Listener;

use Bundle\SitemapBundle\Listener\ResponseFilter;
use Symfony\Components\EventDispatcher\Event;
use Symfony\Components\HttpKernel\HttpKernelInterface;
use Symfony\Components\HttpKernel\Response;
use Symfony\Components\HttpKernel\Request;
use Bundle\SitemapBundle\Sitemap\Url;

/**
 * Description of ResponseFilterTest
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class ResponseFilterTest extends \PHPUnit_Framework_TestCase {
    protected $sitemap;
    protected $dumper;
    protected $filter;
    public function setUp() {
        $this->sitemap = $this->getMock('Bundle\SitemapBundle\Sitemap\Sitemap', array('getCurrent'), array(), '', false);
        $this->dumper = $this->getMock('Bundle\SitemapBundle\Dumper\Dumper', array('dump'), array(), '', false);
        $this->filter = new ResponseFilter($this->sitemap, $this->dumper, $this->getUrl());
    }
    public function tearDown() {
        unset($this->sitemap, $this->dumper);
    }
    public function testRegistersToCorrectEvent() {
        $dispatcher = $this->getEventDispatcherMock();
        $dispatcher->expects($this->once())
            ->method('connect')
            ->with('core.response', array($this->filter, 'filter'));
        $this->filter->register($dispatcher);
    }
    public function testDoesntProcessNotMasterRequests() {
        $response = new Response();
        $this->filter->filter(new Event($this, 'core.response', array('request_type' => HttpKernelInterface::EMBEDDED_REQUEST)), $response);
        $this->assertNull($response->headers->get('Last-Modified'));
    }
    public function testDoesntProcessNotSitemapUrlRequests() {
        $response = new Response();
        $this->filter->filter(new Event($this, 'core.response', array('request_type' => HttpKernelInterface::MASTER_REQUEST)), new Response());
        $this->assertNull($response->headers->get('Last-Modified'));
    }
    public function testSetsResponseLastModifiedHeader() {
        $url = $this->getUrl();
        $this->dumper->expects($this->once())
            ->method('dump');
        $response = new Response();
        $this->filter->filter(new Event($this, 'core.response', array(
            'request_type' => HttpKernelInterface::MASTER_REQUEST,
            'request'      => new Request(),
        )), $response);
        $this->assertEquals($response->headers->get('Last-Modified'), date(DATE_RFC2822, strtotime('2006-01-01')));
    }
    public function testSetsUrlLastModProperty() {
        $url = $this->getUrl();
        $this->dumper->expects($this->once())
            ->method('dump');
        $response = new Response();
        $response->headers->set('Last-Modified', date(DATE_RFC2822));
        $this->filter->filter(new Event($this, 'core.response', array(
            'request_type' => HttpKernelInterface::MASTER_REQUEST,
            'request'      => new Request(),
        )), $response);
        $this->assertEquals($response->headers->get('Last-Modified'), date(DATE_RFC2822));
    }
    public function getEventDispatcherMock() {
        return $this->getMock('Symfony\Components\EventDispatcher\EventDispatcher', array('connect'), array(), '', false);
    }
    public function getUrl() {
        $url = new Url('http://www.google.com/');
        $url->setLastmod(new \DateTime('2006-01-01'));
        $url->setChangefreq(Url::DAILY);
        $url->setPriority(0.6);
        return $url;
    }
}