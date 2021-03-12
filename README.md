Krumo
=====

Krumo is a replacement for `print_r()` and `var_dump()`. This is an updated version
because the **SourceForge.net** version appears to have been abandoned.

Installation:
-------------
Include the Krumo class in your `header.php` or global project include:

~~~PHP
include("/path/to/krumo/class.krumo.php");
~~~

**or**

Add this line to your composer.json "require" section:

composer.json
-------------
```json
{
	"require": {
		"mmucklo/krumo": "*"
	}
}
```

Usage:
------
After Krumo is loaded you have access to the global Krumo functions: `krumo()`, `k()`, and `kd()`.

```php
$arr = array(
	'first' => 'Jason',
	'last'  => 'Doolis',
	'phone' => array(5032612314,4512392014),
	'likes' => array('animal' => 'kitten', 'color' => 'purple'),
);

// Dump out the array, short and long versions
k($arr);
krumo($arr);

// Output the array and then exit();
kd($arr);

// Return the HTML output instead of printing it out
$my_html = krumo($arr, KRUMO_RETURN);

// Output the array with all nodes expanded
krumo($arr, KRUMO_EXPAND_ALL);

// The object based method
$krumo = new Krumo;
$krumo->dump($arr);
```

Options:
--------
These options can be passed as the *second* argument to Krumo to alter behavior:

* `KRUMO_RETURN` - return the Krumo output instead of printing it
* `KRUMO_EXPAND_ALL` - start Krumo with all nodes expanded
* `KRUMO_SORT` - sort arrays before displaying (note: overrides config)
* `KRUMO_NO_SORT` - do **not** sort arrays before displaying (note: overrides config)

Configuration:
--------------

Krumo *will* work without a configuration file. If you'd like to change the default settings you can copy the `krumo.sample.ini` to `krumo.ini` and change the file appropriately.
