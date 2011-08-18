<?php

namespace OpenSky\Bundle\SitemapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use OpenSky\Bundle\SitemapBundle\Sitemap\Sitemap;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\ParameterBag;
use Symfony\Component\HttpFoundation\Response;

/**
 * SitemapController
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 */
class SitemapController extends Controller
{

    /**
     * @return Symfony\Component\HttpKernel\Response
     */
    public function siteindexAction()
    {
        return $this->generateResponse('siteindex', array(
            'totalPages' => $this->getTotalPages(),
            'router'     => $this->getRouter(),
        ));
    }

    /**
     * @return Symfony\Component\HttpKernel\Response
     */
    public function sitemapAction()
    {
        $page = $this->getPage($this->get('request')->query);
        return $this->generateResponse('sitemap', array(
            'urls' => $this->getUrls($page),
            'page' => $page,
        ));
    }

    protected function generateResponse($view, array $args)
    {
        $template = sprintf(
            'OpenSkySitemapBundle:Sitemap:%s.xml.%s',
            $view,
            $this->container->getParameter('opensky.sitemap.template.engine')
        );

        return $this->getTemplating()->renderResponse($template, $args, new Response('', 200, array(
            'Content-Type' => 'application/xml',
        )));
    }

    /**
     * @return int
     */
    protected function getTotalPages()
    {
        return $this->getSitemap()->getTotalPages();
    }

    /**
     * @param int $page
     * @return Traversable<OpenSky\Bundle\SitemapBundle\Sitemap\Url>
     */
    protected function getUrls($page)
    {
        return $this->getSitemap()->getUrls($page);
    }

    /**
     * @return Symfony\Component\Templating\Engine
     */
    protected function getTemplating()
    {
        return $this->get('templating');
    }

    /**
     *
     * @return OpenSky\Bundle\SitemapBundle\Sitemap\Sitemap
     */
    protected function getSitemap()
    {
        return $this->get('opensky.sitemap');
    }

    /**
     * @return Symfony\Component\Routing\Router
     */
    protected function getRouter()
    {
        return $this->get('router');
    }

    /**
     * @return int
     */
    protected function getPage($query)
    {
        return (int) max(array(1, $query->get('page', 1)));
    }

    /**
     * @return Symfony\Component\HttpKernel\Response
     */
    protected function getResponse()
    {
        return $this->get('response');
    }

}
