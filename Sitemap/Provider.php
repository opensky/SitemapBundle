<?php

namespace OpenSky\Bundle\SitemapBundle\Sitemap;

/**
 * Provider
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 */
interface Provider
{
    public function populate(Sitemap $sitemap);
}