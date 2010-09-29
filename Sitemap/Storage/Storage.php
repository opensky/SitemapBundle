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
interface Storage
{
    const PAGE_LIMIT = 50000;

    /**
     * @return int
     */
    public function getTotalPages();

    /**
     * @param string loc
     * @return Bundle\SitemapBundle\Sitemap\Url|null
     */
    public function findOne($loc);

    /**
     * @param int page
     * @return \Traversable<Bundle\SitemapBundle\Sitemap\Url>
     */
    public function find($page);

    /**
     * @param Bundle\SitemapBundle\Sitemap\Url
     */
    public function save(Url $url);
}