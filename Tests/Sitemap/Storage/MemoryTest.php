<?php

namespace Bundle\SitemapBundle\Tests\Sitemap\Storage;

use Bundle\SitemapBundle\Sitemap\Storage\Memory;
use Bundle\SitemapBundle\Sitemap\Url;

/**
 * MemoryTest
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class MemoryTest extends \PHPUnit_Framework_TestCase
{
    protected $storage;

    public function setUp()
    {
        $this->storage = new Memory();
    }

    public function tearDown()
    {
        unset($this->storage);
    }

    public function testFindReturnsEmptyResult()
    {
        $this->assertEquals(0, count($this->storage->find(1)));
    }

    public function testFindOneReturnsNullForNonExistentResult()
    {
        $this->assertNull($this->storage->findOne('http://google.com'));
    }

    public function testHasUrlAfterAdding()
    {
        $location = 'http://www.twitter.com';
        $url = $this->getCreateUrl($location);
        $this->storage->save($url);
        $this->assertSame($url, $this->storage->findOne($location));
        $this->assertEquals(1, count($this->storage->find(1)));
    }

    public function testGetTotalPages()
    {
        $this->storage->urls = array_pad(array(), Memory::PAGE_LIMIT + 1, 'http://www.google.com');
        $this->assertEquals(2, $this->storage->getTotalPages());
    }

    protected function getCreateUrl($location)
    {
        return new Url($location);
    }

}