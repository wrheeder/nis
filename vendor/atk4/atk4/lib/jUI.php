<?php
/**
 * jQuery UI support
 */
class jUI extends jQuery
{
    private $atk4_initialised = false;

    public function init()
    {
        parent::init();
        if (@$this->app->jui) {
            throw $this->exception('Do not add jUI twice');
        }
        $this->app->jui = $this;

        $this->addDefaultIncludes();

        $this->atk4_initialised = true;
    }
    public function addDefaultIncludes()
    {
        $this->addInclude('start-atk4');

        /* $config['js']['jqueryui']='https://code.jquery.com/ui/1.11.4/jquery-ui.min.js'; // to use CDN */
        if ($v = $this->app->getConfig('js/versions/jqueryui', null)) {
            $v = 'jquery-ui-'.$v;
        } else {
            $v = $this->app->getConfig('js/jqueryui', 'jquery-ui-1.11.4.min');  // bundled jQueryUI version
        }

        $this->addInclude($v);

        $this->addInclude('ui.atk4_loader');
        $this->addInclude('ui.atk4_notify');
        $this->addInclude('atk4_univ_basic');
        $this->addInclude('atk4_univ_jui');
    }
    public function addInclude($file, $ext = '.js')
    {
        if (strpos($file, 'http') === 0) {
            parent::addOnReady('$.atk4.includeJS("'.$file.'")');

            return $this;
        }
        $url = $this->app->locateURL('js', $file.$ext);

        if (!$this->atk4_initialised) {
            return parent::addInclude($file, $ext);
        }

        parent::addOnReady('$.atk4.includeJS("'.$url.'")');

        return $this;
    }
    public function addStylesheet($file, $ext = '.css', $template = false)
    {
        $url = $this->app->locateURL('css', $file.$ext);
        if (!$this->atk4_initialised || $template) {
            return parent::addStylesheet($file, $ext);
        }

        parent::addOnReady('$.atk4.includeCSS("'.$url.'")');
    }
    public function addOnReady($js)
    {
        if (is_object($js)) {
            $js = $js->getString();
        }
        if (!$this->atk4_initialised) {
            return parent::addOnReady($js);
        }

        $this->app->template->append('document_ready', '$.atk4(function(){ '.$js."; });\n");

        return $this;
    }
}
