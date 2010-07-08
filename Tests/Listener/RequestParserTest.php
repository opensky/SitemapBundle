<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Tests\Listener;

use Symfony\Components\EventDispatcher\Event;
use Symfony\Components\HttpKernel\HttpKernelInterface;
use Symfony\Components\HttpKernel\Request;
use Symfony\Components\Routing\Route;
use Bundle\SitemapBundle\Sitemap\Url;
use Bundle\SitemapBundle\Listener\RequestParser;

/**
 * Description of RequestParserTest
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class RequestParserTest extends \PHPUnit_Framework_TestCase {
    protected $parser;
    protected $router;
    protected $sitemap;
    protected $container;
    public function setUp() {
        $this->router = $this->getMock('Symfony\Components\Routing\Router', array('getRouteCollection'), array(), '', false);
        $this->sitemap = $this->getMock('Bundle\SitemapBundle\Sitemap\Sitemap', array('add', 'get', 'setCurrent'), array(), '', false);
        $this->container = $this->getMock('Symfony\Components\DependencyInjection\Container', array('setService'));
        $this->parser = new RequestParser($this->router, $this->sitemap, $this->container);
    }
    public function tearDown() {
        unset ($this->parser, $this->router, $this->sitemap);
    }
    public function testConnectsToTheRightEvent() {
        $dispatcher = $this->getEventDispatcherMock();
        $dispatcher->expects($this->once())
            ->method('connect')
            ->with('core.request', array($this->parser, 'process'));
        $this->parser->register($dispatcher);
    }
    public function testDoesntProcessNotMainRequests() {
        $this->sitemap->expects($this->never())
            ->method('add');
        $this->sitemap->expects($this->never())
            ->method('setCurrent');
        $this->parser->process(new Event($this, 'core.request', array('request_type' => HttpKernelInterface::EMBEDDED_REQUEST)));
    }
    public function testDoesntProcessRequestWithNoRoute() {
        $request = new Request(array(), array(), array(), array(), array(), array());
        $this->sitemap->expects($this->never())
            ->method('add');
        $this->sitemap->expects($this->never())
            ->method('setCurrent');
        $this->parser->process(new Event($this, 'core.request', array('request_type' => HttpKernelInterface::MASTER_REQUEST, 'request' => $request)));
    }
    public function testDoesntProcessRequestIfCannotFindRoute() {
        $this->sitemap->expects($this->never())
            ->method('add');
        $this->sitemap->expects($this->never())
            ->method('setCurrent');
        $this->router->expects($this->once())
            ->method('getRouteCollection')
            ->will($this->returnValue($this->getRouteCollectionMock()));
        $request = new Request(array(), array(), array('_route' => 'test_route'), array(), array(), array());
        $this->parser->process(new Event($this, 'core.request', array('request_type' => HttpKernelInterface::MASTER_REQUEST, 'request' => $request)));
    }
    public function testDoesntProcessRouteWithNoSitemapOption() {
        $this->sitemap->expects($this->never())
            ->method('add');
        $this->container->expects($this->never())
            ->method('setService');
        $this->router->expects($this->once())
            ->method('getRouteCollection')
            ->will($this->returnValue($this->getRouteCollectionMock(new Route('/', array(), array(), array()))));
        $request = new Request(array(), array(), array('_route' => 'test_route'), array(), array(), array());
        $this->parser->process(new Event($this, 'core.request', array('request_type' => HttpKernelInterface::MASTER_REQUEST, 'request' => $request)));
    }
    public function testAddsToSitemapBasedOnRouteOptions() {
        $this->router->expects($this->once())
            ->method('getRouteCollection')
            ->will($this->returnValue($this->getRouteCollectionMock(new Route('/', array(), array(), array('sitemap' => array(
                'changefreq' => Url::DAILY,
                'priority'   => 1.0,
                'lastmod'    => date('Y-m-d'),
                'autoupdate' => true,
            ))))));
        $url = new Url('http://www.google.com/', array(
            'changefreq' => Url::DAILY,
            'priority'   => 1.0,
            'lastmod'    => date('Y-m-d'),
        ));
        $this->sitemap->expects($this->once())
            ->method('add')
            ->with('http://', array(
                'changefreq' => Url::DAILY,
                'priority'   => 1.0,
                'lastmod'    => date('Y-m-d'),
            ));
        $this->sitemap->expects($this->once())
            ->method('get')
            ->with('http://')
            ->will($this->returnValue($url));
        $this->container->expects($this->once())
            ->method('setService')
            ->with('sitemap.current_url', $url);
        $request = new Request(array(), array(), array('_route' => 'test_route'), array(), array(), array());
        $this->parser->process(new Event($this, 'core.request', array('request_type' => HttpKernelInterface::MASTER_REQUEST, 'request' => $request)));
    }
    public function getEventDispatcherMock() {
        return $this->getMock('Symfony\Components\EventDispatcher\EventDispatcher', array('connect'), array(), '', false);
    }
    public function getRouteCollectionMock($route = null) {
        $routeCollection = $this->getMock('Symfony\Components\Routing\RouteCollection', array('getRoute'), array(), '', false);
        $routeCollection->expects($this->once())
            ->method('getRoute')
            ->with('test_route')
            ->will($this->returnValue($route));
        return $routeCollection;
    }
}