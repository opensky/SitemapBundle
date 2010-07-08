This bundle will help with sitemap generation in your Symfony2 based projects.
To enable the sitemap bundle, add it to you kernel registerBundles() method:

    use Symfony\Foundation\Kernel;

    class MyKernel extends Kernel {
        ...
        public function registerBundles() {
            return array(
                ...
                new Bundle\SitemapBundle\SitemapBundle(),
                ...
            );
        }
    }

The second step is to enable its DependencyInjection extension in your config.yml:

    sitemap.sitemap: ~

After the two steps were completed, you can use the 'sitemap' service:

    $this->container->getService('sitemap')->add('http://www.google.com/', array(
        'changefreq' => 'monthly',
        'priority'   => 1.0,
        'lastmod'    => 2010-01-01,
    ));

The last and most important step is to enable sitemap routing in your routing.yml:

    sitemap:
      resource: SitemapBundle/Resources/config/routing.yml

After that is done, you can access your sitemap at /sitemap.xml

Happy Coding