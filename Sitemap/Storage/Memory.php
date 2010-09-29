<?php

namespace Bundle\SitemapBundle\Sitemap\Storage;

use Bundle\SitemapBundle\Sitemap\Url;

/**
 * Storage
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
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
     * @return Bundle\SitemapBundle\Sitemap\Url|null
     */
    public function findOne($loc)
    {
        return isset($this->urls[$loc]) ? $this->urls[$loc] : null;
    }

    /**
     * @param int page
     * @return \Traversable<Bundle\SitemapBundle\Sitemap\Url>
     */
    public function find($page)
    {
        return array_slice($this->urls, ($page - 1) * self::PAGE_LIMIT, self::PAGE_LIMIT);
    }

    /**
     * @param Bundle\SitemapBundle\Sitemap\Url
     */
    public function save(Url $url)
    {
        $this->urls[$url->getLoc()] = $url;
    }

}