<?php
/**
 * Undocumented.
*/
class App_Installer extends App_Web
{
    /**
     * Name of page class
     *
     * @var string
     */
    public $page_class = 'Page';

    /**
     * Page options
     *
     * @var array
     */
    public $page_options = null;

    /**
     * Should we show intro?
     *
     * @var bool
     */
    protected $show_intro = false;

    /**
     * Initialization.
     */
    public function init()
    {
        parent::init();

        $this->page = null;
        $this->saved_base_path = $this->pm->base_path;
        $this->pm->base_path .= basename($_SERVER['SCRIPT_NAME']);

        $this->add('jUI');

        $this->template->trySet('version', 'Web Software Installer');

        $this->stickyGET('step');

        $this->s_first = $this->s_last = $this->s_current = $this->s_prev = $this->s_next = null;
        $this->s_cnt = 0;

        $this->initInstaller();
    }

    /**
     * @todo Description
     */
    public function initInstaller()
    {
        //$m = $this->layout->add('Menu');

        foreach (get_class_methods($this) as $method) {
            list($a, $method) = explode('step_', $method);
            if (!$method) {
                continue;
            }

            if (is_null($this->s_first)) {
                $this->s_first = $method;
            }
            $this->s_last = $method;

            $u = $this->url(null, array('step' => $method));
            $this->page = $this->pm->base_path.'?step='.$_GET['step'];
           // $m->addMenuItem($u, ucwords(strtr($method, '_', ' ')));
            $this->page = null;

            if (is_null($this->s_current)) {
                ++$this->s_cnt;
                if ($method == $_GET['step']) {
                    $this->s_current = $method;
                    $this->s_title = ucwords(strtr($method, '_', ' '));
                } else {
                    $this->s_prev = $method;
                }
            } else {
                if (is_null($this->s_next)) {
                    $this->s_next = $method;
                }
            }
        }

        if (!$_GET['step']) {
            if ($this->show_intro) {
                return $this->showIntro($this->makePage('init'));
            }

            $this->app->redirect($this->stepURL('first'));
        }

        $this->initStep($this->s_current);
    }

    /**
     * @todo Description
     *
     * @param string $step Unused parameter !!!
     * @param string $template
     * @return View
     */
    public function makePage($step, $template = 'step/default')
    {
        return $this->layout->add($this->page_class, null, null, array($template));
    }

    /**
     * @todo Description
     *
     * @param string $step
     * @return mixed
     */
    public function initStep($step)
    {
        $step_method = 'step_'.$step;
        if (!$this->hasMethod($step_method)) {
            return $this->add('H1')->set('No such step');
        }
        $this->header = $this->add('H1')->set('Step '.$this->s_cnt.': '.$this->s_title);
        $page = $this->makePage($step);

        return $this->$step_method($page);
    }

    /**
     * @todo Description
     *
     * @param View $p
     */
    public function showIntro($p)
    {
        $p->add('H1')->set('Welcome to Web Software');
        $p->add('P')->set('Thank you for downloading this software. '.
            'This wizard will guide you through the installation procedure.');

        if (!is_writable('.')) {
            $p->add('View_Warning')->setHTML('This installation does not have permissions to create your '.
                '<b>config.php</b> file for you. You will need to manually create this file');
        } elseif (file_exists('config.php')) {
            $p->add('View_Warning')->setHTML('It appears that you already have <b>config.php</b> file in your '.
                'application folder. This installation will read defaults from config.php, but it will ultimatelly '.
                '<b>overwrite</b> it with the new settings.');
        }

        $p->add('Button')->set('Start')->js('click')->univ()->location($this->stepURL('first'));
    }

    /**
     * @todo Description
     *
     * @param string $position
     *
     * @return URL
     */
    public function stepURL($position)
    {
        $s = 's_'.$position;
        $s = $this->$s;

        return $this->url(null, array('step' => $s));
    }
}
