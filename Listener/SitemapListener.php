<?php

namespace Bundle\SitemapBundle\Listener;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Bundle\SitemapBundle\Sitemap\Sitemap;
use Bundle\SitemapBundle\Dumper\Dumper;
use Bundle\SitemapBundle\Sitemap\Url;
use Bundle\SitemapBundle\Exception;

/**
 * SitemapListener
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class SitemapListener
{
    const DEFAULT_PRIORITY = '0.2';

    /**
     * @var Bundle\SitemapBundle\Sitemap\Sitemap
     */
    protected $sitemap;
    /**
     * @var Bundle\SitemapBundle\Dumper\Dumper
     */
    protected $dumper;

    /**
     * @param Bundle\SitemapBundle\Sitemap\Sitemap $sitemap
     */
    public function __construct(Sitemap $sitemap, Dumper $dumper)
    {
        $this->sitemap = $sitemap;
        $this->dumper = $dumper;
    }

    public function register(EventDispatcher $dispatcher)
    {
        $dispatcher->connect('sitemap.update', array($this, 'update'));
        $dispatcher->connect('sitemap.create', array($this, 'create'));
    }

    public function update(Event $event)
    {
        $urlLoc = $event->get('loc');

        if (!$this->sitemap->has($urlLoc)) {
            throw new Exception\OutOfBoundsException('Url ' . $urlLoc . ' could not be found.');
        }

        $url = $this->sitemap->get($urlLoc);

        $time = new \DateTime();
        $lastmod = $url->getLastmod();

        $url->setLastmod($time);
        $url->setChangefreq($this->getChangefreq($time->diff(\DateTime::createFromFormat(Url::LASTMOD_FORMAT, $lastmod))));

        $this->dump($this->sitemap);
    }

    public function create(Event $event)
    {
        $this->sitemap->add($event->get('loc'), array(
            'changefreq' => ($event->has('changefreq') ? $event->get('changefreq') : Url::YEARLY),
            'priority' => ($event->has('priority') ? $event->get('priority') : self::DEFAULT_PRIORITY),
            'lastmod' => new \DateTime(),
        ));

        $this->dump($this->sitemap);
    }

    protected function getChangefreq(\DateInterval $interval)
    {
        return ($interval->y >= 1) ? Url::YEARLY : (($interval->m >= 1) ? Url::MONTHLY : (($interval->d >= 7) ? Url::WEEKLY : Url::DAILY));
    }

    protected function dump(Sitemap $sitemap)
    {
        $this->dumper->dump($sitemap);
    }

}