<?php

namespace Bundle\SitemapBundle\Dumper;

use Bundle\SitemapBundle\Sitemap\Sitemap;

/**
 * Dumper
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
interface Dumper
{
    public function dump(Sitemap $sitemap);
}