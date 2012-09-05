<?php

/**
 * Test: Nette\Strings and RegexpException run-time error.
 *
 * @author     David Grudl
 * @package    Nette
 */

use Nette\Strings;



require __DIR__ . '/../bootstrap.php';



Assert::throws(function() {
	Strings::split("0123456789\xFF", '#\d#u');
}, 'Nette\RegexpException', 'Malformed UTF-8 data (pattern: #\d#u)');

Assert::throws(function() {
	Strings::match("0123456789\xFF", '#\d#u');
}, 'Nette\RegexpException', 'Malformed UTF-8 data (pattern: #\d#u)');

Assert::throws(function() {
	Strings::matchAll("0123456789\xFF", '#\d#u');
}, 'Nette\RegexpException', 'Malformed UTF-8 data (pattern: #\d#u)');

Assert::throws(function() {
	Strings::replace("0123456789\xFF", '#\d#u', 'x');
}, 'Nette\RegexpException', 'Malformed UTF-8 data (pattern: #\d#u)');

function cb() { return 'x'; }

Assert::throws(function() {
	Strings::replace("0123456789\xFF", '#\d#u', new Nette\Callback('cb'));
}, 'Nette\RegexpException', 'Malformed UTF-8 data (pattern: #\d#u)');
