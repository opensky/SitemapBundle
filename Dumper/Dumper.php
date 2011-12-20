<?php

namespace OpenSky\Bundle\SitemapBundle\Dumper;

use OpenSky\Bundle\SitemapBundle\Sitemap\Sitemap;

/**
 * Dumper
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 */
interface Dumper
{
    public function dump(Sitemap $sitemap);
}