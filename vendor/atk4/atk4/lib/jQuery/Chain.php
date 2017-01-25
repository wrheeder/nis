<?php
/**
 * This class represents sequentall calls to one jQuery object.
 *
 * jQuery chain proxy. Returned by view->js(). Calls to this
 * class will be converted into jQuery calls.
 *
 * Feel free to call _XX functions in this class
 */
class jQuery_Chain extends AbstractModel
{
    public $str = '';
    public $prepend = '';
    public $library = null;
    public $enclose = false;
    public $preventDefault = false;
    public $base = '';
    public $debug = false;
    public $univ_called = false;
    public function __call($name, $arguments)
    {
        if ($arguments) {
            $a2 = $this->_flattern_objects($arguments, true);
            $this->str .= ".$name(".$a2.')';
        } else {
            $this->str .= ".$name()";
        }

        return $this;
    }
    public function __get($property)
    {
        /* this enables you  to have syntax like this:
         *
         * $this->js()->offset()->top <-- access object items, if object is
         * returned by chained method call */
        if (!property_exists($this, $property)) {
            $this->str .= ".$property";
        }

        return $this;
    }
    /* convert reserved words or used methods into js calls, such as "execute" */
    public function _fn($name, $arguments = array())
    {
        // Wrapper for functons which use reserved words
        return $this->__call($name, $arguments);
    }
    /* converting this object into string will produce JavaScript code */
    public function __toString()
    {
        return $this->_render();
    }

    /* Some methods shouldn't be special! */
    public function each()
    {
        return $this->__call('each', func_get_args());
    }

    /* Chain binds to parent object by default. Use this to use other selector $('selector') */
    public function _selector($selector = null)
    {
        if ($selector === false) {
            $this->library = null;
        } elseif ($selector instanceof self) {
            $this->library = '$('.$selector.')';
        } else {
            $this->library = '$('.json_encode($selector).')';
        }

        return $this;
    }

