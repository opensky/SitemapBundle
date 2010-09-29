<?php

namespace Bundle\SitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateCommand
 *
 * @package OpenSky SitemapBundle
 * @version $Id$
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 * @license http://www.gnu.org/licenses/agpl.txt GNU Affero General Public License
 */
class GenerateCommand extends BaseCommand
{

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('sitemap:generate')
            ->setDescription('Generate sitemap, using its data providers.');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $sitemap = $this->getSitemap();
        foreach ($this->container->findTaggedServiceIds('sitemap.provider') as $id => $attributes) {
            $this->container->get($id)->populate($sitemap);
        }
        $this->getSitemapDumper()->dump($sitemap);
    }

    /**
     * @return Bundle\SitemapBundle\Sitemap\Sitemap
     */
    protected function getSitemap()
    {
        return $this->container->get('sitemap');
    }

    /**
     *
     * @return Bundle\SitemapBundle\Dumper\Dumper
     */
    protected function getSitemapDumper()
    {
        return $this->container->get('sitemap.dumper');
    }

}