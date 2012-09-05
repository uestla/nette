<?php

/**
 * Test: Nette\Arrays::flatten()
 *
 * @author     David Grudl
 * @package    Nette
 */

use Nette\Arrays;



require __DIR__ . '/../bootstrap.php';



$res = Arrays::flatten(array(
	2 => array('a', array('b')),
	4 => array('c', 'd'),
	'e',
));

Assert::same( array(
	0 => 'a',
	1 => 'b',
	2 => 'c',
	3 => 'd',
	4 => 'e',
), $res);
