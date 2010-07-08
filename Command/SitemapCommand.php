<?php
/*
 * This file is part of the SitemapBundle for Symfony2 framework
 * created by Bulat Shakirzyanov <mallluhuct@gmail.com>
 */

namespace Bundle\SitemapBundle\Command;

use Symfony\Framework\WebBundle\Command\Command as BaseCommand;
use Symfony\Components\Console\Input\InputOption;
use Symfony\Components\Console\Input\InputArgument;
use Symfony\Components\Console\Input\InputInterface;
use Symfony\Components\Console\Output\OutputInterface;

/**
 * SitemapCommand allows you to run:
 * 
 * >php console sitemap:generate
 *
 * The command will find all annotated sitemap providers from dependency
 * injection container, and execute their populate() method.
 * After that, the dumper will dump the sitemap instance into a pre-cached
 * php class
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class SitemapCommand extends BaseCommand {

    /**
     * {@inheritDoc}
     */
    protected function configure() {
        $this
            ->setName('sitemap:generate')
            ->setDescription('Generate sitemap, using its data providers.');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $sitemap = $this->container->getService('sitemap');
        foreach ($this->container->findAnnotatedServiceIds('sitemap.provider') as $id => $attributes) {
            $this->container->getService($id)->populate($sitemap);
        }
        $this->container->getService('sitemap_dumper')->dump($sitemap);
    }
}