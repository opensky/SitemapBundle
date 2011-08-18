<?php

namespace OpenSky\Bundle\SitemapBundle;

use OpenSky\Bundle\SitemapBundle\DependencyInjection\Compiler\SitemapProviderPass;
use OpenSky\Bundle\SitemapBundle\DependencyInjection\OpenSkySitemapExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * OpenSkySitemapBundle
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 * @copyright (c) 2010 OpenSky Project Inc
 */
class OpenSkySitemapBundle extends Bundle
{
    public function __construct()
    {
        $this->extension = new OpenSkySitemapExtension();
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SitemapProviderPass());
    }
}
