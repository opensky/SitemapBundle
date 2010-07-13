<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Controller;

use Symfony\Framework\WebBundle\Controller;
use Bundle\SitemapBundle\Sitemap\Sitemap;
use Symfony\Components\HttpKernel\Exception\NotFoundHttpException;

/**
 * This is the controller that will be used for serving sitemaps
 *
 * It serves sitemaps in the chunks of 50,000 entries per page
 * to get to the next page, if there is one, just use url like
 *
 * sitemap.xml?page=<page_num>
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright The OpenSky Project Inc. 2010
 * @link http://www.theopenskyproject.com/
 */
class SitemapController extends Controller {
    public function sitemapAction() {
        $template = $this->getTemplateLoader()->load('sitemap', array(
            'bundle'     => 'SitemapBundle',
            'controller' => 'Sitemap',
            'format'     => '.xml',
        ));

        $urls = array_slice($this->getUrls(), $this->getPage() - 1, Sitemap::ENTRIES_LIMIT);

        if ($this->getPage() > 1 && 0 === count($urls)) {
            throw new NotFoundHttpException('Sitemap not found');
        }

        ob_start();
        require $template;
        $content = ob_get_clean();

        $response = $this->getResponse();
        $response->setContent($content);

        return $response;
    }
    public function getUrls() {
        return $this->container->getService('sitemap')->getUrls();
    }
    public function getTemplateLoader() {
        return $this->container->getTemplatingService()->getLoader();
    }
    public function getPage() {
        return (int) max(array(1, $this->request->query->get('page', 1)));
    }
    public function getResponse() {
        $response = $this->container->getResponseService();
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
    }
}