<?php
/**
 * Created by IntelliJ IDEA.
 * User: WRheeder
 * Date: 25/01/2017
 * Time: 10:58 AM
 */

class Frontend extends ApiFrontend{
    var $leftBar;
    var $mm;
    function init()
    {
        parent::init();
        $this->api_public_path = dirname(@$_SERVER['SCRIPT_FILENAME']);
        $this->api_base_path = dirname(dirname(@$_SERVER['SCRIPT_FILENAME']));
        $this->dbConnect();
        $this->addLocations();
        $this->addProjectLocations();
        $this->addAddonsLocations();
        $this->add('jUI');
        $this->initAddons();
    }

    function initLayout()
    {
        $auth = $this->add('ApplicationAuth');
        $l = $this->add('Layout_Fluid');
        $m = $l->add('Menu_Horizontal', null, 'Top_Menu');
        $m->addMenuItem('index', 'Home');
        if ($auth->isLoggedIn()) {
            $l->template->Set('user_icon','http://www.gravatar.com/avatar/wrheeder');
            if ($auth->get('config_data_menu')) {
                //        $m->addMenuItem('configdata', 'Config Data Importer');
            }
            //           $l->template->tryDel('user_icon');
            /* if ($auth->canUploadSites()) {
                 $m->addMenuItem('SiteUploader', 'Site Upload');
             }*/
            if ($auth->isAdmin()) {
                $m->addMenuItem('Admin','Admin');
            }

        }
        $this->mm=$this->leftBar=$l->addLeftBar();
        $this->auth->check();
        $this->addStylesheet("style");
        parent::initLayout();
    }

    private function addLocations()
    {
        $this->api->pathfinder->base_location->defineContents(array(
            'docs' => array('docs', 'doc'), // Documentation (external)
            'content' => 'content', // Content in MD format
            'addons' => 'vendor',
            'php' => array('shared',),
        )); //->setBasePath($this->api_base_path);
    }
    function addProjectLocations() {
//        $this->pathfinder->base_location->setBasePath($this->api_base_path);
//        $this->pathfinder->base_location->setBaseUrl($this->url('/'));
        $this->pathfinder->addLocation(
            array(
                'page' => 'page',
                'php' => '../shared',
            )
        )->setBasePath($this->api_base_path);
        $this->pathfinder->addLocation(
            array(
                'js' => 'js',
                'css' => 'css',
            )
        )
            ->setBaseUrl($this->url('/'))
            ->setBasePath($this->api_public_path)
        ;
    }

    function addAddonsLocations() {
        $base_path = $this->pathfinder->base_location->getPath();
        $file = $base_path . '/sandbox_addons.json';
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $objects = json_decode($json);
            foreach ($objects as $obj) {
                // Private location contains templates and php files YOU develop yourself
                /* $this->private_location = */
                $this->api->pathfinder->addLocation(array(
                    'docs' => 'docs',
                    'php' => 'lib',
                    'template' => 'templates',
                ))
                    ->setBasePath($base_path . '/' . $obj->addon_full_path)
                ;

                $addon_public = $obj->addon_symlink_name;
                // this public location cotains YOUR js, css and images, but not templates
                /* $this->public_location = */
                $this->api->pathfinder->addLocation(array(
                    'js' => 'js',
                    'css' => 'css',
                    'public' => './',
                    //'public'=>'.',  // use with < ?public? > tag in your template
                ))
                    ->setBasePath($this->api_base_path . '/' . $obj->addon_public_symlink)
                    ->setBaseURL($this->api->url('/') . $addon_public) // $this->api->pm->base_path
                ;
            }
        }
    }

    function initAddons() {
        $base_path = $this->pathfinder->base_location->getPath();
        $file = $base_path . '/sandbox_addons.json';
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $objects = json_decode($json);
            foreach ($objects as $obj) {
                // init addon
                $init_class_path = $base_path . '/' . $obj->addon_full_path . '/lib/Initiator.php';
                if (file_exists($init_class_path)) {
                    $class_name = str_replace('/', '\\', $obj->name . '\\Initiator');
                    $init = $this->add($class_name, array(
                        'addon_obj' => $obj,
                    ));
                }
            }
        }
    }
}