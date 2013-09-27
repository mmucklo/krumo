krumo
=====

Krumo is a replacement for print_r() and var_dump(). This is an updated/forked version
because the **SourceForge.net** version appears to have been abandonned.

Installation:
-------------
Put this line in your header.php or global project include:

~~~PHP
include("/path/to/krumo/class.krumo.php");
~~~

**or**

Add this line to your composer.json "require" section:

### composer.json
```json
    "require": {
       ...
       "oodle/krumo": "*"
```

Usage:
------
After Krumo is loaded you have access to the global Krumo functions: **krumo()**, **k()**, and **kd()**.

```php
$arr = array(
	'first' => 'Jason',
	'last'  => 'Doolis,'
	'phone' => array(5032612314,4512392014),
	'likes' => array('animal' => 'kitten', 'color' => 'purple'),
);

// Dump out the array, short and long versions
k($arr); 
krumo($arr);

// Dump out the array and then exit();
kd($arr); 
```

Updates:
--------

1. Proper Object Dumping (ala var_dump)
   * Fixes for object dumping - to be able to dump Public, Protected, and Private members of objects
1. Dynamic Config
   * Ability to set the Config dynamically.
   * inspired by: [justmoon/krumo@a8208...](https://github.com/justmoon/krumo/commit/a82082d52f9dd348510175b508d5b2c73d69d7ad)
1. Node Collapse Behavior
   * Ability to specify nodes open by default
   * largely copied from: [justmoon/krumo@bd1f3...](https://github.com/justmoon/krumo/commit/bd1f3efd476122b5d6c79e881b6f1017c8771713) (with fixes)
1. New Options
   * New options under [display] in ini file (whether to show the line number / call info, plus whether to show the krumo version)
   * inspired by: [justmoon/krumo@37e1b1...](https://github.com/justmoon/krumo/commit/37e1b1c07ca0266baad699565314b11b80410df2)
1. New Default Url
   * Default URL in krumo.ini is now /krumo/
1. Cleanup
   * A few @($...) to isset($...) changes in class.krumo.php
   * All source code has been run through dos2unix
1. composer.json
   * Allow to be loaded via composer
1. setLineNumberTestCallback
   * Allow a custom line number test

### Sample usage of setLineNumberTestCallback
```php
// Place this in a utilites.php file or whatever as a convenience method for calling krumo(...)
function k()
{
    if (function_exists('krumo'))
    {
        $args = func_get_args();
        \krumo::setLineNumberTestCallback(function ($d) {
            if (strtolower($d['function']) == 'k')
                return true;
        });
        return call_user_func_array(
            array('krumo', 'dump'), $args);
    }
}
```

Note: Thanks to [justmoon](https://github.com/justmoon/krumo) for inspiration / code on a few features
