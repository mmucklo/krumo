Krumo
=====

Krumo is a replacement for `print_r()` and `var_dump()`. This is an updated/forked version
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
After Krumo is loaded you have access to the global Krumo functions: `krumo()`, `k()`, and `kd()`.

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
