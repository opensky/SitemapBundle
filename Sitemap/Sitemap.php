<?php

namespace Bundle\SitemapBundle\Sitemap;

use Bundle\SitemapBundle\Sitemap\Storage\Storage;

/**
 * Sitemap
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class Sitemap
{
    const DEFAULT_URL_CLASS = 'Bundle\SitemapBundle\Sitemap\Url';

    /**
     * @var string
     */
    protected $urlClass = self::DEFAULT_URL_CLASS;
    /**
     * @var array
     */
    protected $defaults = array();
    /**
     * @var Bundle\SitemapBundle\Sitemap\Storage\Storage
     */
    protected $storage;

    /**
     * @param Bundle\SitemapBundle\Sitemap\Storage\Storage $storage
     * @param array $defaults
     */
    public function __construct(Storage $storage, array $defaults = array())
    {
        $this->storage = $storage;
        foreach (array('changefreq', 'priority', 'lastmod') as $prop) {
            if (isset($defaults[$prop])) {
                $this->defaults[$prop] = $defaults[$prop];
            }
        }
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->storage->getTotalPages();
    }

    /**
     * @param int $page
     * @return \Traversable<Bundle\SitemapBundle\Sitemap\Url>
     */
    public function getUrls($page = 1)
    {
        return $this->storage->find($page);
    }

    /**
     * @param string $loc
     * @param array $info
     * @return Bundle\SitemapBundle\Sitemap\Url
     */
    public function add($loc, array $info)
    {
        $info = array_merge($this->defaults, $info);
        $urlClass = $this->getUrlClass();
        $url = new $urlClass($loc);
        foreach (array('changefreq', 'priority', 'lastmod') as $prop) {
            if (isset($info[$prop])) {
                $url->{'set' . ucfirst($prop)}($info[$prop]);
            }
        }
        $this->storage->save($url);
        return $url;
    }

    /**
     * @param string $loc
     * @return Bundle\SitemapBundle\Sitemap\Url|null
     */
    public function get($loc)
    {
        return $this->storage->findOne($loc);
    }

    /**
     * @param string $loc
     * @return boolean
     */
    public function has($loc)
    {
        return ($this->get($loc) !== null);
    }

    /**
     * @param string $class
     */
    public function setUrlClass($class)
    {
        if ($class !== self::DEFAULT_URL_CLASS && (!class_exists($class) || !is_subclass_of($class, self::DEFAULT_URL_CLASS))) {
            throw new \InvalidArgumentException('Class ' . $class . ' doesn\'t exist or is not instance of ' . self::DEFAULT_URL_CLASS);
        }
        $this->urlClass = $class;
    }

    /**
     * @return string
     */
    public function getUrlClass()
    {
        return $this->urlClass;
    }

    /**
     * @return Bundle\SitemapBundle\Sitemap\Storage\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

}