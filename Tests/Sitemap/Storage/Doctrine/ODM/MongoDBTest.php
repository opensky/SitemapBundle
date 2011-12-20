<?php

namespace Bundle\SitemapBundle\Tests\Sitemap\Storage\Doctrine\ODM;

use Bundle\SitemapBundle\Sitemap\Storage\Doctrine\ODM\MongoDB;
use Bundle\SitemapBundle\Sitemap\Url;

/**
 * MongoDBTest
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class MongoDBTest extends \PHPUnit_Framework_TestCase
{
    protected $storage;
    protected $dm;
    protected $repository;

    public function setUp()
    {
        $this->dm         = $this->getDocumentManagerMock();
        $this->repository = $this->getDocumentRepositoryMock();
        $this->storage    = new MongoDB($this->dm);

        $this->storage->setRepository($this->repository);
    }

    public function tearDown()
    {
        unset($this->storage, $this->repository, $this->dm);
    }

    public function testRegister()
    {
        $classMetadata        = $this->getClassMetadataMock();
        $classMetadataFactory = $this->getClassMetadataFactoryMock();

        $classMetadataFactory->expects($this->once())
            ->method('setMetadataFor')
            ->with(MongoDB::URL_CLASS, $classMetadata);

        $this->storage->register($classMetadata, $classMetadataFactory);
    }

    /**
     * @dataProvider getUrls
     */
    public function testFindOne($location, $result)
    {
        $this->repository->expects($this->once())
            ->method('find')
            ->with($location)
            ->will($this->returnValue($result));

        $this->assertSame($result, $this->storage->findOne($location));
    }

    public function testFind()
    {
        $expectedNumberOfResults = 5;
        $page                    = 1;
        $cursor                  = $this->getMongoCursorMock();

        $cursor->expects($this->once())
            ->method('skip')
            ->with(($page - 1) * MongoDB::PAGE_LIMIT)
            ->will($this->returnValue($cursor));
        $cursor->expects($this->once())
            ->method('limit')
            ->with(MongoDB::PAGE_LIMIT)
            ->will($this->returnValue($cursor));
        $cursor->expects($this->once())
            ->method('count')
            ->will($this->returnValue($expectedNumberOfResults));

        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($cursor));

        $this->assertEquals($expectedNumberOfResults, count($this->storage->find($page)));
    }

    public function testSave()
    {
        $url = new Url('http://www.example.com');

        $this->dm->expects($this->once())
            ->method('persist')
            ->with($url);

        $this->storage->save($url);
    }

    public function testGetTotalPages()
    {
        $cursor = $this->getMongoCursorMock();

        $cursor->expects($this->once())
            ->method('count')
            ->will($this->returnValue(MongoDB::PAGE_LIMIT + 1));

        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($cursor));

        $this->assertEquals(2, $this->storage->getTotalPages());
    }

    public function testGetDocumentManager()
    {
        $this->assertSame($this->dm, $this->storage->getDocumentManager());
    }

    public function testGetDocumentRepository()
    {
        $this->assertSame($this->repository, $this->storage->getDocumentRepository());
    }

    public function getUrls()
    {
        return array(
            array('http://example.com/', null),
            array('http://example.com/', new Url('http://example.com/')),
        );
    }

    /**
     * @return Doctrine\ODM\MongoDB\Mapping\ClassMetadata
     */
    protected function getClassMetadataMock()
    {
        return $this->getMockBuilder('Doctrine\ODM\MongoDB\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
    }

    /**
     * @return Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory
     */
    protected function getClassMetadataFactoryMock()
    {
        return $this->getMockBuilder('Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
    }

    /**
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManagerMock()
    {
        return $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
    }

    /**
     * @return Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected function getDocumentRepositoryMock()
    {
        return $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentRepository')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
    }

    /**
     * @return Doctrine\ODM\MongoDB\Cursor
     */
    protected function getMongoCursorMock()
    {
        return $this->getMockBuilder('Doctrine\ODM\MongoDB\Cursor')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();
    }
}