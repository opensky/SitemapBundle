<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Sitemap;

/**
 * The sitemap service
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright The OpenSky Project Inc. 2010
 * @link http://www.theopenskyproject.com/
 */
class Sitemap {

    const ENTRIES_LIMIT = 50000;

    protected $urls = array();
    protected $urlClass = 'Bundle\SitemapBundle\Sitemap\Url';
    protected $defaults = array();

    public function __construct(array $defaults = array()) {
        foreach (array('changefreq', 'priority', 'lastmod') as $prop) {
            if (isset($defaults[$prop])) {
                $this->defaults[$prop] = $defaults[$prop];
            }
        }
    }

    public function getUrls() {
        return $this->urls;
    }

    public function add($loc, array $info) {
        $info = array_merge($this->defaults, $info);
        $urlClass = $this->getUrlClass();
        $url = new $urlClass($loc);
        foreach (array('changefreq', 'priority', 'lastmod') as $prop) {
            if (isset ($info[$prop])) {
                $url->{'set' . ucfirst($prop)}($info[$prop]);
            }
        }
        $this->urls[$loc] = $url;
        ksort($this->urls);
        return $url;
    }

    public function get($loc) {
        return $this->has($loc) ? $this->urls[$loc] : null;
    }

    public function has($loc) {
        return isset($this->urls[$loc]);
    }

    public function setUrlClass($class) {
        if ($class !== 'Bundle\SitemapBundle\Sitemap\Url' && ( ! class_exists($class) || ! is_subclass_of($class, 'Bundle\SitemapBundle\Sitemap\Url'))) {
            throw new \InvalidArgumentException('Class ' . $class . ' doesn\'t exist or is not instance of Bundle\SitemapBundle\Sitemap\Url');
        }
        $this->urlClass = $class;
    }

    public function getUrlClass() {
        return $this->urlClass;
    }

}