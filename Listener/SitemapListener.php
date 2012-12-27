<?php

namespace OpenSky\Bundle\SitemapBundle\Listener;

use OpenSky\Bundle\SitemapBundle\Dumper\Dumper;
use OpenSky\Bundle\SitemapBundle\Exception;
use OpenSky\Bundle\SitemapBundle\Sitemap\Sitemap;
use OpenSky\Bundle\SitemapBundle\Sitemap\Url;
use Symfony\Component\EventDispatcher\Event;

/**
 * SitemapListener
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 */
class SitemapListener
{
    const DEFAULT_PRIORITY = '0.2';

    /**
     * @var OpenSky\Bundle\SitemapBundle\Sitemap\Sitemap
     */
    protected $sitemap;
    /**
     * @var OpenSky\Bundle\SitemapBundle\Dumper\Dumper
     */
    protected $dumper;

    /**
     * @param OpenSky\Bundle\SitemapBundle\Sitemap\Sitemap $sitemap
     */
    public function __construct(Sitemap $sitemap, Dumper $dumper)
    {
        $this->sitemap = $sitemap;
        $this->dumper = $dumper;
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

        if ($event->has('imageloc')) {
            $url->setImageloc($event->get('imageloc'));
            if ($event->has('imagetitle')) {
                $url->setImagetitle($event->get('imagetitle'));
            }
        }

        $this->dump($this->sitemap);
    }

    public function create(Event $event)
    {
        $values = array(
            'changefreq' => ($event->has('changefreq') ? $event->get('changefreq') : Url::YEARLY),
            'priority' => ($event->has('priority') ? $event->get('priority') : self::DEFAULT_PRIORITY),
            'lastmod' => new \DateTime(),
            'imageloc' => ($event->has('imageloc') ? $event->get('imageloc') : null),
            'imagetitle' => ($event->has('imagetitle') ? $event->get('imagetitle') : null),
        );

        $this->sitemap->add($event->get('loc'), $values);

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