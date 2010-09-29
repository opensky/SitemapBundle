<?php

namespace Bundle\SitemapBundle\Sitemap;

/**
 * Provider
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
interface Provider
{
    public function populate(Sitemap $sitemap);
}