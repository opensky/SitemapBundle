<?php

namespace OpenSky\Bundle\SitemapBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class SitemapProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $providers = array();
        foreach ($container->findTaggedServiceIds('opensky.sitemap.provider') as $id => $attributes) {
            $providers[] = $id;
        }

        if (count($providers)) {
            $container->setParameter('opensky.sitemap.providers', $providers);
        }
    }
}
