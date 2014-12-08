<?php

if (defined("INI_SYSTEM_CHECK_DISABLED") == false) {
    define("INI_SYSTEM_CHECK_DISABLED", ini_get('disable_functions'));
}


$autoinstall = false;


if (defined('MW_INSTALL_FROM_CONFIG')) {
    $autoinstall = mw_var('mw_autoinstall');
}
$installed = false;
if (mw_is_installed()) {
    $installed = mw_is_installed();
}

if ($installed != false) {
    if (function_exists('is_admin') and is_admin() == false) {
        exit('Must be admin');
    }
}


function __mw_install_log($text)
{
    if (defined('MW_CACHE_ROOT_DIR')) {
        if (!is_dir(MW_CACHE_ROOT_DIR)) {
            if (mkdir(MW_CACHE_ROOT_DIR) == false) {
                echo "<div>Couldn't create directory: " . MW_CACHE_ROOT_DIR . "</div>\n";
            }
        }
    }
    $log_file = MW_CACHE_ROOT_DIR . DIRECTORY_SEPARATOR . 'install_log.txt';
    if (!is_file($log_file)) {
        @touch($log_file);

    }
    if (is_file($log_file)) {
        if ($text == 'done') {
            @file_put_contents($log_file, "[" . date('H:i:s') . "] " . "\t" . $text . "<br>\n\r");

        } else {
            @file_put_contents($log_file, "[" . date('H:i:s') . "] " . "\t" . $text . "<br>\n\r", FILE_APPEND);

        }
    }

}


$done = false;
$auto_install = false;

$cfg = MW_CONFIG_FILE;
if (is_file($cfg) and is_readable($cfg)) {
    require ($cfg);
    if (isset($config) and is_array($config) and isset($config['db']) and is_array($config['db'])) {
        if (!isset($config['is_installed']) or (trim($config['is_installed'])) == 'no') {
            if (isset($config['autoinstall']) and (trim($config['autoinstall'])) == 'yes') {
                $autoinstall = $config;
                $autoinstall['is_installed'] = 'no';
                $auto_install = true;
            }
        }
    }
}
if (isset($autoinstall) and is_array($autoinstall) and isset($autoinstall['is_installed'])) {
    $to_save = $autoinstall;
} else {
    $to_save = $_REQUEST;
}


if (is_array($to_save)) {
    $to_save = array_change_key_case($to_save, CASE_LOWER);
}


$remove = array('{default_timezone}', '{table_prefix}', '{is_installed}',
    '{db_type}', '{db_host}', '{dbname}', '{db_user}', '{db_pass}',
    '{admin_username}', '{admin_password}', '{admin_email}', '{with_default_content}', '{default_template}');

