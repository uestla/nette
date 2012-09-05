<?php

/**
 * Test: Nette\Strings and RegexpException compile error.
 *
 * @author     David Grudl
 * @package    Nette
 * @subpackage UnitTests
 */

use Nette\Strings;



require __DIR__ . '/../bootstrap.php';



Assert::throws(function() {
	Strings::split('0123456789', '#*#');
}, 'Nette\RegexpException', 'preg_split(): Compilation failed: nothing to repeat at offset 0 in pattern: #*#');

Assert::throws(function() {
	Strings::match('0123456789', '#*#');
}, 'Nette\RegexpException', 'preg_match(): Compilation failed: nothing to repeat at offset 0 in pattern: #*#');

Assert::throws(function() {
	Strings::matchAll('0123456789', '#*#');
}, 'Nette\RegexpException', 'preg_match_all(): Compilation failed: nothing to repeat at offset 0 in pattern: #*#');

Assert::throws(function() {
	Strings::replace('0123456789', '#*#', 'x');
}, 'Nette\RegexpException', 'preg_replace(): Compilation failed: nothing to repeat at offset 0 in pattern: #*#');

Assert::throws(function() {
	Strings::replace('0123456789', array('##', '#*#'), 'x');
}, 'Nette\RegexpException', 'preg_replace(): Compilation failed: nothing to repeat at offset 0 in pattern: ## or #*#');

function cb() { return 'x'; }

Assert::throws(function() {
	Strings::replace('0123456789', '#*#', new Nette\Callback('cb'));
}, 'Nette\RegexpException', 'preg_match(): Compilation failed: nothing to repeat at offset 0 in pattern: #*#');

Assert::throws(function() {
	Strings::replace('0123456789', array('##', '#*#'), new Nette\Callback('cb'));
}, 'Nette\RegexpException', 'preg_match(): Compilation failed: nothing to repeat at offset 0 in pattern: #*#');
