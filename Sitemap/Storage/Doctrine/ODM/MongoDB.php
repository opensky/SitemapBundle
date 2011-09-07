<?php

namespace OpenSky\Bundle\SitemapBundle\Sitemap\Storage\Doctrine\ODM;

use OpenSky\Bundle\SitemapBundle\Sitemap\Url;
use OpenSky\Bundle\SitemapBundle\Sitemap\Storage\Storage;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;

/**
 * MongoDB
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 */
class MongoDB implements Storage
{
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
     * @param Doctrine\ODM\MongoDB\DocumentManager $dm
     */
    public function __construct(DocumentManager $dm, DocumentRepository $repository)
    {
        $this->dm = $dm;
        $this->repository = $repository;
    }

    /**
     * Finds one Url by location, returns null if not found
     *
     * @param string $loc
     * @return OpenSky\Bundle\SitemapBundle\Sitemap\Url|null
     */
    public function findOne($loc)
    {
        return $this->repository->find($loc);
    }

    /**
     * Finds all urls on specific page
     *
     * @param int $page
     * @return \Traversable<OpenSky\Bundle\SitemapBundle\Sitemap\Url>
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
     * @param OpenSky\Bundle\SitemapBundle\Sitemap\Url $url
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