    /**
     * Allows to chain calls on different library.
     *
     * If you are using jQuery, then you can call _selector('blah')
     * which will result in $('blah') prefix, however if you want
     * to chain to any other library you can use this modifier instead:
     *
     * _library('window.player').play();
     *
     * will result in
     *
     * window.player.play();
     *
     * You must be sure to properly escape the string!
     */
    public function _library($library)
    {
        $this->library = $library;

        return $this;
    }
    /**
     * Use this to bind chain to document $(document)...
     *
     * @return [type] [description]
     */
    public function _selectorDocument()
    {
        return $this->_library('$(document)');
    }
    /**
     * Use this to bind chain to window $(window)...
     *
     * @return [type] [description]
     */
    public function _selectorWindow()
    {
        return $this->_library('$(window)');
    }
    /**
     * Use this to bind chain to "this" $(this)...
     */
    public function _selectorThis()
    {
        return $this->_library('$(this)');
    }
    /**
     * Use this to bind chain to "region" $(region). Region is defined by ATK when reloading.
     */
    public function _selectorRegion()
    {
        return $this->_library('$(region)');
    }
    /**
     * Execute more JavaScript code before chain. Avoid using.
     *
     * @param [type] $code [description]
     *
     * @return [type] [description]
     */
    public function _prepend($code)
    {
        if (is_array($code)) {
            $code = implode(';', $code);
        }
        $this->prepend = $code.';'.$this->prepend;

        return $this;
    }
    public function debug()
    {
        $this->debug = true;

        return $this;
    }
    /**
     * Send chain in response to form submit, button click or ajaxec() function for AJAX control output.
     *
     * @return [type] [description]
     */
    public function execute()
    {
        if (isset($_POST['ajax_submit']) || $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            //if($this->app->jquery)$this->app->jquery->getJS($this->owner);

            if (headers_sent($file, $line)) {
                echo "<br/>Direct output (echo or print) detected on $file:$line. <a target='_blank' "
                    ."href='http://agiletoolkit.org/error/direct_output'>Use \$this->add('Text') instead</a>.<br/>";
            }

            $x = $this->app->template->get('document_ready');
            if (is_array($x)) {
                $x = implode('', $x);
            }
            echo $this->_render();
            $this->app->hook('post-js-execute');
            exit;
        } else {
            throw $this
                ->exception('js()->..->execute() must be used in response to form submission or AJAX operation only');
        }
    }
    /* [private] used by custom json_encoding */
    public function _safe_js_string($str)
    {
        $l = strlen($str);
        $ret = '';
        for ($i = 0; $i < $l; ++$i) {
            switch ($str[$i]) {
                case "\r":
                    $ret .= '\\r';
                    break;
                case "\n":
                    $ret .= '\\n';
                    break;
                case '"':
                case "'":
                case '<':
                case '>':
                case '&':
                case '\\':
                    $ret .= '\x'.dechex(ord($str[$i]));
                    break;
                default:
                    $ret .= $str[$i];
                    break;
            }
        }

        return $ret;
    }
    /* [private] custom json_encoding. called on function arguments. */
    protected function _flattern_objects($arg, $return_comma_list = false)
    {
        /*
         * This function is very similar to json_encode, however it will traverse array
         * before encoding in search of objects based on AbstractObject. Those would
         * be replaced with their json representation if function exists, otherwise
         * with string representation
         */
        if (is_object($arg)) {
            if ($arg instanceof self) {
                $r = $arg->_render();
                if (substr($r, -1) == ';') {
                    $r = substr($r, 0, -1);
                }

                return $r;
            } elseif ($arg instanceof AbstractView) {
                return "'#".$arg->getJSID()."'";
            } else {
                return "'".$this->_safe_js_string((string) $arg)."'";    // indirectly call toString();
            }
        } elseif (is_array($arg)) {
            $a2 = array();
            // is array associative? (hash)
            $assoc = $arg != array_values($arg);

            foreach ($arg as $key => $value) {
                $value = $this->_flattern_objects($value);
                $key = $this->_flattern_objects($key);
                if (!$assoc || $return_comma_list) {
                    $a2[] = $value;
                } else {
                    $a2[] = $key.':'.$value;
                }
            }
            if ($return_comma_list) {
                $s = implode(',', $a2);
            } elseif ($assoc) {
                $s = '{'.implode(',', $a2).'}';
            } else {
                $s = '['.implode(',', $a2).']';
            }
        } elseif (is_string($arg)) {
            $s = "'".$this->_safe_js_string($arg)."'";
        } elseif (is_bool($arg)) {
            $s = json_encode($arg);
        } elseif (is_numeric($arg)) {
            $s = json_encode($arg);
        } elseif (is_null($arg)) {
            $s = json_encode($arg);
        } else {
            throw $this->exception('Unable to encode value for jQuery Chain - unknown type')
                ->addMoreInfo('arg', $arg);
        }

        return $s;
    }
    /**
     * Prevents calling univ() multiple times.
     *
     * Useful for backwards compatibility and in case of human mistake
     *
     * @return this
     */
    public function univ()
    {
        if ($this->univ_called) {
            return $this;
        }
        $this->univ_called = true;

        return $this->_fn('univ');
    }
    /**
     * Calls real redirect (from univ), but accepts page name.
     *
     * Use url() for 1st argument manually anyway.
     *
     * @param string $page Page name
     * @param array  $arg  Arguments
     *
     * @return this
     */
    public function redirect($page = null, $arg = null)
    {
        $url = $this->app->url($page, $arg);

        return $this->univ()->_fn('redirect', array($url));
    }
    /**
     * Reload object.
     *
     * You can bind this to custom event and trigger it if object is not
     * directly accessible.
     * If interval is given, then object will periodically reload itself.
     *
     * @param array        $arg
     * @param jQuery_Chain $fn
     * @param string       $url
     * @param int          $interval Interval in milisec. how often to reload object
     *
     * @return this
     */
    public function reload($arg = array(), $fn = null, $url = null, $interval = null)
    {
        if ($fn && $fn instanceof self) {
            $fn->_enclose();
        }
        $obj = $this->owner;
        if (!$url) {
            $url = $this->app->url(null, array('cut_object' => $obj->name));
        }

        return $this->univ()->_fn('reload', array($url, $arg, $fn, $interval));
    }
    /* Chain will not be called but will return callable function instead. */
    public function _enclose($fn = null, $preventDefault = false)
    {
        // builds structure $('obj').$fn(function(){ $('obj').XX; });
        if ($fn === null) {
            $fn = true;
        }
        $this->enclose = $fn;
        $this->preventDefault = $preventDefault;

        return $this;
    }
    public function _render()
    {
        $ret = $this->prepend;
        if ($this->library) {
            $ret .= $this->library;
        } else {
            if ($this->str) {
                $ret .= "$('#".$this->owner->getJSID()."')";
            }
        }
        $ret .= $this->str;

        if ($this->enclose === true) {
            if ($this->preventDefault) {
                $ret = 'function(ev,ui){ev.preventDefault();ev.stopPropagation(); '.$ret.'}';
            } else {
                $ret = 'function(ev,ui){'.$ret.'}';
            }
        } elseif ($this->enclose) {
            $ret = ($this->library ?: "$('#".$this->owner->getJSID()."')").
                    ".bind('".$this->enclose."',function(ev,ui){ev.preventDefault();ev.stopPropagation(); ".$ret.'})';
        }

        if (@$this->debug) {
            echo "<font color='blue'>".htmlspecialchars($ret).';</font><br/>';
            $this->debug = false;
        }

        return $ret;
    }
    /* Returns HTML for a link with text $text. When clicked will execute this chain. */
    public function getLink($text)
    {
        return '<a href="javascript:void(0)" onclick="'.$this->getString().'">'.$text.'</a>';
    }
    public function getString()
    {
        return $this->_render();
    }
    /* Specify requirement for stylesheet. Will load dynamically. */
    public function _css($file)
    {
        $this->app->jquery->addStylesheet($file);

        return $this;
    }
    /* Specify requirement for extra javascript include. Will load dynamically. */
    public function _load($file)
    {
        $this->app->jquery->addInclude($file);

        return $this;
    }
}
