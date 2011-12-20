<?php

namespace OpenSky\Bundle\SitemapBundle\Sitemap\Storage;

use OpenSky\Bundle\SitemapBundle\Sitemap\Url;

/**
 * Storage
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 */
class Memory implements Storage
{
    public $urls = array();

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return ceil(count($this->urls) / self::PAGE_LIMIT);
    }

    /**
     * @param string loc
     * @return OpenSky\Bundle\SitemapBundle\Sitemap\Url|null
     */
    public function findOne($loc)
    {
        return isset($this->urls[$loc]) ? $this->urls[$loc] : null;
    }

    /**
     * @param int page
     * @return \Traversable<OpenSky\Bundle\SitemapBundle\Sitemap\Url>
     */
    public function find($page)
    {
        return array_slice($this->urls, ($page - 1) * self::PAGE_LIMIT, self::PAGE_LIMIT);
    }

    /**
     * @param OpenSky\Bundle\SitemapBundle\Sitemap\Url
     */
    public function save(Url $url)
    {
        $this->urls[$url->getLoc()] = $url;
    }

}