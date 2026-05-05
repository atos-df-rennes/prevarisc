<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
        // Chargement des plugins de base
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_View());
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_ACL());
        Zend_Controller_Front::getInstance()->registerPlugin(new Plugin_XmlHttpRequest());

        // Chargement des plugins tiers
        if (getenv('PREVARISC_THIRDPARTY_PLUGINS')) {
            $thirdparty_plugins = explode(';', getenv('PREVARISC_THIRDPARTY_PLUGINS'));
            foreach ($thirdparty_plugins as $thirdparty_plugin) {
                Zend_Controller_Front::getInstance()->registerPlugin(new $thirdparty_plugin());
            }
        }

        return parent::run();
    }

    /**
     * Initialisation de l'auto-loader.
     */
    protected function _initAutoLoader(): Zend_Loader_Autoloader
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();

        $autoloader_application = new Zend_Application_Module_Autoloader(['basePath' => APPLICATION_PATH, 'namespace' => null]);

        $autoloader_application->addResourceType('cache', 'cache/', 'Cache');

        $autoloader->pushAutoloader($autoloader_application);

        return $autoloader;
    }

    /**
     * Initialisation d'un cache standard.
     *
     * @param array $frontendOptions surcharge des options de configuration du front
     * @param array $backendOptions  surcharge des options de configuration du back
     *
     * @return Zend_Cache_Core une instance de cache
     */
    protected function getCache(array $frontendOptions = [], array $backendOptions = [])
    {
        $options = $this->getOption('cache');

        return Zend_Cache::factory(
            // front adapter
            'Core',
            // back adapter
            $options['adapter'],
            // frontend options
            array_merge([
                'caching' => $options['enabled'],
                'lifetime' => $options['lifetime'],
                'cache_id_prefix' => 'prevarisc_'.md5(getenv('PREVARISC_DB_DBNAME')).'_',
                'write_control' => $options['write_control'],
            ], $frontendOptions),
            // backend options
            array_merge([
                'servers' => [
                    [
                        'host' => $options['host'],
                        'port' => $options['port'],
                    ],
                ],
                'compression' => $options['compression'],
                'read_control' => $options['read_control'],
                'cache_dir' => $options['cache_dir'],
                'cache_file_perm' => 0666,
                'hashed_directory_perm' => 0777,
            ], $backendOptions),
            // use a custom name for front
            false,
            // use a custom name for back
            $options['customAdapter'],
            // use application's autoload if an adapter is not loaded
            true
        );
    }

    /**
     * Initialisation du cache objet de l'application.
     */
    protected function _initCache(): Zend_Cache_Core
    {
        return $this->getCache();
    }

    /**
     * Initialisation du cache spécial recherches.
     */
    protected function _initCacheSearch(): Zend_Cache_Core
    {
        return $this->getCache();
    }

    /**
     * Initialisation de la vue.
     */
    protected function _initView(): Zend_View
    {
        $view = new Zend_View();

        $view->headMeta()
            ->appendName('viewport', 'width=device-width,initial-scale=1')
            ->appendHttpEquiv('X-UA-Compatible', 'IE=edge,chrome=1')
            ->appendName('description', 'Logiciel de gestion du service Prévention')
            ->appendName('author', 'SDIS62 - Service Recherche et Développement');

        $view->addHelperPath(APPLICATION_PATH.'/views/helpers');

        return $view;
    }

    /**
     * Initialisation du layout.
     */
    protected function _initLayout(): Zend_Layout
    {
        return Zend_Layout::startMvc(['layoutPath' => APPLICATION_PATH.DS.'layouts']);
    }

    /**
     * Initialisation du data store à utiliser.
     *
     * @return object
     */
    public function _initDataStore()
    {
        $options = $this->getOption('resources');
        $options = $options['dataStore'];

        $className = $options['adapter'];

        return new $className($options);
    }

    public function _initTranslator(): void
    {
        $translator = new Zend_Translate(
            [
                'adapter' => 'array',
                'content' => implode(DS, [
                    APPLICATION_PATH,
                    '..',
                    'vendor',
                    'zendframework',
                    'zendframework1',
                    'resources',
                    'languages',
                ]),
                'locale' => 'fr',
                'scan' => Zend_Translate::LOCALE_DIRECTORY,
            ]
        );
        Zend_Validate_Abstract::setDefaultTranslator($translator);
    }

    public function _initAuth(): Zend_Session_Namespace
    {
        $options = $this->getOption('cache');
        $max_lifetime = isset($options['session_max_lifetime']) ? (int) $options['session_max_lifetime'] : 7200;

        // Bridge Symfony → Zend : PHP définit la constante SID dès le premier session_start()
        // et elle ne peut jamais être undéfinie. ZF1 interdit alors tout nouveau démarrage via
        // son check defined('SID') → Zend_Session_Exception.
        // Quand le fallback legacy est déclenché après traitement Symfony, on doit :
        //   1. Rouvrir la session PHP (fermée par session_write_close() côté Symfony)
        //   2. Synchroniser les drapeaux internes de ZF1 pour bypasser son start()
        if (defined('SID')) {
            if (PHP_SESSION_NONE === session_status()) {
                session_start();
            }

            $rStarted  = new ReflectionProperty('Zend_Session', '_sessionStarted');
            $rReadable = new ReflectionProperty('Zend_Session_Abstract', '_readable');
            $rWritable = new ReflectionProperty('Zend_Session_Abstract', '_writable');

            foreach ([$rStarted, $rReadable, $rWritable] as $r) {
                $r->setAccessible(true);
                $r->setValue(null, true);
            }
        }

        $namespace = new Zend_Session_Namespace(Zend_Auth::class);
        $namespace->setExpirationSeconds($max_lifetime);

        return $namespace;
    }

    public function _initRoutes(): void
    {
        include APPLICATION_PATH . '/routes.php';
    }
}
