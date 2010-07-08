<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Listener;

use Symfony\Components\Routing\Router;
use Symfony\Components\EventDispatcher\EventDispatcher;
use Symfony\Components\EventDispatcher\Event;
use Symfony\Components\HttpKernel\HttpKernelInterface;
use Symfony\Components\HttpKernel\Request;
use Bundle\SitemapBundle\Sitemap\Sitemap;
use Symfony\Components\DependencyInjection\ContainerInterface;

/**
 * The SitemapBundle supports url entry auto updates (not optimized for performance).
 *
 * To enable it, you would need to provide specific options to your route definitions:
 *
 * some_route:
 *   pattern: /my/cool/url
 *   ...
 *   options:
 *     sitemap: - { autoupdate: true, lastmod: 2005-06-06 }
 *
 * The key option is sitemap[autoupdate], after that you could specify defaults:
 *
 * sitemap[lastmod]
 * sitemap[changefreq]
 * sitemap[priority]
 *
 * When request enters the filtering dispatch loop, the sitemap is re-dumped and
 * current url is update with response's 'Last-Modified' header content.
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class RequestParser {

    protected $router;
    protected $sitemap;
    protected $container;

    public function __construct(Router $router, Sitemap $sitemap, ContainerInterface $container) {
        $this->router = $router;
        $this->sitemap = $sitemap;
        $this->container = $container;
    }

    public function register(EventDispatcher $dispatcher) {
        $dispatcher->connect('core.request', array($this, 'process'));
    }

    public function process(Event $event) {
        if ($event->getParameter('request_type') !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }
        $request = $event->getParameter('request');
        if (null === ($route = $request->path->get('_route'))) {
            return;
        }
        $route = $this->findRoute($route);
        if (null === $route) {
            return;
        }
        $options = $route->getOptions();
        if ( ! isset($options['sitemap']) || ! isset($options['sitemap']['autoupdate']) || ! $options['sitemap']['autoupdate']) {
            return;
        }
        $info = array();
        foreach(array('lastmod', 'changefreq', 'priority') as $prop) {
            if (isset($options['sitemap'][$prop])) {
                $info[$prop] = $options['sitemap'][$prop];
            }
        }
        $this->addCurrentRoute($this->getUri($request), $info);
    }

    public function findRoute($route) {
        return $this->router->getRouteCollection()->getRoute($route);
    }

    public function addCurrentRoute($url, $options) {
        if ( ! $this->sitemap->has($url)) {
            $this->sitemap->add($url, (array) $options);
        }
        $this->container->setService('sitemap.current_url', $this->sitemap->get($url));
    }

    public function getUri(Request $request) {
        return $request->getScheme().'://'.$request->getHost().$request->getScriptName().$request->getPathInfo();
    }

}