<?php

require('../../class.krumo.php');

$obj = (object) array('a' => array('b' => array('c' => array('d' => array('e' => null)))));

krumo($obj);

