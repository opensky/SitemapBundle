<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Listener;

use Symfony\Components\EventDispatcher\Event;
use Symfony\Components\EventDispatcher\EventDispatcher;
use Symfony\Components\HttpKernel\Response;
use Symfony\Components\HttpKernel\Request;
use Symfony\Components\HttpKernel\HttpKernelInterface;
use Bundle\SitemapBundle\Sitemap\Sitemap;
use Bundle\SitemapBundle\Dumper\Dumper;
use Bundle\SitemapBundle\Sitemap\Url;

/**
 * The listener that updates the current url lastmod attribute and re-dumps the sitemap
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class ResponseFilter {

    protected $dumper;
    protected $sitemap;
    protected $currentUrl;

    public function __construct(Sitemap $sitemap, Dumper $dumper, Url $currentUrl = null) {
        $this->currentUrl = $currentUrl;
        $this->sitemap = $sitemap;
        $this->dumper = $dumper;
    }

    public function register(EventDispatcher $dispatcher) {
        $dispatcher->connect('core.response', array($this, 'filter'));
    }

    public function filter(Event $event, Response $response) {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getParameter('request_type') || null === ($currentUrl = $this->getCurrentUrl())) {
            return $response;
        }
        if ($response->headers->has('Last-Modified')) {
            $headerTime = new \DateTime($response->headers->get('Last-Modified'));
            $lastmod = $currentUrl->getLastmod();
            $lastmod->setTimestamp(max(array($headerTime->getTimestamp(), $lastmod->getTimestamp())));
        } else {
            $response->headers->set('Last-Modified', $currentUrl->getLastmod()->format(DATE_RFC2822));
        }
        $this->dumper->dump($this->sitemap);
        return $response;
    }

    public function getCurrentUrl() {
        return $this->currentUrl;
    }

}