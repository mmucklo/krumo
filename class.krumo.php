<?php
/**
* Krumo: Structured information display solution
*
* Krumo is a debugging tool (PHP5 only), which displays structured information
* about any PHP variable. It is a nice replacement for print_r() or var_dump()
* which are used by a lot of PHP developers.
*
* @original author Kaloyan K. Tsvetkov <kaloyan@kaloyan.info>
* @author Matthew J. Mucklo <mmucklo@gmail.com>
* @author Scott Baker <scott@perturb.org>
* @author various others - see composer.json, and contributors tab on github.com
* @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License Version 2.1
*
* https://github.com/mmucklo/krumo
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
    define('KRUMO_RETURN', '158bafa5-b505-4661-9904-46504e00a5bb');
}

if (!defined('KRUMO_EXPAND_ALL')) {
    define('KRUMO_EXPAND_ALL', '381019f0-fe97-4012-bb58-19f0e479665a');
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
class Krumo
{
    /**
     * Return Krumo version
     *
     * @return string
     * @access public
     * @static
     */
    public static function version()
    {
        return trim(file_get_contents(__DIR__ . "/VERSION"));
    }

    protected static function getCharset()
    {
        return self::_config('display', 'default_charset', 'UTF-8');
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        return static::dump(debug_backtrace());
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
        if (!static::_debug()) {
            return false;
        }

        static::heading("This is a list of all currently declared classes.");

        return static::dump(get_declared_classes());
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all currently declared interfaces.");

        return static::dump(get_declared_interfaces());
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all currently included (or required) files.");

        return static::dump(get_included_files());
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all currently declared functions.");

        return static::dump(get_defined_functions());
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all currently declared constants (defines).");

        return static::dump(get_defined_constants());
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all currently loaded PHP extensions.");

        return static::dump(get_loaded_extensions());
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all HTTP request headers.");

        return static::dump(getAllHeaders());
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
        if (!static::_debug()) {
            return false;
        }

        if (!is_readable(get_cfg_var('cfg_file_path'))) {
            return false;
        }

        // render it
        static::heading("This is a list of the configuration settings read from ", get_cfg_var('cfg_file_path'), ".");

        return static::dump(parse_ini_file(get_cfg_var('cfg_file_path'), true));
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all your configuration settings.");

        return static::dump(ini_get_all());
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of the specified directories under your ", "include_path", " option.");

        return static::dump(explode(PATH_SEPARATOR, ini_get('include_path')));
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all the values from the ", "\$_REQUEST", " array.");

        return static::dump($_REQUEST);
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all the values from the ", "\$_GET", " array.");

        return static::dump($_GET);
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all the values from the ", "\$_POST", " array.");

        return static::dump($_POST);
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all the values from the ", "\$_SERVER", " array.");

        return static::dump($_SERVER);
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all the values from the ", "\$_COOKIE", " array.");

        return static::dump($_COOKIE);
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all the values from the ", "\$_ENV", " array.");

        return static::dump($_ENV);
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
        if (!static::_debug()) {
            return false;
        }

        // render it
        static::heading("This is a list of all the values from the ", "\$_SESSION", " array.");

        return static::dump($_SESSION);
    }

    /**
     * Prints a list of all the values from an INI file.
     *
     * @param string $ini_file
     * @return bool
     * @access public
     * @static
     */
    public static function ini($ini_file)
    {
        // disabled
        if (!static::_debug()) {
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

        static::heading("This is a list of all the values from the ", $ini_file, "INI file");
        return static::dump($_);
    }


    protected static function heading($first, $code = '', $rest = '')
    {
        if (!static::isCli()) {
            print "<div class=\"krumo-title\">";
        }

        print $first;

        if ($code && !static::isCli()) {
            print "<code><b>";
        }

        print $code;

        if ($code && !static::isCli()) {
            print "</code></b>";
        }

        print $rest;

        if (!static::isCli()) {
            print "</div>";
        }
    }

    public static function htmlHeaders()
    {
        if (!headers_sent()) {
            header("Content-Type", "text/html");

            // Print out a minimal page header
            print "<!DOCTYPE html><html><head><meta charset=\"" . static::getCharset() . "\"></head><body>";
        }
    }

    /**
     * Dump information about a variable
     *
     * @param mixed $data,...
     * @access public
     * @static
     * @return bool
     */
    public static function dump($data, $second = '')
    {
        if (static::isCli()) {
            print_r($data);
            return true;
        }

        // If we're capturing call dump() with just data and capture the output
        if ($second === KRUMO_RETURN) {
            ob_start();

            static::dump($data);

            $str = ob_get_clean();

            return $str;
        // If we were given expand all, set the global variable
        } elseif ($second === KRUMO_EXPAND_ALL) {
            static::$expand_all = true;
            static::dump($data);

            return true;
        }

        $clearObjectRecursionProtection   = false;
        if (static::$objectRecursionProtection === null) {
            static::$objectRecursionProtection = array();
            $clearObjectRecursionProtection  = true;
        }

        // disabled
        if (!static::_debug()) {
            return false;
        }

        // more arguments
        if (func_num_args() > 1) {
            $_ = func_get_args();
            $result = true;
            foreach ($_ as $d) {
                $result = $result && static::dump($d);
            }

            return $result;
        }

        // find caller
        $_ = debug_backtrace();
        while ($d = array_pop($_)) {
            $callback = static::$lineNumberTestCallback;
            $function = strToLower($d['function']);
            if (in_array($function, array("krumo","k","kd")) || (strToLower(@$d['class']) == 'krumo') || (is_callable($callback) && call_user_func($callback, $d))) {
                break;
            }
        }

        $showVersion  = static::_config('display', 'show_version', true);
        $showCallInfo = static::_config('display', 'show_call_info', true);
        $krumoUrl     = 'https://github.com/mmucklo/krumo';

        //////////////////////
        // Start HTML header//
        //////////////////////
        print "<div class=\"krumo-root\">\n";
        print "\t<ul class=\"krumo-node krumo-first\">\n";

        // The actual item itself
        static::_dump($data);

        if ($showVersion || $showCallInfo) {
            print "\t\t<li class=\"krumo-footnote\" onDblClick=\"toggle_expand_all();\">\n";

            if ($showCallInfo && isset($d['file']) && $d['file']) {
                print "<span class=\"krumo-call\" style=\"white-space:nowrap;\">";
                print "Called from <strong><code>" . $d['file'] . "</code></strong>, ";
                print "line <strong><code>" . $d['line'] . "</code></strong></span>";
            }

            if ($showVersion) {
                $version = static::version();
                print "<span class=\"krumo-version\" style=\"white-space:nowrap;\">\n";
                print "<strong class=\"krumo-version-number\">Krumo version $version</strong> | <a href=\"$krumoUrl\" target=\"_blank\">$krumoUrl</a>\n";
                print "</span>\n";
            }

            print "</li>";
        }

        print "</ul></div>\n";
        print "<!-- Krumo - HTML -->\n\n";

        // Output the CSS and JavaScript AFTER the HTML
        static::_css();
        ////////////////////
        // End HTML header//
        ////////////////////

        // flee the hive
        $_recursion_marker = static::_marker();
        if ($hive =& static::_hive($dummy)) {
            foreach ($hive as $i => $bee) {
                if (is_object($bee)) {
                    if (($hash = spl_object_hash($bee)) && isset(static::$objectRecursionProtection[$hash])) {
                        unset(static::$objectRecursionProtection[$hash]);
                    }
                } elseif (isset($hive[$i]->$_recursion_marker)) {
                    unset($hive[$i][$_recursion_marker]);
                }
            }
        }

        if ($clearObjectRecursionProtection) {
            static::$objectRecursionProtection = null;
        }
        return true;
    } // End of dump()



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
        if (empty(static::$_config) && is_readable($krumo_ini)) {
            static::$_config = (array) parse_ini_file($krumo_ini, true);
        }

        // exists
        if (isset(static::$_config[$group][$name])) {
            return static::$_config[$group][$name];
        } else {
            return $fallback;
        }
    }

    public static function setConfig($config)
    {
        static::$_config = $config;
    }

    public static function setLineNumberTestCallback($callback)
    {
        static::$lineNumberTestCallback = $callback;
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
        static::$_cascade = $cascade;
    }

    /**
     * This allows you to uncollapse items programattically. Example:
     *
     * static::$expand_all = 1;
     * krumo($my_array);
     */
    public static $expand_all = 0;

    /**
     * Determines if a given node will be collapsed or not.
     * @param $level integer Which level we're at
     * @param $childCount integer How many children are at this level
     * @return bool
     */
    private static function _isCollapsed($level, $childCount)
    {
        if (static::$expand_all) {
            return false;
        }

        $cascade = static::$_cascade;

        if ($cascade == null) {
            $cascade = static::_config('display', 'cascade', array());
        }

        if (isset($cascade[$level])) {
            return $childCount >= $cascade[$level];
        }
        return true;
    }


    /**
     * Calculate the relative path of a given absolute URL
     *
     * @return string
     * @access public
     * @static
     * @param $file string The file to calculate the relative path of
     * @param $returnDir bool If set to true, only return the dirname
     */
    public static function calculate_relative_path($file, $returnDir = false)
    {
        // We find the document root of the webserver
        $doc_root = $_SERVER['DOCUMENT_ROOT'];

        // Remove the document root, from the FULL absolute path of the
        // file we're looking for
        $ret = "/" . str_replace($doc_root, "", $file, $ok);
        if (!$ok) {
            return false;
        }

        // If they want the path to the dir, only return the dir part
        if ($returnDir) {
            $ret = dirname($ret) . "/";
        }

        $ret = preg_replace("|//|", "/", $ret);

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
        $skin = static::_config('skin', 'selected', 'stylish');

        // custom selected skin
        $rel_css_file = "skins/{$skin}/skin.css";
        $css_file = KRUMO_DIR . $rel_css_file;
        if (is_readable($css_file)) {
            $css = file_get_contents($css_file);
        }

        // default skin
        if (!$css && ($skin != 'default')) {
            $skin         = 'stylish';
            $rel_css_file = "skins/$skin/skin.css";
            $css_file     = KRUMO_DIR . $rel_css_file;
            $css          = file_get_contents($css_file);
        }

        // print
        if ($_css = $css != '') {
            // See if there is a CSS path in the config
            $relative_krumo_path = static::calculate_relative_path(__FILE__, true);
            $css_url = static::_config('css', 'url', $relative_krumo_path);

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
                $js_text = file_get_contents($js_file);
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
        return true === static::_debug(true);
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
        return false === static::_debug(false);
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

    private static function sanitizeName($name)
    {
        // Check if the key has whitespace in it, if so show it and add an icon explanation
        $has_white_space = preg_match("/\\s/", $name);
        if ($has_white_space) {
            // Convert the white space to unicode underbars to visualize it

            $name  = preg_replace("/\\s/", "&#9251;", $name);
            $title = "Note: Key contains white space";
            $icon  = static::get_icon("information", $title);

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
    private static function _dump(&$data, $name = '&hellip;')
    {
        // Highlight elements that have a space in their name.
        // Spaces are hard to see in the HTML and are hard to troubleshoot
        $name = static::sanitizeName($name);

        // object
        if (is_object($data)) {
            static::_object($data, $name);
        }
        // Closure
// Not yet implemented

//        else if (($data instanceof \Closure))
//            static::_closure();

        elseif (is_array($data)) {
            static::_array($data, $name);
        }
        // resource
        elseif (is_resource($data)) {
            static::_resource($data, $name);
        }
        // scalar
        elseif (is_string($data)) {
            static::_string($data, $name);
        }
        // float
        elseif (is_float($data)) {
            static::_float($data, $name);
        }
        // integer
        elseif (is_integer($data)) {
            static::_integer($data, $name);
        }
        // boolean
        elseif (is_bool($data)) {
            static::_boolean($data, $name);
        }
        // null
        elseif (is_null($data)) {
            static::_null($name);
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

        $html = sprintf($html, $name, static::get_separator());

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
    private static $objectRecursionProtection = null;
    private static function &_hive(&$bee)
    {
        static $_ = array();

        // new bee
        if (!is_null($bee)) {

            // stain it
            $_recursion_marker = static::_marker();
            if (is_object($bee)) {
                $hash = spl_object_hash($bee);
                if ($hash && isset(static::$objectRecursionProtection[$hash])) {
                    static::$objectRecursionProtection[$hash]++;
                } elseif ($hash) {
                    static::$objectRecursionProtection[$hash] = 1;
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
        $_recursion_marker = static::_marker();

        if ($_is_object) {
            if (($hash = spl_object_hash($data)) && isset(static::$objectRecursionProtection[$hash])) {
                $_r = static::$objectRecursionProtection[$hash];
            } else {
                $_r = null;
            }
        } else {
            $_r = isset($data[$_recursion_marker]) ? $data[$_recursion_marker] : null;
        }

        // recursion detected
        if ($_r > 0) {
            static::_recursion();
        }

        // stain it
        static::_hive($data);

        // render it
        $collapsed = static::_isCollapsed(static::$_level, count($data)-1);
        if ($collapsed) {
            $collapse_style = 'style="display: none;"';
        } else {
            $collapse_style = '';
        }

        print "<div class=\"krumo-nest\" $collapse_style>";
        print "<ul class=\"krumo-node\">";

        // we're descending one level deeper
        static::$_level++;

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

                static::_dump($value, "<span>$prefix</span>&nbsp;$name");
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

                static::_dump($v, $k);
            }
        }

        print "</ul>\n</div>";
        static::$_level--;
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
        return is_array($var) && array_diff_key($var, array_keys(array_keys($var)));
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
        $config_sort = static::_config('sorting', 'sort_arrays', true);

        // If the sort is enabled in the config (default = yes) and the array is assoc (non-numeric)
        if (sizeof($data) > 1 && $config_sort && static::is_assoc($data)) {
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
        $collapsed = static::_isCollapsed(static::$_level, count($data));

        // Setup the CSS classes depending on how many children there are
        if ($childCount > 0 && $collapsed) {
            $elementClasses = ' krumo-expand';
        } elseif ($childCount > 0) {
            $elementClasses = ' krumo-expand krumo-opened';
        } else {
            $elementClasses = '';
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
            $_ = array_values($data());
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

        if (!($data instanceof \Closure) && count($data)) {
            static::_vars($data);
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
        $collapsed = static::_isCollapsed(static::$_level, $childCount);

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
        if ($childCount == 0) {
            $empty_str = ' (empty)';
        }

        $class_name = get_class($data);
        print "<a class=\"krumo-name\">$name</a> <em class=\"krumo-type\">Object</em> ";
        print static::get_separator() . " <strong class=\"krumo-class\">$class_name</strong>$empty_str</div>";

        // If the object is an inherited exception, we want to print out the trace
        // so we add a bogus trace parameter that contains the trace array
        // Note: this is not required (will throw an error) for raw Exceptions
        if ($data instanceof Exception && $class_name !== "Exception") {
            $data->trace = $data->getTrace();
        }

        if ($properties) {
            static::_vars($data);
        }

        // Remove the trace we added
        if ($data instanceof Exception && $class_name !== "Exception") {
            unset($data->trace);
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

        $html = sprintf($html, $name, static::get_separator(), get_resource_type($data));

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
        $value = '';
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

        $html = sprintf($html, $name, static::get_separator(), $value);

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
        print static::get_separator() . " <strong class=\"krumo-integer\">$data</strong>";

        $ut = static::is_datetime($name, $data);
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
        print static::get_separator() . " <strong class=\"krumo-float\">$data</strong>";

        $ut = static::is_datetime($name, $data);
        if ($ut) {
            print " ~ <strong class=\"krumo-datetime\">$ut</strong>";
        }

        print "</div></li>";
    }

    public static function get_icon($name, $title)
    {
        $path = dirname(__FILE__) . "/icons/$name.png";
        $rel  = static::calculate_relative_path($path);

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
        $separator = static::_config('display', 'separator', " =&gt; ");

        return $separator;
    }

    /**
      * Properly add "S" depending on the number of items
      * static::plural(1,"show") = "show"
      * static::plural(4,"show") = "shows"
      * @param $number integer How many finger(s) am I holding up?
      * @param $word string Word to possibly (simply) pluralize
      * @return string
      */
    private static function plural($number, $word)
    {
        if ($number > 1 || $number === 0) {
            return $word . "s";
        } elseif ($number === 1) {
            return $word;
        } else {
            return "???";
        }
    }

    private static function is_datetime($name, $value)
    {
        // If the name contains date or time, and the value looks like a unixtime
        if (preg_match("/date|time/i", $name) && ($value > 10000000 && $value < 4000000000)) {
            $ret = date("r", $value);

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
        $collapsed = static::_isCollapsed(static::$_level, 1);

        if ($collapsed) {
            $collapse_style = 'style="display: none;"';
        } else {
            $collapse_style = '';
        }

        // extra
        $_ = $data;

        // Get the truncate length from the config, or default to 100
        $truncate_length = static::_config('display', 'truncate_length', 100);
        $display_cr      = static::_config('display', 'carriage_returns', true);

        $strlen = strlen($data);
        if (function_exists('mb_strlen')) {
            $strlen = mb_strlen($data, static::getCharset());
        }

        if ($strlen > $truncate_length) {
            if (function_exists('mb_substr')) {
                $_ = mb_substr($data, 0, $truncate_length - 1, static::getCharset());
            } else {
                $_ = substr($data, 0, $truncate_length - 1);
            }
            $_extra = true;
        } else {
            $_extra = false;
        }

        $icon = '';
        // Check to see if the line has any carriage returns
        if (preg_match("/\n|\r/", $data)) {
            $slash_n = substr_count($data, "\n");
            $slash_r = substr_count($data, "\r");

            $title = "Note: String contains ";

            if ($slash_n) {
                $title .= "$slash_n " . static::plural($slash_n, "new line");
            }
            if ($slash_n && $slash_r) {
                $title .= " and ";
            }
            if ($slash_r) {
                $title .= "$slash_r " . static::plural($slash_r, "carriage return");
            }

            $icon = static::get_icon("information", $title);

            // We flag this as extra so the dropdown can show the correctly formatted version
            $_extra = true;
        }

        $_ = htmlentities($_);

        // Convert all the \r or \n to visible paragraph markers
        if ($display_cr) {
            $_ = preg_replace("/(\\r\\n|\\n|\\r)/", "<strong class=\"krumo-carrage-return\"> &para; </strong>", $_);
        } else {
            $_ = nl2br($_);
        }

        $expand_class = '';
        if ($_extra) {
            $expand_class = 'krumo-expand';
        }

        print "<li class=\"krumo-child\">";
        print "<div class=\"krumo-element $expand_class\" ";
        if ($_extra) {
            print " onClick=\"krumo.toggle(this);\" ";
        }
        print "onMouseOver=\"krumo.over(this);\" onMouseOut=\"krumo.out(this);\">\n";

        print "<a class=\"krumo-name\">$name</a> ";
        print "<em class=\"krumo-type\">String(<strong class=\"krumo-string-length\">" . strlen($data) . "</strong>)</em> $icon";

        print static::get_separator() . " <strong class=\"krumo-string\">" . $_;
        // This has to go AFTER the htmlspecialchars
        if (strlen($data) > $truncate_length) {
            print "&hellip;";
        }
        print "</strong>";

        $ut = static::is_datetime($name, $data);
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
            $data = nl2br($data);

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
    private static function isCli()
    {
        static $cli = null;
        if ($cli === null) {
            $cli = (php_sapi_name() === "cli");
        }

        return $cli;
    }
}


/**
* Alias of {@link static::dump()}
*
* @param mixed $data,...
*
* @see static::dump()
*/
if (!function_exists("krumo")) {
    function krumo()
    {
        $_ = func_get_args();

        return call_user_func_array(array('krumo', 'dump'), $_);
    }
}

if (!function_exists('k')) {
    function k()
    {
        $_ = func_get_args();

        return call_user_func_array(array('krumo', 'dump'), $_);
    }
}

if (!function_exists('kd')) {
    function kd()
    {
        if (php_sapi_name() !== 'cli')
            Krumo::htmlHeaders();
        $_ = func_get_args();
        call_user_func_array(array('krumo', 'dump'), $_);

        exit();
    }
}
// vim: tabstop=4 shiftwidth=4 expandtab autoindent
