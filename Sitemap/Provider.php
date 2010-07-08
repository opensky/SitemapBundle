<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Sitemap;

/**
 * This is the inteface for sitemap provider classes
 * During the execution of console command that regenerates the sitemap,
 * all services annotated as 'sitemap.provider', are being called on
 * 'populate()' with the Sitemap instance as the only argument.
 * Providers are free to do anything they want with the sitemap instance
 * during the 'populate()' call. The usual strategy is to call $sitemap->add()
 * for each url the provider needs to include.
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
interface Provider {
    public function populate(Sitemap $sitemap);
}