if (isset($to_save['is_installed'])) {


    __mw_install_log('Starting install');

    if (isset($to_save['is_installed'])) {
        $f = mw_includes_path() . 'install' . DIRECTORY_SEPARATOR . 'config.base.php';
        $save_config = file_get_contents($f);
        __mw_install_log('Copying default config file');
        if (isset($to_save['custom_dsn'])) {
            if (trim($to_save['custom_dsn']) != '') {
                $to_save['dsn'] = $to_save['custom_dsn'];
            }
        }
        if (!isset($to_save['db_type'])) {
            $to_save['db_type'] = 'mysql';
        }
        if (isset($to_save['test'])) {

        }

        if (!isset($to_save['default_timezone'])) {
            $to_save['default_timezone'] = "UTC";
        }

        if (isset($to_save['table_prefix'])) {
            $prefix = trim($to_save['table_prefix']);
            if ($prefix != '') {
                $last_char = substr($prefix, -1);
                if ($last_char != '_') {
                    $prefix = $prefix . '_';
                    $to_save['table_prefix'] = $prefix;
                }
            }

        }

        if (!defined('get_table_prefix()') and isset($to_save['table_prefix'])) {
            define('get_table_prefix()', $to_save['table_prefix']);
        }

        //$to_save['IS_INSTALLED'] = 'yes';

        $save_config_orig = $save_config;
        foreach ($to_save as $k => $v) {
            if (is_string($v)) {
                $save_config = str_ireplace('{' . $k . '}', $v, $save_config);
            }
        }
        $cfg = MW_CONFIG_FILE;
        //var_dump( $cfg);

        /*  file_put_contents($cfg, $save_config);
         mw()->cache_manager->clear();
         clearstatcache();
         sleep(2);*/


        if (isset($to_save['is_installed']) and $to_save['is_installed'] != 'yes') {

            if (isset($to_save['is_installed']) and $to_save['is_installed'] != 'yes') {


            }


            if (!isset($to_save['db_type'])) {
                if (isset($to_save['db']['type'])) {
                    $to_save['db_type'] = $to_save['db']['type'];
                }
            }
            if (!isset($to_save['db_host'])) {
                if (isset($to_save['db']['host'])) {
                    $to_save['db_host'] = $to_save['db']['host'];
                }
            }
            if (!isset($to_save['dbname'])) {
                if (isset($to_save['db']['dbname'])) {
                    $to_save['dbname'] = $to_save['db']['dbname'];
                }
            }
            if (!isset($to_save['db_user'])) {
                if (isset($to_save['db']['user'])) {
                    $to_save['db_user'] = $to_save['db']['user'];
                }
            }
            if (!isset($to_save['db_pass'])) {
                if (isset($to_save['db']['pass'])) {
                    $to_save['db_pass'] = $to_save['db']['pass'];
                }
            }

            __mw_install_log('Testing database settings');

            if ($to_save['db_pass'] == '') {
                $temp_db = array('type' => $to_save['db_type'], 'host' => $to_save['db_host'], 'dbname' => $to_save['dbname'], 'user' => $to_save['db_user']);
            } else {
                $temp_db = array('type' => $to_save['db_type'], 'host' => $to_save['db_host'], 'dbname' => $to_save['dbname'], 'user' => $to_save['db_user'], 'pass' => $to_save['db_pass']);
            }

            mw()->cache_manager->clear();
            // if($to_save['db_user'] == 'root'){

            //              $new_db = $to_save['dbname'];
            //              $query_make_db="CREATE DATABASE IF NOT EXISTS $new_db";
            //              $qz = \mw()->database_manager->query($query_make_db, $cache_id = false, $cache_group = false, $only_query = false, $temp_db);
            //          if (isset($qz['error'])) {
            //                      //  var_dump($qz);
            //                          print('Error with the database creation! ');
            //                      }

            // }


            $qs = "SELECT '' AS empty_col";
            //var_dump($qs);
            mw_var('temp_db', $temp_db);
            $qz = mw()->database_manager->query($qs, $cache_id = false, $cache_group = false, $only_query = false, $temp_db);

            if (isset($qz['error'])) {
                __mw_install_log('Database Settings Error');

                _e("Error with the database connection or database probably does not exist!");
                exit();
            } else {

                if (!strstr(INI_SYSTEM_CHECK_DISABLED, 'ini_set')) {
                    ini_set('memory_limit', '512M');
                    ini_set("set_time_limit", 600);
                    __mw_install_log('Increasing server memory');
                }
                if (!strstr(INI_SYSTEM_CHECK_DISABLED, 'set_time_limit')) {
                    set_time_limit(0);
                }


                __mw_install_log('Clearing cache');

                mw()->cache_manager->clear();

                $save_config = $save_config_orig;
                $to_save['is_installed'] = 'no';
                foreach ($to_save as $k => $v) {
                    if (is_string($v)) {
                        $save_config = str_ireplace('{' . $k . '}', $v, $save_config);
                    }
                }


                $default_htaccess_file = MW_ROOTPATH . '.htaccess';

                __mw_install_log('Checking .htaccess');
                $to_add_htaccess = true;
                if (is_file($default_htaccess_file)) {
                    $default_htaccess_file_c = file_get_contents($default_htaccess_file);
                    if (strstr($default_htaccess_file_c, 'mw htaccess')) {
                        $to_add_htaccess = false;
                    }
                }


                if ($to_add_htaccess == true) {
                    $f_htaccess = mw_includes_path() . 'install' . DIRECTORY_SEPARATOR . 'htaccess_mw.txt';
                    if (is_file($f_htaccess)) {
                        $f_htaccess_file_c = file_get_contents($f_htaccess);
                        if (strstr($f_htaccess_file_c, 'mw htaccess')) {
                            if (isset($_SERVER['SCRIPT_NAME'])) {
                                $dnht = dirname($_SERVER['SCRIPT_NAME']);
                            } else if (isset($_SERVER['PHP_SELF'])) {
                                $dnht = dirname($_SERVER['PHP_SELF']);
                            }


                            if (isset($dnht) and !defined('MW_INSTALL_FROM_CONFIG')) {
                                $dnht = str_replace('\\', '/', $dnht);
                                $dnht = str_replace(' ', '%20', $dnht);
                                if ($dnht != '/' and $dnht != '.' and $dnht != './' and$dnht != DIRECTORY_SEPARATOR) {
                                    // $f_htaccess_file_c = str_ireplace('/your_sub_folder/', $dnht, $f_htaccess_file_c);

                                    $f_htaccess_file_c = str_ireplace('#RewriteBase /your_sub_folder/', 'RewriteBase ' . $dnht . '/', $f_htaccess_file_c);


                                }
                            }

                            __mw_install_log('Adding .htaccess');
                            file_put_contents($default_htaccess_file, $f_htaccess_file_c, FILE_APPEND);
                        }
                    }

                }

                if (isset($_SERVER["SERVER_SOFTWARE"])) {

                    $sSoftware = strtolower($_SERVER["SERVER_SOFTWARE"]);
                    if (stripos($sSoftware, "microsoft-iis") !== false or stristr($sSoftware, "microsoft-iis") !== false) {
                        __mw_install_log($_SERVER["SERVER_SOFTWARE"]);
                        $default_webconfig_iis_file = MW_ROOTPATH . 'Web.config';

                        $to_add_webconfig_iis = true;
                        if (is_file($default_webconfig_iis_file)) {
                            $default_htaccess_file_c = file_get_contents($default_webconfig_iis_file);
                            if (strstr($default_htaccess_file_c, '<action type="Rewrite" url="index.php" />')) {
                                $to_add_webconfig_iis = false;
                            }
                        }


                        __mw_install_log('Web.config check ' . $to_add_webconfig_iis);


                        if ($to_add_webconfig_iis == true) {
                            $f_htaccess = mw_includes_path() . 'install' . DIRECTORY_SEPARATOR . 'Web.config.txt';
                            if (is_file($f_htaccess)) {
                                $f_htaccess_c = file_get_contents($f_htaccess);
                                __mw_install_log('Adding Web.config');
                                file_put_contents($default_webconfig_iis_file, $f_htaccess_c, FILE_APPEND);
                            }

                        }


                    }
                }


                __mw_install_log('Writing config file');
                file_put_contents($cfg, $save_config);
                __mw_install_log('Clearing cache');
                clearstatcache();


                mw()->cache_manager->clear();
                // _reload_c();

                $local_config = mw('application')->loadConfigFromFile($cfg, true);

                if (!defined('USER_ID')) {

                    define('USER_ID', 1);
                }
                if (!defined('USER_IS_ADMIN')) {

                    define('USER_IS_ADMIN', 1);
                }
                if (!defined('MW_FORCE_MOD_INSTALLED')) {

                    define('MW_FORCE_MOD_INSTALLED', 1);
                }
                __mw_install_log('Initializing settings');
                mw('option')->db_init();
                __mw_install_log('Setting default settings');
                mw('option')->_create_mw_default_options();


                __mw_install_log('Initializing users');
                mw()->user_manager->db_init();

                event_trigger('mw_db_init_options');
                event_trigger('mw_db_init_users');


                __mw_install_log('Creating default database tables');
                mw()->cache_manager->clear('db');
                event_trigger('mw_db_init_default');
                event_trigger('mw_db_init');
                mw()->content_manager->db_init();
                __mw_install_log('Creating log database tables');
                mw('notifications')->db_init();
                __mw_install_log('Creating online shop database tables');
                mw()->shop_manager->db_init();
                mw()->shop_manager->create_mw_shop_default_options();
                __mw_install_log('Creating modules database tables');

                mw()->modules->db_init();

                if (!defined('MW_FORCE_SAVE_EXTENDED')) {

                    define('MW_FORCE_SAVE_EXTENDED', 1);
                }
                if (mw_is_installed() != true) {
                    if (isset($to_save['admin_username']) and isset($to_save['admin_password']) and $to_save['admin_username'] != '') {
                        if ($to_save['admin_username'] != '{admin_username}') {
                            __mw_install_log('Adding admin user');
                            $new_admin = array();
                            $new_admin['username'] = $to_save['admin_username'];
                            $new_admin['password'] = ($to_save['admin_password']);
                            if (isset($to_save['admin_email']) and $to_save['admin_email'] != '') {
                                $new_admin['email'] = $to_save['admin_email'];
                            }
                            $new_admin['is_active'] = 1;
                            $new_admin['is_admin'] = 'y';
                            mw_var('FORCE_SAVE', get_table_prefix() . 'users');
                            save_user($new_admin);
                        }
                    }
                }
                $save_config = $save_config_orig;
                $to_save['is_installed'] = 'yes';
                foreach ($to_save as $k => $v) {
                    if (is_string($v)) {
                        $save_config = str_ireplace('{' . $k . '}', $v, $save_config);
                    }
                }
                file_put_contents($cfg, $save_config);
                __mw_install_log('Finalizing config file');

                event_trigger('mw_db_init_modules');
                __mw_install_log('Scanning for modules');

                mw()->modules->scan_for_modules("skip_cache=1&cleanup_db=1");
                __mw_install_log('Installing modules');
                mw()->modules->update_db();





                __mw_install_log('Loading modules');
                event_trigger('mw_scan_for_modules');



                clearstatcache();
                _reload_c();


                if (isset($to_save['with_default_content'])) {
                    if ($to_save['with_default_content'] != '{with_default_content}' and $to_save['with_default_content'] != 'no') {
                        $default_content_folder = mw_includes_path() . 'install' . DIRECTORY_SEPARATOR;
                        $default_content_file = $default_content_folder . 'mw_default_content.zip';

                        if (isset($to_save['default_template']) and $to_save['default_template'] != false and $to_save['default_template'] != '{default_template}') {
                            if (defined('templates_path()')) {
                                $template_dir = templates_path().DS.$to_save['default_template'];
                                $template_dir = normalize_path($template_dir,true);
                                if(is_dir($template_dir)){
                                    $template_default_content = $template_dir.'mw_default_content.zip';
                                    if(is_file($template_default_content) and is_readable($template_default_content)){
                                        $default_content_file = $template_default_content;
                                        $default_content_folder = $template_dir;

                                    }
                                }
                             }

                        }

                        if (is_file($default_content_file)) {
                            __mw_install_log('Installing default content');
                            define("MW_NO_DEFAULT_CONTENT", true);
                            $restore = new \Microweber\Utils\Backup();
                            $restore->backups_folder = $default_content_folder;
                            $restore->backup_file = 'mw_default_content.zip';
                            // $restore->debug = 1;
                            ob_start();
                            $rest = $restore->exec_restore();

                            ob_get_clean();
                            __mw_install_log('Default content is installed');
                        }
                    }
                }

                if (isset($to_save['default_template']) and $to_save['default_template'] != false and $to_save['default_template'] != '{default_template}') {
                    $templ = $to_save['default_template'];
                    $templ = str_replace('..', '', $templ);
                    $option = array();
                    $option['option_value'] = trim($templ);
                    $option['option_key'] = 'current_template';
                    $option['option_group'] = 'template';
                    mw_var('FORCE_SAVE', get_table_prefix() . 'options');

                    $option = mw('option')->save($option);

                    mw()->cache_manager->delete('options');
                }


                __mw_install_log('Clearing cache after install');

                mw()->cache_manager->clear();

                // mw()->content_manager->create_default_content('install');
                if ($auto_install != false) {
                    $done = true;
                    $f = mw_includes_path() . 'install' . DIRECTORY_SEPARATOR . 'main.php';
                    include ($f);
                    exit();
                } else {
                    print('done');
                }


                __mw_install_log('done');

            }
            // @//session_write_close();
            exit();

            //var_dump($_REQUEST);
            //$l = \mw()->database_manager->query_log(true);
            //var_dump($l);
        } else {
            $done = true;
            $f = mw_includes_path() . 'install' . DIRECTORY_SEPARATOR . 'done.php';
            include ($f);
            exit();
        }

        //  var_dump($save_config);
    }

}

if (!isset($to_save['IS_INSTALLED'])) {
    $cfg = MW_CONFIG_FILE;

    $data = false;
    if (is_file($cfg)) {

        include ($cfg);
        if (isset($config)) {
            $data = $config;
        }

        //
    }
    if (is_array($data)) {

        foreach ($data as $key => $value) {

            if (is_string($value) and !is_array($key)) {
                $value_clean = str_ireplace($remove, '', $value);
                $data[$key] = $value_clean;
            }

        }
    }


    __mw_install_log('Preparing to install');
    $f = mw_includes_path() . 'install' . DIRECTORY_SEPARATOR . 'main.php';
    include ($f);
}
