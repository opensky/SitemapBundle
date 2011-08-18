# Adding bundle to Kernel

This bundle will help with sitemap generation in your Symfony2 based projects.
To enable the sitemap bundle, add it to you kernel registerBundles() method:

    use Symfony\Foundation\Kernel;

    class MyKernel extends Kernel {
        ...
        public function registerBundles() {
            return array(
                ...
                new OpenSky\Bundle\SitemapBundle\SitemapBundle(),
                ...
            );
        }
    }

# Enabling the services

The second step is to enable its DependencyInjection extension in your config.yml:

    opensky_sitemap:
      default_lastmod:    2010-06-01
      default_changefreq: monthly
      default_priority:   0.5
      driver:             odm.mongodb

In the example above, we enabled the sitemap with `odm.mongodb` driver, which means that you have to add `DoctrineMongoDBBundle` to your Kernel and register its configuration like so:

    doctrine_odm.mongodb:
      auto_generate_proxy_classes: true
      default_document_manager: default
      default_connection: mongodb
      cache_driver: array
      metadata_cache_driver: array
      default_database: opensky
      proxy_namespace: Proxies
      document_managers:
        default:
          connection: mongodb
      connections:
        mongodb:
          server: localhost:27017

# Writing custom url providers for *sitemap:generate* command

The third step is to write your url providers to populate the 'sitemap' with
existing urls, e.g:

    <?php

    namespace My\ForumBundle\Sitemap;

    use OpenSky\Bundle\SitemapBundle\Sitemap\Provider as SitemapProvider;
    use OpenSky\Bundle\SitemapBundle\Sitemap\Sitemap;
    use Symfony\Component\Routing\Router;
    use My\ForumBundle\Document\TopicRepository;

    class ForumTopicProvider implements SitemapProvider {

        protected $topicRepository;
        protected $router;

        public function __construct(TopicRepository $topicRepository, Router $router)
        {
            $this->topicRepository = $topicRepository;
            $this->router = $router;
        }

        public function populate(Sitemap $sitemap)
        {
            foreach ($this->topicRepository->find() as $topic) {
                $sitemap->add($this->router->generate('topic_view', array(
                        'id' => $topic->getId(),
                    ), true), array(
                        'changefreq' => \OpenSky\Bundle\SitemapBundle\Sitemap\Url::MONTHLY,
                        'lastmod'    => $seller->getUpdatedAt(),
                        'priority'   => '0.8',
                    )
                );
            }
        }
    }

**NOTE:** in the above example, we use router to generate absolute urls. Since you don't have knowledge of your current domain in the CLI mode (the mode, where commands are run), router will still generate relative urls. You have to make the host a container parameter, that you will then use inside of your sitemap provider.

And register your provider in DIC like this:

    <service id="forum.sitemap.provider" class="My\ForumBundle\ForumTopicProvider">
        <tag name="opensky.sitemap.provider" />
        <argument type="service" id="forum.document_repository.topic" />
        <argument type="service" id="router" />
    </service>

After providers are in place and registered, time to run the generation command:

    > php forum/console sitemap:generate

or simply:

    > php forum/console sitemap:g

# Creating/Updating sitemap urls in the application

After the three steps were completed, you can use Symfony2's native 'event_dispatcher'
service to let the 'sitemap' know of new url:

    $eventDispatcher->notify(new Event($this, 'opensky.sitemap.create', array(
        'loc'        => $router->generate('user_view', array('id' => $user->getId())),
        'changefreq' => 'daily',
        'priority'   => '0.8',
    )));

or existing url updates:

    $eventDispatcher->notify(new Event($this, 'opensky.sitemap.update', array(
        'loc'        => $router->generate('user_view', array('id' => $user->getId())),
        'priority'   => '0.6',
    )));

# Enabling sitemap routes

The last and most important step is to enable sitemap routing in your routing.yml:

    sitemap:
      resource: @OpenSkySitemapBundle/Resources/config/routing.yml

After that is done, you can access your sitemap at /sitemap.xml and siteindex at /siteindex.xml

Happy Coding