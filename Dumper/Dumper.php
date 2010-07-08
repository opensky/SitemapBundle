<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Dumper;

use Bundle\SitemapBundle\Sitemap\Sitemap;

/**
 * Sitemap Dumper interface, PHP is the only one supported right now, but
 * we might add more.
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
interface Dumper {

    public function dump(Sitemap $sitemap);

}