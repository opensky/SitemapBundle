<?php

namespace OpenSky\Bundle\SitemapBundle\Dumper\Doctrine\ODM;

use OpenSky\Bundle\SitemapBundle\Dumper\Dumper;
use OpenSky\Bundle\SitemapBundle\Sitemap\Sitemap;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * MongoDB
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 */
class MongoDB implements Dumper
{
    /**
     * ${@inheritDoc}
     */
    public function dump(Sitemap $sitemap)
    {
        // now flush the new ones
        $dm = $this->getDocumentManager($sitemap);
        $dm->flush(null, array('safe' => true));
    }

    /**
     * @param Sitemap $sitemap
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager(Sitemap $sitemap)
    {
        return $sitemap->getStorage()->getDocumentManager();
    }

    /**
     * @param Sitemap $sitemap
     * @return Doctrine\MongoDB\Collection
     */
    protected function getDocumentCollection(Sitemap $sitemap)
    {
        return $this->getDocumentManager($sitemap)->getDocumentCollection($sitemap->getUrlClass());
    }

}
