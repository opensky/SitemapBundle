<?php

namespace Bundle\SitemapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Bundle\SitemapBundle\Sitemap\Sitemap;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\ParameterBag;

/**
 * SitemapController
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
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
            'router' => $this->getRouter(),
        ));
    }

    /**
     * @return Symfony\Component\HttpKernel\Response
     */
    public function sitemapAction()
    {

        $page = $this->getPage($this['request']->query);
        return $this->generateResponse('sitemap', array(
            'urls' => $this->getUrls($page),
            'page' => $page,
        ));
    }

    protected function generateResponse($template, array $args)
    {
        $template = $this->getTemplateLoader()->load($template, array(
                'bundle' => 'SitemapBundle',
                'controller' => 'Sitemap',
                'format' => '.xml',
            ));

        extract($args);

        ob_start();
        require $template;

        return $this->createResponse(ob_get_clean(), 200, array(
            'Content-Type' => 'application/xml'
        ));
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
     * @return Traversable<Bundle\SitemapBundle\Sitemap\Url>
     */
    protected function getUrls($page)
    {
        return $this->getSitemap()->getUrls($page);
    }

    /**
     * @return Symfony\Component\Templating\Loader\LoaderInterface
     */
    protected function getTemplateLoader()
    {
        return $this->getTemplating()->getLoader();
    }

    /**
     * @return Symfony\Component\Templating\Engine
     */
    protected function getTemplating()
    {
        return $this['templating'];
    }

    /**
     *
     * @return Bundle\SitemapBundle\Sitemap\Sitemap
     */
    protected function getSitemap()
    {
        return $this['sitemap'];
    }

    /**
     * @return Symfony\Component\Routing\Router
     */
    protected function getRouter()
    {
        return $this['router'];
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
        return $this['response'];
    }

}