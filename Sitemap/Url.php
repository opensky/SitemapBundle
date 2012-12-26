<?php

namespace OpenSky\Bundle\SitemapBundle\Sitemap;

use OpenSky\Bundle\SitemapBundle\Exception;

/**
 * Url
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 */
class Url
{
    const PATTERN = '~^
      (http|https)://                         # protocol
      (
        ([a-z0-9-]+\.)+[a-z]{2,6}             # a domain name
          |                                   #  or
        \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}    # a IP address
      )
      (:[0-9]+)?                              # a port (optional)
      (/?|/\S+)                               # a /, nothing or a / with something
    $~ix';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';
    const YEARLY = 'yearly';
    const LASTMOD_FORMAT = 'Y-m-d';

    /**
     * @var string
     */
    protected $loc;
    /**
     *
     * @var string
     */
    protected $lastmod;
    /**
     *
     * @var string
     */
    protected $changefreq;
    /**
     * @var float
     */
    protected $priority;
    /**
     * @var string
     */
    protected $imageloc;
    /**
     * @var string
     */
    protected $imagetitle;

    /**
     * @param string $loc
     * @throws \InvalidArgumentException
     */
    public function __construct($loc)
    {
        if (!preg_match(self::PATTERN, $loc)) {
            throw new Exception\InvalidArgumentException($loc . ' is not valid url location');
        }
        $this->loc = $loc;
    }

    /**
     * @return string
     */
    public function getLoc()
    {
        return $this->loc;
    }

    /**
     * @param string|\DateTime $lastmod
     */
    public function setLastmod($lastmod)
    {
        if ($lastmod instanceof \DateTime) {
            $lastmod = $lastmod->getTimestamp();
        }
        $this->lastmod = date(self::LASTMOD_FORMAT, $lastmod);
    }

    /**
     * @return string
     */
    public function getLastmod()
    {
        if ($this->lastmod instanceof \DateTime) {
            $this->lastmod = $this->lastmod->format(self::LASTMOD_FORMAT);
        }
        return $this->lastmod;
    }

    /**
     * @param string $changefreq
     */
    public function setChangefreq($changefreq)
    {
        if (!in_array($changefreq, array(self::DAILY, self::MONTHLY, self::WEEKLY, self::YEARLY))) {
            throw new Exception\InvalidArgumentException('Change frequency ' . $changefreq . ' is invalid');
        }
        $this->changefreq = $changefreq;
    }

    /**
     * @return string
     */
    public function getChangefreq()
    {
        return $this->changefreq;
    }

    /**
     * @param float $priority
     */
    public function setPriority($priority)
    {
        if ($priority <= 0 || $priority > 1) {
            throw new Exception\InvalidArgumentException('Priority must be in between 0 and 1, ' . $priority . ' given');
        }
        $this->priority = $priority;
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return number_format($this->priority, 1);
    }

    /**
     * @return string
     */
    public function getImageloc()
    {
        return $this->imageloc;
    }

    /**
     * @param string $imageloc
     */
    public function setImageloc($imageloc)
    {
        $this->imageloc = $imageloc;
    }

    /**
     * @return string
     */
    public function getImagetitle()
    {
        return $this->imagetitle;
    }

    /**
     * @param string $imagetitle
     */
    public function setImagetitle($imagetitle)
    {
        $this->imagetitle = $imagetitle;
    }    
}

