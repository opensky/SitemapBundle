<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Dumper;

use Bundle\SitemapBundle\Sitemap\Sitemap;

/**
 * PHPDumer is dumping prepared sitemap into a pre-cached php file.
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class PHPDumper implements Dumper {

    protected $filename;
    protected $class;

    public function __construct($filename, $class) {
        $this->filename = $filename;
        $this->class = $class;
    }

    public function dump(Sitemap $sitemap) {
        if (false === file_put_contents($this->filename, $this->getClassTemplate($sitemap))) {
            throw new \RuntimeException('Could not create file ' . $file);
        }
        return true;
    }

    public function getClassTemplate(Sitemap $sitemap) {
        return <<<EOT
<?php
class $this->class extends \\Bundle\\SitemapBundle\\Sitemap\\Sitemap {
    public function __construct(array \$defaults = array()) {
        parent::__construct(\$defaults);{$this->getContructor($sitemap)}
    }
}
EOT;
    }

    public function getContructor(Sitemap $sitemap) {
        $constructor = '';
        foreach ($sitemap->getUrls() as $url) {
            $info = array();
            foreach (array('lastmod', 'changefreq', 'priority') as $property) {
                if (null !== ($value = $url->{'get' . ucfirst($property)}())) {
                    $info[$property] = $value;
                }
            }
            $params = var_export($info, true);
            $constructor .= <<<EOT

        \$this->add('{$url->getLoc()}', $params);
EOT;
        }
        return $constructor;
    }

}