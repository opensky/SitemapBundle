<?php

namespace Bundle\SitemapBundle\Sitemap\Storage\Doctrine\ODM;

use Bundle\SitemapBundle\Sitemap\Url;
use Bundle\SitemapBundle\Sitemap\Storage\Storage;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;

/**
 * MongoDB
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class MongoDB implements Storage
{
    const URL_CLASS = 'Bundle\SitemapBundle\Sitemap\Url';

    /**
     * @var Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @param Doctrine\ODM\MongoDB\DocumentManager $dm
     */
    public function __construct(DocumentManager $dm, ClassMetadata $metadata)
    {
        $this->dm = $dm;
        $this->dm->getMetadataFactory()->setMetadataFor(self::URL_CLASS, $metadata);
    }

    /**
     * Returns total number of pages in the sitemap
     *
     * @return int
     */
    public function getTotalPages()
    {
        return ceil($this->dm->find(self::URL_CLASS)->count() / self::PAGE_LIMIT);
    }

    /**
     * Finds one Url by location, returns null if not found
     *
     * @param string $loc
     * @return Bundle\SitemapBundle\Sitemap\Url|null
     */
    public function findOne($loc)
    {
        return $this->dm->find(self::URL_CLASS, $loc);
    }

    /**
     * Finds all urls on specific page
     *
     * @param int $page
     * @return \Traversable<Bundle\SitemapBundle\Sitemap\Url>
     */
    public function find($page)
    {
        return $this->dm->find(self::URL_CLASS)->skip($this->getSkip($page))->limit(self::PAGE_LIMIT);
    }

    /**
     * Persists Url in DocumentManager, does not call flush
     *
     * @param Url $url
     */
    public function save(Url $url)
    {
        $this->dm->persist($url);
    }

    /**
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager()
    {
        return $this->dm;
    }

    private function getSkip($page)
    {
        return ((int) $page - 1) * self::PAGE_LIMIT;
    }

}