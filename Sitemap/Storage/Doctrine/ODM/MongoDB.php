<?php

namespace Bundle\SitemapBundle\Sitemap\Storage\Doctrine\ODM;

use Bundle\SitemapBundle\Sitemap\Url;
use Bundle\SitemapBundle\Sitemap\Storage\Storage;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;

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
     * @var Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param Doctrine\ODM\MongoDB\DocumentManager    $dm
     * @param Doctrine\ODM\MongoDB\DocumentRepository $r
     */
    public function __construct(DocumentManager $dm, DocumentRepository $r)
    {
        $this->dm         = $dm;
        $this->repository = $r;
    }

    /**
     * Registers ClassMetadata with a ClassMetadataFactory.
     *
     * @param Doctrine\ODM\MongoDB\Mapping\ClassMetadata        $cm;
     * @param Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory $cmf;
     */
    public function register(ClassMetadata $cm, ClassMetadataFactory $cmf)
    {
        $cmf->setMetadataFor(self::URL_CLASS, $cm);
    }

    /**
     * Finds one Url by location, returns null if not found
     *
     * @param string $loc
     * @return Bundle\SitemapBundle\Sitemap\Url|null
     */
    public function findOne($loc)
    {
        return $this->repository->find($loc);
    }

    /**
     * Finds all urls on specific page
     *
     * @param int $page
     * @return \Traversable<Bundle\SitemapBundle\Sitemap\Url>
     */
    public function find($page)
    {
        return $this->repository->findAll()
            ->skip($this->getSkip($page))
            ->limit(self::PAGE_LIMIT);
    }

    /**
     * Returns total number of pages in the sitemap
     *
     * @return int
     */
    public function getTotalPages()
    {
        return ceil($this->repository->findAll()->count() / self::PAGE_LIMIT);
    }

    /**
     * Persists Url in DocumentManager, does not call flush
     *
     * @param \Bundle\SitemapBundle\Sitemap\Url $url
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

    /**
     * @return Doctrine\ODM\MongoDB\DocumentRepository
     */
    public function getDocumentRepository()
    {
        return $this->repository;
    }

    private function getSkip($page)
    {
        return ((int) $page - 1) * self::PAGE_LIMIT;
    }

}