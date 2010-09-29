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

    sitemap.config:
      default_lastmod:    2010-06-01
      default_changefreq: monthly
      default_priority:   0.5
      driver:             odm.mongodb

The third step is to write your url providers to populate the 'sitemap' with
existing urls, e.g:

    <?php

    namespace My\ForumBundle;

    use Bundle\SitemapBundle\Sitemap\Provider as SitemapProvider;
    use Bundle\SitemapBundle\Sitemap\Sitemap;
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
                    )), array(
                        'changefreq' => \Bundle\SitemapBundle\Sitemap\Url::MONTHLY,
                        'lastmod'    => $seller->getUpdatedAt(),
                        'priority'   => '0.8',
                    )
                );
            }
        }
    }

After the three steps were completed, you can use Symfony2's native 'event_dispatcher'
service to let the 'sitemap' know of new url:

    $eventDispatcher->notify(new Event($this, 'sitemap.create', array(
        'loc'        => $router->generate('user_view', array('id' => $user->getId())),
        'changefreq' => 'daily',
        'priority'   => '0.8',
    )));

or existing url updates:

    $eventDispatcher->notify(new Event($this, 'sitemap.update', array(
        'loc'        => $router->generate('user_view', array('id' => $user->getId())),
        'priority'   => '0.6',
    )));

The last and most important step is to enable sitemap routing in your routing.yml:

    sitemap:
      resource: SitemapBundle/Resources/config/routing.yml

After that is done, you can access your sitemap at /sitemap.xml and siteindex at /siteindex.xml

Happy Coding