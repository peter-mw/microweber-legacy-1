<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 11/22/14
 * Time: 3:09 PM
 */

namespace Microweber;

error_reporting(E_ALL);
ini_set('display_errors', 1);
//use Illuminate\Support\Facades\Config;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\ClassLoader;
use Illuminate\Filesystem\Filesystem;

use Illuminate\Http\Request;
use Illuminate\Config\FileLoader;
use Artdevue\Fcache\Fcache;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Cache;
use Illuminate\Cache\Repository;

use Microweber\Database\MySqlConnection;

if (!defined('MW_VERSION')) {
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR . 'bootstrap.php');
}

class MicroweberServiceProvider extends ServiceProvider
{


    public function __construct($app)
    {
        ClassLoader::addDirectories(array(
            modules_path(),
            __DIR__,
        ));
        ClassLoader::register();
        spl_autoload_register(array($this, 'autoloadModules'));
        parent::__construct($app);
    }

    public function register()
    {
        \App::after(function () {
        });

        $this->app->bind('config', function ($app) {
            return new Providers\SaveConfig($app->getConfigLoader(), $app->environment());
        });

        $this->app->singleton('event_manager', function ($app) {
            return new Providers\Event($app);
        });

        $this->app->singleton('database_manager', function ($app) {
            return new Providers\DatabaseManager($app);
        });

        $this->app->singleton('format', function ($app) {
            return new Utils\Format($app);
        });

        $this->app->singleton('parser', function ($app) {
            return new Utils\Parser($app);
        });

        $this->app->singleton('url_manager', function ($app) {
            return new Providers\UrlManager($app);
        });
        $this->app->singleton('ui', function ($app) {
            return new Providers\Ui($app);
        });
        $this->app->singleton('content_manager', function ($app) {
            return new Providers\ContentManager($app);
        });


        $this->app->singleton('update', function ($app) {
            return new Providers\UpdateManager($app);
        });
        $this->app->singleton('cache_manager', function ($app) {
            return new Providers\CacheManager($app);
        });
        $this->app->singleton('config_manager', function ($app) {
            return new Providers\ConfigurationManager($app);
        });
        $this->app->singleton('media_manager', function ($app) {
            return new Providers\MediaManager($app);
        });
        $this->app->singleton('fields_manager', function ($app) {
            return new Providers\FieldsManager($app);
        });
        $this->app->singleton('forms_manager', function ($app) {
            return new Providers\FormsManager($app);
        });

        $this->app->singleton('notifications_manager', function ($app) {
            return new Providers\NotificationsManager($app);
        });

        $this->app->singleton('log_manager', function ($app) {
            return new Providers\LogManager($app);
        });
        $this->app->singleton('option_manager', function ($app) {
            return new Providers\OptionManager($app);
        });


        // remove me //

        $this->app->singleton('db_model', function () {
            return new \DbModel();
        });

        // end of remove me //


        $this->app->bind('template', function ($app) {
            return new Providers\Template($app);
        });
        $this->app->singleton('modules', function ($app) {
            return new Providers\Modules($app);
        });
        $this->app->singleton('category_manager', function ($app) {
            return new Providers\CategoryManager($app);
        });

        $this->app->singleton('menu_manager', function ($app) {
            return new Providers\MenuManager($app);
        });
        $this->app->singleton('user_manager', function ($app) {
            return new Providers\UserManager($app);
        });

        $this->app->singleton('shop_manager', function ($app) {
            return new Providers\ShopManager($app);
        });

        $this->app->singleton('layouts_manager', function ($app) {
            return new Providers\LayoutsManager($app);
        });
        $this->app->singleton('ui', function ($app) {
            return new Providers\Ui($app);
        });


//        $this->app->bind('module', function ($app) {
//            return new Models\Module($app);
//        });


//        $this->app->extend('db', function ($app) {
//            return new Db($app);
//        });

        //''app()->proba = 'ou';

//        $this->app->singleton('db.connection.mysql', function ($app, $parameters) {
//            list($connection, $database, $prefix, $config) = $parameters;
//            return new MySqlConnection($connection, $database, $prefix, $config);
//        });

        Event::listen('silluminate.query', function ($sql, $bindings, $time) {
            echo $sql; // select * from my_table where id=?
            print_r($bindings); // Array ( [0] => 4 )
            echo $time; // 0.58

            // To get the full sql query with bindings inserted
            $sql = str_replace(array('%', '?'), array('%%', '%s'), $sql);
            $full_sql = vsprintf($sql, $bindings);
        });


        // $this->registerModules();
    }


    public function boot()
    {
        parent::boot();

        Cache::extend('fcache', function ($app) {
            $store = new \Artdevue\Fcache\Fcache;
            return new Repository($store);
        });

        $is_installed = mw_is_installed();

        if (!$is_installed) {
            return;
        }
        $modules = load_all_functions_files_for_modules();


    }

    // from here http://www.sitepoint.com/autoloading-and-the-psr-0-standard/
    function autoloadModules($className)
    {
        $filename = modules_path() . $className . ".php";
        if (is_file($filename)) {
            require $filename;
        }
    }




//    protected function registerCache()
//    {
//        $this->app['mw.cache'] = $this->app->share(function ($app) {
//            return new Models\Cache($app);
//        });
//    }
//
//
//    protected function registerHtmlBuilder()
//    {
//        $this->app->bind('config', function($app)
//        {
//            return new SaveConfig($app->getConfigLoader(), $app->environment());
//        });
//    }
//
//    protected function registerFormBuilder()
//    {
//        $this->app['form'] = $this->app->share(function ($app) {
//            return new \admin\Controller();
//        });
//    }


} 