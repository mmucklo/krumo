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

if (!defined('KRUMO_CAPTURE')) {
	define('KRUMO_CAPTURE','158bafa5-b505-4661-9904-46504e00a5bb');
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
Class krumo {

	/**
	* Return Krumo version
	*
	* @return string
	* @access public
	* @static
	*/
	Public Static Function version() {
		return '0.4.3';
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Prints a debug backtrace
	*
	* @access public
	* @static
	*/
	Public Static Function backtrace() {
		// disabled ?
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		return krumo::dump(debug_backtrace());
	}

	/**
	* Prints a list of all currently declared classes.
	*
	* @access public
	* @static
	*/
	Public Static Function classes() {
		// disabled ?
		if (!krumo::_debug()) {
			return false;
		}

		print "<div class=\"krumo-title\">\n";
		print "This is a list of all currently declared classes.";
		print "</div>\n";

		return krumo::dump(get_declared_classes());
	}

	/**
	* Prints a list of all currently declared interfaces (PHP5 only).
	*
	* @access public
	* @static
	*/
	Public Static Function interfaces() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all currently declared interfaces.</div>";

		return krumo::dump(get_declared_interfaces());
	}

	/**
	* Prints a list of all currently included (or required) files.
	*
	* @access public
	* @static
	*/
	Public Static Function includes() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all currently included (or required) files.</div>";

		return krumo::dump(get_included_files());
	}

	/**
	* Prints a list of all currently declared functions.
	*
	* @access public
	* @static
	*/
	Public Static Function functions() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all currently declared functions.</div>";

		return krumo::dump(get_defined_functions());
	}

	/**
	* Prints a list of all currently declared constants.
	*
	* @access public
	* @static
	*/
	Public Static Function defines() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all currently declared constants (defines).</div>";

		return krumo::dump(get_defined_constants());
	}

	/**
	* Prints a list of all currently loaded PHP extensions.
	*
	* @access public
	* @static
	*/
	Public Static Function extensions() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all currently loaded PHP extensions.</div>";

		return krumo::dump(get_loaded_extensions());
	}

	/**
	* Prints a list of all HTTP request headers.
	*
	* @access public
	* @static
	*/
	Public Static Function headers() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all HTTP request headers.</div>";

		return krumo::dump(getAllHeaders());
	}

	/**
	* Prints a list of the configuration settings read from <i>php.ini</i>
	*
	* @access public
	* @static
	*/
	Public Static Function phpini() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		if (!is_readable(get_cfg_var('cfg_file_path'))) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">";
		print "This is a list of the configuration settings read from <code><b>" . get_cfg_var('cfg_file_path') . "</b></code>.";
		print "</div>";

		return krumo::dump(parse_ini_file(get_cfg_var('cfg_file_path'), true));
	}

	/**
	* Prints a list of all your configuration settings.
	*
	* @access public
	* @static
	*/
	Public Static Function conf() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all your configuration settings.</div>";

		return krumo::dump(ini_get_all());
	}

	/**
	* Prints a list of the specified directories under your <i>include_path</i> option.
	*
	* @access public
	* @static
	*/
	Public Static Function path() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\"> This is a list of the specified directories under your <code><b>include_path</b></code> option.</div>";

		return krumo::dump(explode(PATH_SEPARATOR, ini_get('include_path')));
	}

	/**
	* Prints a list of all the values from the <i>$_REQUEST</i> array.
	*
	* @access public
	* @static
	*/
	Public Static Function request() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_REQUEST</b></code> array.</div>";

		return krumo::dump($_REQUEST);
	}

	/**
	* Prints a list of all the values from the <i>$_GET</i> array.
	*
	* @access public
	* @static
	*/
	Public Static Function get() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_GET</b></code> array.</div>";

		return krumo::dump($_GET);
	}

	/**
	* Prints a list of all the values from the <i>$_POST</i> array.
	*
	* @access public
	* @static
	*/
	Public Static Function post() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_POST</b></code> array.</div>";

		return krumo::dump($_POST);
	}

	/**
	* Prints a list of all the values from the <i>$_SERVER</i> array.
	*
	* @access public
	* @static
	*/
	Public Static Function server() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_SERVER</b></code> array.</div>";

		return krumo::dump($_SERVER);
	}

	/**
	* Prints a list of all the values from the <i>$_COOKIE</i> array.
	*
	* @access public
	* @static
	*/
	Public Static Function cookie() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_COOKIE</b></code> array.</div>";

		return krumo::dump($_COOKIE);
	}

	/**
	* Prints a list of all the values from the <i>$_ENV</i> array.
	*
	* @access public
	* @static
	*/
	Public Static Function env() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_ENV</b></code> array.</div>";

		return krumo::dump($_ENV);
	}

	/**
	* Prints a list of all the values from the <i>$_SESSION</i> array.
	*
	* @access public
	* @static
	*/
	Public Static Function session() {

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// render it
		print "<div class=\"krumo-title\">This is a list of all the values from the <code><b>\$_SESSION</b></code> array.</div>";

		return krumo::dump($_SESSION);
	}

	/**
	* Prints a list of all the values from an INI file.
	*
	* @param string $ini_file
	*
	* @access public
	* @static
	*/
	Public Static Function ini($ini_file) {

		// disabled
		if (!krumo::_debug()) {
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

		return krumo::dump($_);
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Dump information about a variable
	*
	* @param mixed $data,...
	* @access public
	* @static
	*/
	Public Static Function dump($data, $capture = '') {
		// If we're capturing call dump() with just data and capture the output
		if ($capture === KRUMO_CAPTURE) {
			ob_start();

			krumo::dump($data);

			$str = ob_get_clean();
			return $str;
		}

		$clearObjectRecursionProtection   = false;
		if (self::$objectRecursionProtection === NULL) {
			self::$objectRecursionProtection = array();
			$clearObjectRecursionProtection  = true;
		}

		// disabled
		if (!krumo::_debug()) {
			return false;
		}

		// more arguments
		if (func_num_args() > 1) {
			$_ = func_get_args();
			foreach($_ as $d) {
				krumo::dump($d);
			}
			return;
		}

		// the css
		krumo::_css();

		// find caller
		$_ = debug_backtrace();
		while($d = array_pop($_)) {
			$callback = self::$lineNumberTestCallback;
			$function = strToLower($d['function']);
			if (in_array($function, array("krumo","k","kd")) || (strToLower(@$d['class']) == 'krumo') || (is_callable($callback) && $callback($d))) {
				break;
			}
		}

		$showVersion  = krumo::_config('display', 'show_version', TRUE);
		$showCallInfo = krumo::_config('display', 'show_call_info', TRUE);
		$krumoUrl     = 'https://github.com/oodle/krumo';

		//////////////////////
		// Start HTML header//
		//////////////////////
		print "<div class=\"krumo-root\">\n";
		print "\t<ul class=\"krumo-node krumo-first\">\n";
		
		// The actual item itself
		print krumo::_dump($data);

		if ($showVersion || $showCallInfo) {
			print "\t\t<li class=\"krumo-footnote\">\n";

			if ($showVersion) {
				$version = krumo::version();
				print "<div class=\"krumo-version\" style=\"white-space:nowrap;\">\n";
				print "<h6>Krumo version $version</h6> | <a href=\"$krumoUrl\"	target=\"_blank\">$krumoUrl</a>\n";
				print "</div>\n";
			}

			if ($showCallInfo && isset($d['file']) && $d['file']) {
				print "<span class=\"krumo-call\" style=\"white-space:nowrap;\">";
				print "Called from <code>" . $d['file'] . "</code>, ";
				print "line <code>" . $d['line'] . "</code></span>";
			}

			print "</li>";
		}

		print "</ul></div>\n";
		print "<!-- Krumo - HTML -->\n\n";
		////////////////////
		// End HTML header//
		////////////////////

		// flee the hive
		$_recursion_marker = krumo::_marker();
		if ($hive =& krumo::_hive($dummy)) {
			foreach($hive as $i => $bee) {
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

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	 * Configuration array.
	 */
	Private Static $_config = array();

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
	Private Static Function _config($group, $name, $fallback=null) {
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

	Public Static Function setConfig($config) {
		self::$_config = $config;
	}

	Public Static Function setLineNumberTestCallback($callback) {
		self::$lineNumberTestCallback = $callback;
	}
	
	Private Static $lineNumberTestCallback = null;
	
	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Cascade configuration array
	*
	* By default, all nodes are collapsed.
	*/
	Private Static $_cascade = null;

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
	Public Static Function cascade(array $cascade = null) {
		self::$_cascade = $cascade;
	}

	/**
	* Determines if a given node will be collapsed or not.
	*/
	Private Static Function _isCollapsed($level, $childCount) {
		$cascade = self::$_cascade;

		if ($cascade == null) {
			$cascade = krumo::_config('display', 'cascade', array());
		}

		if (isset($cascade[$level])) {
			return $childCount >= $cascade[$level];
		} else {
			return true;
		}
	}
	
	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Calculate the relative path of a given absolute URL
	*
	* @return string
	* @access public
	* @static
	*/
	Public Static function calculate_relative_path($file, $return_dir = 0) {
		// We find the document root of the webserver
		$doc_root = $_SERVER['DOCUMENT_ROOT'];

		// Remove the document root, from the FULL absolute path of the 
		// file we're looking for
		$ret = "/" . str_replace($doc_root,"",$file,$ok);
		if (!$ok) { return false; }

		// If they want the path to the dir, only return the dir part
		if ($return_dir) { $ret = dirname($ret) . "/"; }

		return $ret;
	}
	
	/**
	* Print the skin (CSS)
	*
	* @return boolean
	* @access private
	* @static
	*/
	Private Static Function _css() {
		static $_css = false;

		// already set ?
		if ($_css) {
			return true;
		}

		$css = '';
		$skin = krumo::_config('skin', 'selected', 'default');

		// custom selected skin
		$rel_css_file = "skins/{$skin}/skin.css";
		$css_file = KRUMO_DIR . $rel_css_file;
		if (is_readable($css_file)) {
			$css = join(file($css_file));
		}

		// default skin
		if (!$css && ($skin != 'default')) {
			$skin         = 'default';
			$rel_css_file = "skins/default/skin.css";
			$css_file     = KRUMO_DIR . $rel_css_file;
			$css          = join(file($css_file));
		}

		// print
		if ($_css = $css != '') {
			// See if there is a CSS path in the config
			$relative_krumo_path = krumo::calculate_relative_path(__FILE__,true);
			$css_url = krumo::_config('css', 'url', $relative_krumo_path);

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
			print join(file(KRUMO_DIR . "krumo.min.js"));
			print "</script>\n";
			print "<!-- Krumo - JavaScript -->\n";
		}

		return $_css;
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Enable Krumo
	*
	* @return boolean
	* @access public
	* @static
	*/
	Public Static Function enable() {
		return true === krumo::_debug(true);
	}

	/**
	* Disable Krumo
	*
	* @return boolean
	* @access public
	* @static
	*/
	Public Static Function disable() {
		return false === krumo::_debug(false);
	}

	/**
	* Get\Set Krumo state: whether it is enabled or disabled
	*
	* @param boolean $state
	* @return boolean
	* @access private
	* @static
	*/
	Private Static Function _debug($state = null) {
		static $_ = true;

		// set
		if (isset($state)) {
			$_ = (boolean)$state;
		}

		// get
		return $_;
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Dump information about a variable
	*
	* @param mixed $data
	* @param string $name
	* @access private
	* @static
	*/
	Private Static Function _dump(&$data, $name = '...') {
		// object
		if (is_object($data)) {
			return krumo::_object($data, $name);
		}

		// array
		if (is_array($data)) {
			return krumo::_array($data, $name);
		}

		// resource
		if (is_resource($data)) {
			return krumo::_resource($data, $name);
		}

		// scalar
		if (is_string($data)) {
			return krumo::_string($data, $name);
		}

		// float
		if (is_float($data)) {
			return krumo::_float($data, $name);
		}

		// integer
		if (is_integer($data)) {
			return krumo::_integer($data, $name);
		}

		// boolean
		if (is_bool($data)) {
			return krumo::_boolean($data, $name);
		}

		// null
		if (is_null($data)) {
			return krumo::_null($name);
		}
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Render a dump for a NULL value
	*
	* @param string $name
	* @return string
	* @access private
	* @static
	*/
	Private Static Function _null($name) {
		print "<li class=\"krumo-child\">";
		print "<div class=\"krumo-element\" onMouseOver=\"krumo.over(this);\" onMouseOut=\"krumo.out(this);\">";
		print "<a class=\"krumo-name\">$name</a> (<em class=\"krumo-type krumo-null\">NULL</em>)";
		print "</div></li>";
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Return the marked used to stain arrays
	* and objects in order to detect recursions
	*
	* @return string
	* @access private
	* @static
	*/
	Private Static Function _marker() {
		static $_recursion_marker;
		if (!isset($_recursion_marker)) {
			$_recursion_marker = uniqid('krumo');
		}

		return $_recursion_marker;
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Adds a variable to the hive of arrays and objects which
	* are tracked for whether they have recursive entries
	*
	* @param mixed &$bee either array or object, not a scallar vale
	* @return array all the bees
	*
	* @access private
	* @static
	*/
	Private Static $objectRecursionProtection = NULL;
	Private Static Function &_hive(&$bee) {

		static $_ = array();

		// new bee
		if (!is_null($bee)) {

			// stain it
			$_recursion_marker = krumo::_marker();
			if (is_object($bee)) {
				$hash = spl_object_hash($bee);
				if ($hash && isset(self::$objectRecursionProtection[$hash])) {
					self::$objectRecursionProtection[$hash]++;
				} else if ($hash) {
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

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Level of recursion.
	*/
	Private Static $_level = 0;	
	
	/**
	* Render a dump for the properties of an array or objeect
	*
	* @param mixed &$data
	* @access private
	* @static
	*/
	Private Static Function _vars(&$data) {

		$_is_object = is_object($data);

		// test for references in order to
		// prevent endless recursion loops
		$_recursion_marker = krumo::_marker();

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
			return krumo::_recursion();
		}

		// stain it
		krumo::_hive($data);

		// render it
		$collapsed = krumo::_isCollapsed(self::$_level, count($data)-1);
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
					$prefix = 'private ';
				} else if ($property->isProtected()) {
					$setAccessible = true;
					$prefix = 'protected ';
				} else if ($property->isPublic()) {
					$prefix = 'public ';
				}

				$name = $property->getName();
				if ($setAccessible) {
					$property->setAccessible(true);
				}

				$value = $property->getValue($data);
				
				krumo::_dump($value, $prefix . " '$name'");
				if ($setAccessible) {
					$property->setAccessible(false);
				}
			}
		} else {
			// keys
			$keys = array_keys($data);

			// iterate
			foreach($keys as $k) {
				// skip marker
				if ($k === $_recursion_marker) {
					continue;
				}

				// get real value
				$v =& $data[$k];

				krumo::_dump($v,$k);
			}
		} 

		print "</ul>\n</div>";
		self::$_level--;
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Render a block that detected recursion
	*
	* @access private
	* @static
	*/
	Private Static Function _recursion() {
	print '<div class="krumo-nest" style="display:none;">
	<ul class="krumo-node">
		<li class="krumo-child">
			<div class="krumo-element" onMouseOver="krumo.over(this);" onMouseOut="krumo.out(this);">
				<a class="krumo-name"><big>&#8734;</big></a>
				(<em class="krumo-type">Recursion</em>)
			</div>

		</li>
	</ul>';
	}

	Private Function is_assoc($var) {
		return is_array($var) && array_diff_key($var,array_keys(array_keys($var)));
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Render a dump for an array
	*
	* @param mixed $data
	* @param string $name
	* @access private
	* @static
	*/
	Private Static Function _array($data, $name) {
		$config_sort = krumo::_config('display','sort_arrays',true);

		// If the sort is enabled in the config (default = yes) and the array is assoc (non-numeric)
		if (sizeof($data) > 1 && $config_sort && krumo::is_assoc($data)) {
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
		$collapsed = krumo::_isCollapsed(self::$_level, count($data));

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
		print "<a class=\"krumo-name\">$name</a> (<em class=\"krumo-type\">Array, <strong class=\"krumo-array-length\">";
		print count($data) . " element" . $plural;
		print "</strong></em>)";
		if ($sort) { 
			$title = "Array has been sorted prior to display. This is configurable in krumo.ini.";
			print " - <span title=\"$title\" style=\"color: darkred\"><b>sorted</b></span>";
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
			krumo::_vars($data);
		}

		print "</li>";
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Render a dump for an object
	*
	* @param mixed $data
	* @param string $name
	* @access private
	* @static
	*/
	Private Static Function _object(&$data, $name) {
		$reflection = new ReflectionObject($data);
		$properties = $reflection->getProperties();

		$childCount = count($properties);
		$collapsed = krumo::_isCollapsed(self::$_level, $childCount);

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

		print "<a class=\"krumo-name\">$name</a> (<em class=\"krumo-type\">Object</em>) ";
		print "<strong class=\"krumo-class\">" . get_class($data) . "</strong>$empty_str</div>";

		if ($properties) {
			krumo::_vars($data);
		}
		
		print "</li>";
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Render a dump for a resource
	*
	* @param mixed $data
	* @param string $name
	* @access private
	* @static
	*/
	Private Static Function _resource($data, $name) {
		print "<li class=\"krumo-child\">";
		print "<div class=\"krumo-element\" onMouseOver=\"krumo.over(this);\" onMouseOut=\"krumo.out(this);\">";
		print "<a class=\"krumo-name\">$name</a> (<em class=\"krumo-type\">Resource</em>) ";
		print "<strong class=\"krumo-resource\">" . get_resource_type($data) . "</strong>";
		print "</div></li>";
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Render a dump for a boolean value
	*
	* @param mixed $data
	* @param string $name
	* @access private
	* @static
	*/
	Private Static Function _boolean($data, $name) {
		if ($data == false) { $value = "FALSE"; }
		elseif ($data == true) { $value = "TRUE"; }

		print "<li class=\"krumo-child\">";
		print "<div class=\"krumo-element\" onMouseOver=\"krumo.over(this);\" onMouseOut=\"krumo.out(this);\">";
		print "<a class=\"krumo-name\">$name</a> (<em class=\"krumo-type\">Boolean</em>) ";
		print "<strong class=\"krumo-boolean\">$value</strong>";
		print "</div></li>";
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Render a dump for a integer value
	*
	* @param mixed $data
	* @param string $name
	* @access private
	* @static
	*/
	Private Static Function _integer($data, $name) {
		print "<li class=\"krumo-child\">";
		print "<div class=\"krumo-element\" onMouseOver=\"krumo.over(this);\" onMouseOut=\"krumo.out(this);\">";
		print "<a class=\"krumo-name\">$name</a> (<em class=\"krumo-type\">Integer</em>) ";
		print "<strong class=\"krumo-integer\">$data</strong></div></li>";
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Render a dump for a float value
	*
	* @param mixed $data
	* @param string $name
	* @access private
	* @static
	*/
	Private Static Function _float($data, $name) {
		print "<li class=\"krumo-child\">";
		print "<div class=\"krumo-element\" onMouseOver=\"krumo.over(this);\" onMouseOut=\"krumo.out(this);\">";
		print "<a class=\"krumo-name\">$name</a> (<em class=\"krumo-type\">Float</em>) ";
		print "<strong class=\"krumo-float\">$data</strong></div></li>";
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

	/**
	* Render a dump for a string value
	*
	* @param mixed $data
	* @param string $name
	* @access private
	* @static
	*/
	Private Static Function _string($data, $name) {
		$collapsed = krumo::_isCollapsed(self::$_level, 1);

		if ($collapsed) {
			$collapse_style = 'style="display: none;"';
		} else {
			$collapse_style = '';
		}

		// extra
		$_extra = false;
		$_ = $data;

		// Get the truncate length from the config, or default to 100
		$truncate_length = krumo::_config('display', 'truncate_length', 100);

		if (strLen($data) > $truncate_length ) {
			$_ = substr($data, 0, $truncate_length - 3) . '...';
			$_extra = true;
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
		print "(<em class=\"krumo-type\">String, <strong class=\"krumo-string-length\">" . strlen($data) . " characters</strong></em>) ";
		print "<strong class=\"krumo-string\">" . htmlSpecialChars($_) . "</strong>";

		// callback
		if (is_callable($data)) {
			print "<span class=\"krumo-callback\"> | ";
			print "(<em class=\"krumo-type\">Callback</em>) <strong class=\"krumo-string\">" . htmlSpecialChars($_) . "()</strong></span>";
		}

		print "</div>";

		if ($_extra) {
			print "<div class=\"krumo-nest\" $collapse_style>";
			print "<ul class=\"krumo-node\">";
			print "<li class=\"krumo-child\"> <div class=\"krumo-preview\">" . htmlSpecialChars($data) . "</div></li>";
			print "</ul></div>";
		}

		print "</li>";
	}

	// -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- -- --

//--end-of-class--
}

//////////////////////////////////////////////////////////////////////////////

/**
* Alias of {@link krumo::dump()}
*
* @param mixed $data,...
*
* @see krumo::dump()
*/
Function krumo() {
	$_ = func_get_args();
	return call_user_func_array(array('krumo', 'dump'), $_);
}

function k() {
	$_ = func_get_args();
	return call_user_func_array(array('krumo', 'dump'), $_);
}

function kd() {
	$_ = func_get_args();
	call_user_func_array(array('krumo', 'dump'), $_);

	exit();
}

//////////////////////////////////////////////////////////////////////////////

?>
