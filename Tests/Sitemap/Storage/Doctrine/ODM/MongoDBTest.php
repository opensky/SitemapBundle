<?php

namespace Bundle\SitemapBundle\Tests\Sitemap\Storage\Doctrine\ODM;

use Bundle\SitemapBundle\Sitemap\Storage\Doctrine\ODM\MongoDB;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadataFactory;
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

    public function setUp()
    {
        $this->dm = $this->getDocumentManagerMock();
        $metadata = $this->getClassMetadata();
        $this->storage = new MongoDB($this->dm, $metadata);
    }

    public function tearDown()
    {
        unset($this->storage, $this->dm);
    }

    /**
     * @dataProvider getUrls
     */
    public function testFindOne($location, $result)
    {

        $this->dm->expects($this->once())
            ->method('find')
            ->with(MongoDB::URL_CLASS, $location)
            ->will($this->returnValue($result));

        $this->assertSame($result, $this->storage->findOne($location));
    }

    public function testFind()
    {
        $expectedNumberOfResults = 5;
        $page = 1;

        $cursor = $this->getMongoCursorMock();
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

        $this->dm->expects($this->once())
            ->method('find')
            ->with(MongoDB::URL_CLASS)
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

    public function getUrls()
    {
        return array(
            array('http://example.com/', null),
            array('http://example.com/', new Url('http://example.com/')),
        );
    }

    public function testGetTotalPages()
    {
        $cursor = $this->getMongoCursorMock();
        $cursor->expects($this->once())
            ->method('count')
            ->will($this->returnValue(MongoDB::PAGE_LIMIT + 1));

        $this->dm->expects($this->once())
            ->method('find')
            ->with(MongoDB::URL_CLASS)
            ->will($this->returnValue($cursor));

        $this->assertEquals(2, $this->storage->getTotalPages());
    }

    /**
     * @return Doctrine\ODM\MongoDB\Cursor
     */
    protected function getMongoCursorMock()
    {
        return $this->getMock('Doctrine\ODM\MongoDB\Cursor', array('count', 'current', 'next', 'key', 'value', 'rewind', 'skip', 'limit'), array(), '', false, false);
    }

    /**
     * @return Doctrine\ODM\MongoDB\DocumentManager
     */
    protected function getDocumentManagerMock()
    {
        $dm = $this->getMock('Doctrine\ODM\MongoDB\DocumentManager', array('find', 'findOne', 'persist', 'setMetadataFor', 'getMetadataFactory', 'skip', 'limit'), array(), '', false, false);
        $metadataFactory = new ClassMetadataFactory($dm);
        $dm->expects($this->any())
            ->method('getMetadataFactory')
            ->will($this->returnValue($metadataFactory));
        return $dm;
    }

    /**
     * @return Doctrine\ODM\MongoDB\Mapping\ClassMetadata
     */
    protected function getClassMetadata()
    {
        $metadata = new ClassMetadata(MongoDB::URL_CLASS);
        $metadata->setAllowCustomId(true);
        $metadata->setDB('sitemap');
        $metadata->setCollection('urls');
        $metadata->mapField(array(
            'fieldName' => 'loc',
            'type' => 'custom_id',
            'id' => true,
        ));
        $metadata->mapField(array(
            'fieldName' => 'lastmod',
            'type' => 'date',
        ));
        $metadata->mapField(array(
            'fieldName' => 'changefreq',
            'type' => 'string',
        ));
        $metadata->mapField(array(
            'fieldName' => 'priority',
            'type' => 'float',
        ));
        return $metadata;
    }

}