<?php

namespace OpenSky\Bundle\SitemapBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateCommand
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 */
class GenerateCommand extends ContainerAwareCommand
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
        foreach ($this->getProviders() as $id) {
            $this->getContainer()->get($id)->populate($sitemap);
        }
        $this->getSitemapDumper()->dump($sitemap);
    }

    /**
     * @return Bundle\SitemapBundle\Sitemap\Sitemap
     */
    protected function getSitemap()
    {
        return $this->getContainer()->get('opensky.sitemap');
    }

    /**
     * @return Bundle\SitemapBundle\Dumper\Dumper
     */
    protected function getSitemapDumper()
    {
        return $this->getContainer()->get('opensky.sitemap.dumper');
    }

    /**
     * @return array Provider service ids
     */
    protected function getProviders()
    {
        return $this->getContainer()->getParameter('opensky.sitemap.providers');
    }
}