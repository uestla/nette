<?php

/**
 * Test: Nette\Strings::split()
 *
 * @author     David Grudl
 * @package    Nette
 */

use Nette\Strings;



require __DIR__ . '/../bootstrap.php';



Assert::same( array(
	'a',
	',',
	'b',
	',',
	'c',
), Strings::split('a, b, c', '#(,)\s*#') );

Assert::same( array(
	'a',
	',',
	'b',
	',',
	'c',
), Strings::split('a, b, c', '#(,)\s*#', PREG_SPLIT_NO_EMPTY) );
