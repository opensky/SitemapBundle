<?php

namespace Bundle\SitemapBundle\Dumper\Doctrine\ODM;

use Bundle\SitemapBundle\Dumper\Dumper;
use Bundle\SitemapBundle\Sitemap\Sitemap;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * MongoDB
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class MongoDB implements Dumper
{

    /**
     * ${@inheritDoc}
     * @todo - remove the Sitemap URLs from document manager
     */
    public function dump(Sitemap $sitemap)
    {
        $dm = $this->getDocumentManager($sitemap);
        $dm->flush(array('safe' => true));
    }

    /**
     *
     * @param Sitemap $sitemap
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManager(Sitemap $sitemap)
    {
        return $sitemap->getStorage()->getDocumentManager();
    }

}