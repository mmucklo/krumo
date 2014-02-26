<?php
/**
* Krumo: Structured information display solution
*
* Krumo is a debugging tool (PHP5 only), which displays structured information
* about any PHP variable. It is a nice replacement for print_r() or var_dump()
* which are used by a lot of PHP developers.
*
* @original author Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
* @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License Version 2.1
*
* https://github.com/oodle/krumo
*/

//////////////////////////////////////////////////////////////////////////////

/**
* Set the KRUMO_DIR constant up with the absolute path to Krumo files. If it is
* not defined, include_path will be used. Set KRUMO_DIR only if any other module
* or application has not already set it up.
*/
if (!defined('KRUMO_DIR')) {
    define('KRUMO_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}

if (!defined('KRUMO_RETURN')) {
    define('KRUMO_RETURN','158bafa5-b505-4661-9904-46504e00a5bb');
}

if (!defined('KRUMO_EXPAND_ALL')) {
    define('KRUMO_EXPAND_ALL','381019f0-fe97-4012-bb58-19f0e479665a');
}

//////////////////////////////////////////////////////////////////////////////

/**
* Krumo API
*
* This class stores the Krumo API for rendering and
* displaying the structured information it is reporting
*
* @package Krumo
*/
class Krumo {

    /**
     * Return Krumo version
     *
     * @return string
     * @access public
     * @static
     */
    public static function version()
    {
        return '0.5.0';
    }

    /**
     * Prints a debug backtrace
     *
     * @access public
     * @static
     */
    public static function backtrace()
    {
        // disabled ?
        if (!self::_debug()) {
            return false;
        }

        // render it
        return Krumo::dump(debug_backtrace());
    }
    /**
     * Prints a list of all currently declared classes.
     *
     * @access public
     * @static
     */
    public static function classes()
    {
        // disabled ?
        if (!Krumo::_debug()) {
            return false;
        }

        print "<div class=\"krumo-title\">\n";
        print "This is a list of all currently declared classes.";
        print "</div>\n";

        return Krumo::dump(get_declared_classes());
    }

    /**
     * Prints a list of all currently declared interfaces (PHP5 only).
     *
     * @access public
     * @static
     */
    public static function interfaces()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all currently declared interfaces.</div>";

        return Krumo::dump(get_declared_interfaces());
    }

    /**
     * Prints a list of all currently included (or required) files.
     *
     * @access public
     * @static
     */
    public static function includes()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all currently included (or required) files.</div>";

        return Krumo::dump(get_included_files());
    }

    /**
     * Prints a list of all currently declared functions.
     *
     * @access public
     * @static
     */
    public static function functions()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all currently declared functions.</div>";

        return Krumo::dump(get_defined_functions());
    }

    /**
     * Prints a list of all currently declared constants.
     *
     * @access public
     * @static
     */
    public static function defines()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all currently declared constants (defines).</div>";

        return Krumo::dump(get_defined_constants());
    }

    /**
     * Prints a list of all currently loaded PHP extensions.
     *
     * @access public
     * @static
     */
    public static function extensions()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all currently loaded PHP extensions.</div>";

        return Krumo::dump(get_loaded_extensions());
    }

    /**
     * Prints a list of all HTTP request headers.
     *
     * @access public
     * @static
     */
    public static function headers()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all HTTP request headers.</div>";

        return Krumo::dump(getAllHeaders());
    }

    /**
     * Prints a list of the configuration settings read from <i>php.ini</i>
     *
     * @access public
     * @static
     */
    public static function phpini()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        if (!is_readable(get_cfg_var('cfg_file_path'))) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">";
        print "This is a list of the configuration settings read from <code><b>" . get_cfg_var('cfg_file_path') . "</b></code>.";
        print "</div>";

        return Krumo::dump(parse_ini_file(get_cfg_var('cfg_file_path'), true));
    }

    /**
     * Prints a list of all your configuration settings.
     *
     * @access public
     * @static
     */
    public static function conf()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all your configuration settings.</div>";

        return Krumo::dump(ini_get_all());
    }

    /**
     * Prints a list of the specified directories under your <i>include_path</i> option.
     *
     * @access public
     * @static
     */
    public static function path()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\"> This is a list of the specified directories under your <code><b>include_path</b></code> option.</div>";

        return Krumo::dump(explode(PATH_SEPARATOR, ini_get('include_path')));
    }

    /**
     * Prints a list of all the values from the <i>$_REQUEST</i> array.
     *
     * @access public
     * @static
     */
    public static function request()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_REQUEST</b></code> array.</div>";

        return Krumo::dump($_REQUEST);
    }

    /**
     * Prints a list of all the values from the <i>$_GET</i> array.
     *
     * @access public
     * @static
     */
    public static function get()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_GET</b></code> array.</div>";

        return Krumo::dump($_GET);
    }

    /**
     * Prints a list of all the values from the <i>$_POST</i> array.
     *
     * @access public
     * @static
     */
    public static function post()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_POST</b></code> array.</div>";

        return Krumo::dump($_POST);
    }

    /**
     * Prints a list of all the values from the <i>$_SERVER</i> array.
     *
     * @access public
     * @static
     */
    public static function server()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_SERVER</b></code> array.</div>";

        return Krumo::dump($_SERVER);
    }

    /**
     * Prints a list of all the values from the <i>$_COOKIE</i> array.
     *
     * @access public
     * @static
     */
    public static function cookie()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_COOKIE</b></code> array.</div>";

        return Krumo::dump($_COOKIE);
    }

    /**
     * Prints a list of all the values from the <i>$_ENV</i> array.
     *
     * @access public
     * @static
     */
    public static function env()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_ENV</b></code> array.</div>";

        return Krumo::dump($_ENV);
    }

    /**
     * Prints a list of all the values from the <i>$_SESSION</i> array.
     *
     * @access public
     * @static
     */
    public static function session()
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // render it
        print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_SESSION</b></code> array.</div>";

        return Krumo::dump($_SESSION);
    }

    /**
     * Prints a list of all the values from an INI file.
     *
     * @param string $ini_file
     *
     * @access public
     * @static
     */
    public static function ini($ini_file)
    {
        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // read it
        if (!$_ = @parse_ini_file($ini_file, 1)) {
            return false;
        }

        // render it
        if (realpath($ini_file)) {
            $ini_file = realpath($ini_file);
        }

        print "<div class=\"krumo-title\">";
        print "This is a list of all the values from the <code><b>" . $ini_file . "</b></code> INI file.</div>";

        return Krumo::dump($_);
    }


    /**
     * Dump information about a variable
     *
     * @param mixed $data,...
     * @access public
     * @static
     */
    public static function dump($data, $second = '')
    {
        if (Krumo::is_cli()) {
            print_r($data);
            exit;
        }

        // If we're capturing call dump() with just data and capture the output
        if ($second === KRUMO_RETURN) {
            ob_start();

            Krumo::dump($data);

            $str = ob_get_clean();

            return $str;
        // If we were given expand all, set the global variable
        } elseif ($second === KRUMO_EXPAND_ALL) {
            self::$expand_all = true;
            Krumo::dump($data);

            return true;
        }

        $clearObjectRecursionProtection   = false;
        if (self::$objectRecursionProtection === NULL) {
            self::$objectRecursionProtection = array();
            $clearObjectRecursionProtection  = true;
        }

        // disabled
        if (!Krumo::_debug()) {
            return false;
        }

        // more arguments
        if (func_num_args() > 1) {
            $_ = func_get_args();
            foreach ($_ as $d) {
                Krumo::dump($d);
            }

            return;
        }

        // find caller
        $_ = debug_backtrace();
        while ($d = array_pop($_)) {
            $callback = self::$lineNumberTestCallback;
            $function = strToLower($d['function']);
            if (in_array($function, array("krumo","k","kd")) || (strToLower(@$d['class']) == 'krumo') || (is_callable($callback) && $callback($d))) {
                break;
            }
        }

        $showVersion  = Krumo::_config('display', 'show_version', TRUE);
        $showCallInfo = Krumo::_config('display', 'show_call_info', TRUE);
        $krumoUrl     = 'https://github.com/oodle/krumo';

        //////////////////////
        // Start HTML header//
        //////////////////////
        print "<div class=\"krumo-root\">\n";
        print "\t<ul class=\"krumo-node krumo-first\">\n";

        // The actual item itself
        print Krumo::_dump($data);

        if ($showVersion || $showCallInfo) {
            print "\t\t<li class=\"krumo-footnote\" onDblClick=\"toggle_expand_all();\">\n";

            if ($showCallInfo && isset($d['file']) && $d['file']) {
                print "<span class=\"krumo-call\" style=\"white-space:nowrap;\">";
                print "Called from <strong><code>" . $d['file'] . "</code></strong>, ";
                print "line <strong><code>" . $d['line'] . "</code></strong></span>";
            }

            if ($showVersion) {
                $version = krumo::version();
                print "<span class=\"krumo-version\" style=\"white-space:nowrap;\">\n";
                print "<strong class=\"krumo-version-number\">Krumo version $version</strong> | <a href=\"$krumoUrl\" target=\"_blank\">$krumoUrl</a>\n";
                print "</span>\n";
            }

            print "</li>";
        }

        print "</ul></div>\n";
        print "<!-- Krumo - HTML -->\n\n";

        // Output the CSS and JavaScript AFTER the HTML
        krumo::_css();
        ////////////////////
        // End HTML header//
        ////////////////////

        // flee the hive
        $_recursion_marker = Krumo::_marker();
        if ($hive =& Krumo::_hive($dummy)) {
            foreach ($hive as $i => $bee) {
                if (is_object($bee)) {
                    if (($hash = spl_object_hash($bee)) && isset(self::$objectRecursionProtection[$hash])) {
                        unset(self::$objectRecursionProtection[$hash]);
                    }
                } elseif (isset($hive[$i]->$_recursion_marker)) {
                    unset($hive[$i][$_recursion_marker]);
                }
            }
        }

        if ($clearObjectRecursionProtection) {
            self::$objectRecursionProtection = NULL;
        }

    // End of dump()
    }


    /**
      * Configuration array.
      */
    private static $_config = array();

    /**
     * Returns values from Krumo's configuration
     *
     * @param string $group
     * @param string $name
     * @param mixed $fallback
     * @return mixed
     *
     * @access private
     * @static
     */
    private static function _config($group, $name, $fallback=null)
    {
        $krumo_ini = KRUMO_DIR . 'krumo.ini';

        // The config isn't loaded yet
        if (empty(self::$_config) && is_readable($krumo_ini)) {
            self::$_config = (array) parse_ini_file($krumo_ini, true);
        }

        // exists
        if (isset(self::$_config[$group][$name])) {
            return self::$_config[$group][$name];
        } else {
            return $fallback;
        }
    }

    public static function setConfig($config)
    {
        self::$_config = $config;
    }

    public static function setLineNumberTestCallback($callback)
    {
        self::$lineNumberTestCallback = $callback;
    }

    private static $lineNumberTestCallback = null;


    /**
     * Cascade configuration array
     *
     * By default, all nodes are collapsed.
     */
    private static $_cascade = null;

    /**
     * Set a cascade configuration array.
     *
     * Each value in the array is the maximum number of entries that node can
     * have before it is being collapsed. The last value is repeated for all
     * further levels.
     *
     * Example:
     * array(10,5,0) - Nodes from the first level are expanded if they have less
     *                 than or equal to 10 child nodes. Nodes from the second level are ex-
     *                 panded if they have less or equal to 5 nodes and all lower levels
     *                 are collapsed.
     *
     * Note:
     *   To reset, simply call this function with no arguments.
     *
     * @param array $cascade Cascading information
     * @access public
     * @static
     */
    public static function cascade(array $cascade = null)
    {
        self::$_cascade = $cascade;
    }

    /**
     * This allows you to uncollapse items programattically. Example:
     *
     * Krumo::$expand_all = 1;
     * krumo($my_array);
     */
    public static $expand_all = 0;

    /**
     * Determines if a given node will be collapsed or not.
     */
    private static function _isCollapsed($level, $childCount)
    {
        if (self::$expand_all) { return false; }

        $cascade = self::$_cascade;

        if ($cascade == null) {
            $cascade = Krumo::_config('display', 'cascade', array());
        }

        if (isset($cascade[$level])) {
            return $childCount >= $cascade[$level];
        } else {
            return true;
        }
    }


    /**
     * Calculate the relative path of a given absolute URL
     *
     * @return string
     * @access public
     * @static
     */
    public static function calculate_relative_path($file, $return_dir = 0)
    {
        // We find the document root of the webserver
        $doc_root = $_SERVER['DOCUMENT_ROOT'];

        // Remove the document root, from the FULL absolute path of the
        // file we're looking for
        $ret = "/" . str_replace($doc_root,"",$file,$ok);
        if (!$ok) { return false; }

        // If they want the path to the dir, only return the dir part
        if ($return_dir) { $ret = dirname($ret) . "/"; }

        $ret = preg_replace("|//|","/",$ret);

        return $ret;
    }

    /**
     * Print the skin (CSS)
     *
     * @return boolean
     * @access private
     * @static
     */
    private static function _css()
    {
        static $_css = false;

        // already set ?
        if ($_css) {
            return true;
        }

        $css = '';
        $skin = Krumo::_config('skin', 'selected', 'stylish');

        // custom selected skin
        $rel_css_file = "skins/{$skin}/skin.css";
        $css_file = KRUMO_DIR . $rel_css_file;
        if (is_readable($css_file)) {
            $css = join(file($css_file));
        }

        // default skin
        if (!$css && ($skin != 'default')) {
            $skin         = 'stylish';
            $rel_css_file = "skins/$skin/skin.css";
            $css_file     = KRUMO_DIR . $rel_css_file;
            $css          = join(file($css_file));
        }

        // print
        if ($_css = $css != '') {
            // See if there is a CSS path in the config
            $relative_krumo_path = Krumo::calculate_relative_path(__FILE__,true);
            $css_url = Krumo::_config('css', 'url', $relative_krumo_path);

            // Default to /krumo/ if nothing is found in the config
            $css_url || $css_url = "/krumo/";
            $css_url = rtrim($css_url, '/');

            // fix the urls
            $css_url = "$css_url/skins/{$skin}/";
            $css     = preg_replace('~%url%~Uis', $css_url, $css);

            // the CSS
            print "<!-- Using Krumo Skin: \"$skin\" $rel_css_file -->\n";
            print "<style type=\"text/css\">\n";
            print trim($css) . "\n";
            print "</style>\n";
            print "<!-- Krumo - CSS -->\n";

            // the JS
            print "<script type=\"text/javascript\">\n";

            $js_file = KRUMO_DIR . "/js/krumo.min.js";
            if (is_readable($js_file)) {
                $js_text = join(file($js_file));
            } else {
                $js_text = "// Missing JS file krumo.min.js\n";
            }

            print "$js_text</script>\n";
            print "<!-- Krumo - JavaScript -->\n";
        }

        return $_css;
    }


    /**
     * Enable Krumo
     *
     * @return boolean
     * @access public
     * @static
     */
    public static function enable()
    {
        return true === Krumo::_debug(true);
    }

    /**
     * Disable Krumo
     *
     * @return boolean
     * @access public
     * @static
     */
    public static function disable()
    {
        return false === Krumo::_debug(false);
    }

    /**
     * Get\Set Krumo state: whether it is enabled or disabled
     *
     * @param boolean $state
     * @return boolean
     * @access private
     * @static
     */
    private static function _debug($state = null)
    {
        static $_ = true;

        // set
        if (isset($state)) {
            $_ = (boolean) $state;
        }

        // get
        return $_;
    }

    private static function sanitize_name($name)
    {
        // Check if the key has whitespace in it, if so show it and add an icon explanation
        $has_white_space = preg_match("/\s/",$name);
        if ($has_white_space) {
            // Convert the white space to unicode underbars to visualize it
            $name  = preg_replace("/\s/","&#9251;",$name);
            $title = "Note: Key contains white space";
            $icon  = Krumo::get_icon("information",$title);

            $ret = $name . $icon;
        } else {
            $ret = $name;
        }

        return $ret;
    }


    /**
     * Dump information about a variable
     *
     * @param mixed $data
     * @param string $name
     * @access private
     * @static
     */
    private static function _dump(&$data, $name = '…')
    {
        // Highlight elements that have a space in their name.
        // Spaces are hard to see in the HTML and are hard to troubleshoot
        $name = Krumo::sanitize_name($name);

        // object
        if (is_object($data)) {
            return Krumo::_object($data, $name);
        }

        // array
        if (is_array($data)) {
            return Krumo::_array($data, $name);
        }

        // resource
        if (is_resource($data)) {
            return Krumo::_resource($data, $name);
        }

        // scalar
        if (is_string($data)) {
            return Krumo::_string($data, $name);
        }

        // float
        if (is_float($data)) {
            return Krumo::_float($data, $name);
        }

        // integer
        if (is_integer($data)) {
            return Krumo::_integer($data, $name);
        }

        // boolean
        if (is_bool($data)) {
            return Krumo::_boolean($data, $name);
        }

        // null
        if (is_null($data)) {
            return Krumo::_null($name);
        }
    }


    /**
     * Render a dump for a NULL value
     *
     * @param string $name
     * @return string
     * @access private
     * @static
     */
    private static function _null($name)
    {

        $html = '<li class="krumo-child">
            <div class="krumo-element" onMouseOver="krumo.over(this);" onMouseOut="krumo.out(this);">
            <a class="krumo-name">%s</a> %s <em class="krumo-type krumo-null">NULL</em>
            </div></li>';

        $html = sprintf($html, $name, Krumo::get_separator() );

        echo $html;

    }


    /**
     * Return the marked used to stain arrays
     * and objects in order to detect recursions
     *
     * @return string
     * @access private
     * @static
     */
    private static function _marker()
    {
        static $_recursion_marker;
        if (!isset($_recursion_marker)) {
            $_recursion_marker = uniqid('krumo');
        }

        return $_recursion_marker;
    }


    /**
     * Adds a variable to the hive of arrays and objects which
     * are tracked for whether they have recursive entries
     *
     * @param mixed &$bee either array or object, not a scalar value
     * @return array all the bees
     *
     * @access private
     * @static
     */
    private static $objectRecursionProtection = NULL;
    private static function &_hive(&$bee) {

        static $_ = array();

        // new bee
        if (!is_null($bee)) {

            // stain it
            $_recursion_marker = Krumo::_marker();
            if (is_object($bee)) {
                $hash = spl_object_hash($bee);
                if ($hash && isset(self::$objectRecursionProtection[$hash])) {
                    self::$objectRecursionProtection[$hash]++;
                } elseif ($hash) {
                    self::$objectRecursionProtection[$hash] = 1;
                }
            } else {
                if (isset($bee[$_recursion_marker])) {
                    $bee[$_recursion_marker]++;
                } else {
                    $bee[$_recursion_marker] = 1;
                }
            }

            $_[0][] =& $bee;
        }

        // return all bees
        return $_[0];
    }


    /**
     * Level of recursion.
     */
    private static $_level = 0;

    /**
     * Render a dump for the properties of an array or objeect
     *
     * @param mixed &$data
     * @access private
     * @static
     */
    private static function _vars(&$data)
    {
        $_is_object = is_object($data);

        // test for references in order to
        // prevent endless recursion loops
        $_recursion_marker = Krumo::_marker();

        if ($_is_object) {
            if (($hash = spl_object_hash($data)) && isset(self::$objectRecursionProtection[$hash])) {
                $_r = self::$objectRecursionProtection[$hash];
            } else {
                $_r = NULL;
            }
        } else {
            $_r = isset($data[$_recursion_marker]) ? $data[$_recursion_marker] : null;
        }

        // recursion detected
        if ($_r > 0) {
            return Krumo::_recursion();
        }

        // stain it
        Krumo::_hive($data);

        // render it
        $collapsed = Krumo::_isCollapsed(self::$_level, count($data)-1);
        if ($collapsed) {
            $collapse_style = 'style="display: none;"';
        } else {
            $collapse_style = '';
        }

        print "<div class=\"krumo-nest\" $collapse_style>";
        print "<ul class=\"krumo-node\">";

        // we're descending one level deeper
        self::$_level++;

        // Object?? - use Reflection
        if ($_is_object) {
            $reflection = new ReflectionObject($data);
            $properties = $reflection->getProperties();

            foreach ($properties as $property) {
                $prefix = null;
                $setAccessible = false;

                if ($property->isPrivate()) {
                    $setAccessible = true;
                    $prefix = 'private';
                } elseif ($property->isProtected()) {
                    $setAccessible = true;
                    $prefix = 'protected';
                } elseif ($property->isPublic()) {
                    $prefix = 'public';
                }

                $name = $property->getName();
                if ($setAccessible) {
                    $property->setAccessible(true);
                }

                $value = $property->getValue($data);

                Krumo::_dump($value, "<span>$prefix</span>&nbsp;$name");
                if ($setAccessible) {
                    $property->setAccessible(false);
                }
            }
        } else {
            // keys
            $keys = array_keys($data);

            // iterate
            foreach ($keys as $k) {
                // skip marker
                if ($k === $_recursion_marker) {
                    continue;
                }

                // get real value
                $v =& $data[$k];

                Krumo::_dump($v,$k);
            }
        }

        print "</ul>\n</div>";
        self::$_level--;
    }


    /**
     * Render a block that detected recursion
     *
     * @access private
     * @static
     */
    private static function _recursion()
    {

        $html = '<div class="krumo-nest" style="display:none;">
            <ul class="krumo-node">
                <li class="krumo-child">
                    <div class="krumo-element" onMouseOver="krumo.over(this);" onMouseOut="krumo.out(this);">
                        <a class="krumo-name">&#8734;</a>
                        (<em class="krumo-type">Recursion</em>)
                    </div>

                </li>
            </ul>';

        echo $html;
    }

    private static function is_assoc($var)
    {
        return is_array($var) && array_diff_key($var,array_keys(array_keys($var)));
    }


    /**
     * Render a dump for an array
     *
     * @param mixed $data
     * @param string $name
     * @access private
     * @static
     */
    private static function _array($data, $name)
    {
        $config_sort = Krumo::_config('sorting','sort_arrays',true);

        // If the sort is enabled in the config (default = yes) and the array is assoc (non-numeric)
        if (sizeof($data) > 1 && $config_sort && Krumo::is_assoc($data)) {
            // Copy the array to a temp variable and sort it
            $new = $data;
            ksort($new);

            // If the sorted array is the same as the old don't sort it
            if ($new === $data) {
                $sort = 0;
            } else {
                $data = $new;
                $sort = 1;
            }
        } else {
            $sort = 0;
        }

        $childCount = count($data);
        $collapsed = Krumo::_isCollapsed(self::$_level, count($data));

        // Setup the CSS classes depending on how many children there are
        if ($childCount > 0 && $collapsed) {
            $elementClasses = ' krumo-expand';
        } elseif ($childCount > 0) {
            $elementClasses = ' krumo-expand krumo-opened';
        } else {
            $elementClasses = '';
        }

        if (count($data) > 1 || count($data) == 0) {
            $plural = 's';
        } else {
            $plural = '';
        }

        print "<li class=\"krumo-child\">";
        print "<div class=\"krumo-element $elementClasses\"";

        // If there is more than one, make a dropdown
        if (count($data) > 0) {
            print "onClick=\"krumo.toggle(this);\"";
        }

        print "onMouseOver=\"krumo.over(this);\" onMouseOut=\"krumo.out(this);\">";
        print "<a class=\"krumo-name\">$name</a> <em class=\"krumo-type\">Array(<strong class=\"krumo-array-length\">";
        print count($data) . "</strong>)</em>";

        if (count($data)>0) {
            print " &hellip;";
        }

        if ($sort) {
            $title = "Array has been sorted prior to display. This is configurable in krumo.ini.";
            print " - <span title=\"$title\"><strong class=\"krumo-sorted\">Sorted</strong></span>";
        }

        // callback
        if (is_callable($data)) {
            $_ = array_values($data);
            print "<span class=\"krumo-callback\"> |";
            print " (<em class=\"krumo-type\">Callback</em>) <strong class=\"krumo-string\">";

            if (!is_object($_[0])) {
                    echo htmlSpecialChars($_[0]);
            } else {
                    echo htmlSpecialChars(get_class($_[0])) . "::";
            }

            echo htmlSpecialChars($_[1]) . "()</strong></span>";
        }

        print "</div>";

        if (count($data)) {
            Krumo::_vars($data);
        }

        print "</li>";
    }


    /**
     * Render a dump for an object
     *
     * @param mixed $data
     * @param string $name
     * @access private
     * @static
     */
    private static function _object(&$data, $name)
    {
        $reflection = new ReflectionObject($data);
        $properties = $reflection->getProperties();

        $childCount = count($properties);
        $collapsed = Krumo::_isCollapsed(self::$_level, $childCount);

        // Setup the CSS classes depending on how many children there are
        if ($childCount > 0 && $collapsed) {
            $elementClasses = ' krumo-expand';
        } elseif ($childCount > 0) {
            $elementClasses = ' krumo-expand krumo-opened';
        } else {
            $elementClasses = '';
        }

        print "<li class=\"krumo-child\"> <div class=\"krumo-element $elementClasses\"";
        if (count($data) > 0) {
            print 'onClick="krumo.toggle(this);"';
        }
        print 'onMouseOver="krumo.over(this);" onMouseOut="krumo.out(this);">';

        $empty_str = '';
        if ($childCount == 0) { $empty_str = ' (empty)'; }

        print "<a class=\"krumo-name\">$name</a> <em class=\"krumo-type\">Object</em> ";
        print Krumo::get_separator() . " <strong class=\"krumo-class\">" . get_class($data) . "</strong>$empty_str</div>";

        if ($properties) {
            Krumo::_vars($data);
        }

        print "</li>";
    }


    /**
     * Render a dump for a resource
     *
     * @param mixed $data
     * @param string $name
     * @access private
     * @static
     */
    private static function _resource($data, $name)
    {

        $html = '<li class="krumo-child">
            <div class="krumo-element" onMouseOver="krumo.over(this);" onMouseOut="krumo.out(this);">
            <a class="krumo-name">%s</a> <em class="krumo-type">Resource</em>
            %s<strong class="krumo-resource">%s</strong>
            </div></li>';

        $html = sprintf($html, $name, Krumo::get_separator(), get_resource_type($data));

        echo $html;

    }


    /**
     * Render a dump for a boolean value
     *
     * @param mixed $data
     * @param string $name
     * @access private
     * @static
     */
    private static function _boolean($data, $name)
    {
        if ($data == false) {
            $value = "FALSE";
        } elseif ($data == true) {
            $value = "TRUE";
        }

        $html = '<li class="krumo-child">
            <div class="krumo-element" onMouseOver="krumo.over(this);" onMouseOut="krumo.out(this);">
            <a class="krumo-name">%s</a> <em class="krumo-type">Boolean</em>
            %s<strong class="krumo-boolean">%s</strong>
            </div></li>';

        $html = sprintf($html, $name, Krumo::get_separator(), $value);

        echo $html;

    }


    /**
     * Render a dump for a integer value
     *
     * @param mixed $data
     * @param string $name
     * @access private
     * @static
     */
    private static function _integer($data, $name)
    {
        print "<li class=\"krumo-child\">";
        print "<div class=\"krumo-element\" onMouseOver=\"krumo.over(this);\" onMouseOut=\"krumo.out(this);\">";
        print "<a class=\"krumo-name\">$name</a> <em class=\"krumo-type\">Integer</em> ";
        print Krumo::get_separator() . " <strong class=\"krumo-integer\">$data</strong>";

        $ut = Krumo::is_datetime($name, $data);
        if ($ut) {
            print " ~ <strong class=\"krumo-datetime\">$ut</strong>";
        }

        print "</div></li>";
    }


    /**
     * Render a dump for a float value
     *
     * @param mixed $data
     * @param string $name
     * @access private
     * @static
     */
    private static function _float($data, $name)
    {
        print "<li class=\"krumo-child\">";
        print "<div class=\"krumo-element\" onMouseOver=\"krumo.over(this);\" onMouseOut=\"krumo.out(this);\">";
        print "<a class=\"krumo-name\">$name</a> <em class=\"krumo-type\">Float</em> ";
        print Krumo::get_separator() . " <strong class=\"krumo-float\">$data</strong>";

        $ut = Krumo::is_datetime($name,$data);
        if ($ut) {
            print " ~ <strong class=\"krumo-datetime\">$ut</strong>";
        }

        print "</div></li>";
    }

    public static function get_icon($name,$title)
    {
        $path = dirname(__FILE__) . "/icons/$name.png";
        $rel  = Krumo::calculate_relative_path($path);

        $ret = "<img style=\"padding: 0 2px 0 2px\" src=\"$rel\" title=\"$title\" alt=\"name\" />";

        return $ret;
    }

    /**
      * Get the separator to use for separating 'key' / 'value' pairs. Defaults to ' => '
      *
      * @return string
      */
    public static function get_separator()
    {
        $separator = Krumo::_config('display', 'separator', " =&gt; ");

        return $separator;
    }

    private static function is_datetime($name,$value)
    {
        // If the name contains date or time, and the value looks like a unixtime
        if (preg_match("/date|time/i",$name) && ($value > 10000000 && $value < 4000000000)) {
            $ret = date("r",$value);

            return $ret;
        }

        return false;
    }


    /**
     * Render a dump for a string value
     *
     * @param mixed $data
     * @param string $name
     * @access private
     * @static
     */
    private static function _string($data, $name)
    {
        $collapsed = Krumo::_isCollapsed(self::$_level, 1);

        if ($collapsed) {
            $collapse_style = 'style="display: none;"';
        } else {
            $collapse_style = '';
        }

        // extra
        $_extra = false;
        $_ = $data;

        // Get the truncate length from the config, or default to 100
        $truncate_length = Krumo::_config('display', 'truncate_length', 100);
        $display_cr      = Krumo::_config('display', 'carriage_returns', true);

        if (strLen($data) > $truncate_length ) {
            $_ = substr($data, 0, $truncate_length - 1);
            $_extra = true;
        }

        $_ = htmlentities($_);

        if ($display_cr) {
            $_ = preg_replace("/\\n/","<strong class=\"krumo-carrage-return\"> &para; </strong>",$_);
        } else {
            $_ = nl2br($_);
        }

        $expand_class = '';
        if ($_extra) { $expand_class = 'krumo-expand'; }

        print "<li class=\"krumo-child\">";
        print "<div class=\"krumo-element $expand_class\" ";
        if ($_extra) {
            print " onClick=\"krumo.toggle(this);\" ";
        }
        print "onMouseOver=\"krumo.over(this);\" onMouseOut=\"krumo.out(this);\">\n";

        print "<a class=\"krumo-name\">$name</a> ";
        print "<em class=\"krumo-type\">String(<strong class=\"krumo-string-length\">" . strlen($data) . "</strong>)</em> ";
        print Krumo::get_separator() . " <strong class=\"krumo-string\">" . $_;
        // This has to go AFTER the htmlspecialchars
        if ($_extra) {
            print "&hellip;";
        }
        print "</strong>";

        $ut = Krumo::is_datetime($name, $data);
        if ($ut) {
            print " ~ <strong class=\"krumo-datetime\">$ut</strong>";
        }

        // callback
        if (is_callable($data)) {
            print "<span class=\"krumo-callback\"> | ";
            print "(<em class=\"krumo-type\">Callback</em>) <strong class=\"krumo-string\">" . htmlSpecialChars($_) . "()</strong></span>";
        }

        print "</div>";

        if ($_extra) {
            $data = htmlentities($data);

            if ($display_cr) {
                $data = preg_replace("/\\n/","<strong class=\"krumo-carrage-return\"> &para; </strong>",$data);
            } else {
                $data = nl2br($data);
            }

            print "<div class=\"krumo-nest\" $collapse_style>";
            print "<ul class=\"krumo-node\">";
            print "<li class=\"krumo-child\"> <div class=\"krumo-preview\">" . $data . "</div></li>";
            print "</ul></div>";
        }

        print "</li>";
    }

    /**
     * Detect if we're running in CLI mode`
     */
    private static function is_cli()
    {
        if(php_sapi_name() === "cli") {
            return true;
        } else {
            return false;
        }
    }

}


/**
* Alias of {@link Krumo::dump()}
*
* @param mixed $data,...
*
* @see Krumo::dump()
*/
function krumo()
{
    $_ = func_get_args();

    return call_user_func_array(array('krumo', 'dump'), $_);
}

function k()
{
    $_ = func_get_args();

    return call_user_func_array(array('krumo', 'dump'), $_);
}

function kd()
{
    $_ = func_get_args();
    call_user_func_array(array('krumo', 'dump'), $_);

    exit();
}

// vim: tabstop=4 shiftwidth=4 expandtab autoindent
