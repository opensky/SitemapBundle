<?php

namespace OpenSky\Bundle\SitemapBundle\Sitemap\Storage;

use OpenSky\Bundle\SitemapBundle\Sitemap\Url;

/**
 * Storage
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 */
interface Storage
{
    const PAGE_LIMIT = 50000;

    /**
     * @return int
     */
    public function getTotalPages();

    /**
     * @param string loc
     * @return OpenSky\Bundle\SitemapBundle\Sitemap\Url|null
     */
    public function findOne($loc);

    /**
     * @param int page
     * @return \Traversable<OpenSky\Bundle\SitemapBundle\Sitemap\Url>
     */
    public function find($page);

    /**
     * @param OpenSky\Bundle\SitemapBundle\Sitemap\Url
     */
    public function save(Url $url);
}