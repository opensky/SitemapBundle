<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Sitemap;

/**
 * Url
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class Url {
    const
		PATTERN = '~^
		  (http|https)://                         # protocol
		  (
			([a-z0-9-]+\.)+[a-z]{2,6}             # a domain name
			  |                                   #  or
			\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}    # a IP address
		  )
		  (:[0-9]+)?                              # a port (optional)
		  (/?|/\S+)                               # a /, nothing or a / with something
		$~ix',
		DAILY = 'daily',
		WEEKLY = 'weekly',
		MONTHLY = 'monthly',
		YEARLY = 'yearly'
	;

    protected $loc;
    protected $lastmod;
    protected $changefreq;
    protected $priority;

    public function __construct($loc) {
        if (!preg_match(self::PATTERN, $loc)) {
            throw new \InvalidArgumentException($loc . ' is not valid url location');
        }
        $this->loc = $loc;
    }

    public function getLoc() {
        return $this->loc;
    }

    public function setLastmod(\DateTime $lastmod) {
        $this->lastmod = $lastmod;
    }

    public function getLastmod() {
        return $this->lastmod;
    }

    public function setChangefreq($changefreq) {
        if ( !in_array($changefreq, array(self::DAILY, self::MONTHLY, self::WEEKLY, self::YEARLY))) {
            throw new \InvalidArgumentException('Change frequency ' . $changefreq . ' is invalid');
        }
        $this->changefreq = $changefreq;
    }

    public function getChangefreq() {
        return $this->changefreq;
    }

    public function setPriority($priority) {
        if ($priority <= 0 || $priority > 1) {
            throw new \InvalidArgumentException('Priority must be in between 0 and 1, ' . $priority . ' given');
        }
        $this->priority = $priority;
    }

    public function getPriority() {
        return number_format($this->priority, 1);
    }
}