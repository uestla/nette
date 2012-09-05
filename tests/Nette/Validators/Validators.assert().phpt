<?php

/**
 * Test: Nette\Validators\Validators::assert()
 *
 * @author     David Grudl
 * @package    Nette\Utils
 * @subpackage UnitTests
 */

use Nette\Validators\Validators;



require __DIR__ . '/../bootstrap.php';



Assert::throws(function() {
	Validators::assert(TRUE, 'int');
}, 'Nette\AssertionException', "The variable expects to be int, boolean given.");

Assert::throws(function() {
	Validators::assert('1.0', 'int|float');
}, 'Nette\AssertionException', "The variable expects to be int or float, string '1.0' given.");

Assert::throws(function() {
	Validators::assert(1, 'string|integer:2..5', 'variable');
}, 'Nette\AssertionException', "The variable expects to be string or integer in range 2..5, integer given.");
