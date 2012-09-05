<?php

/**
 * Test: Nette\Validators\Validators::assertField()
 *
 * @author     David Grudl
 * @package    Nette
 */

use Nette\Validators\Validators;



require __DIR__ . '/../bootstrap.php';


$arr = array('first' => TRUE);

Assert::throws(function() use ($arr) {
	Validators::assertField(NULL, 'foo', 'foo');
}, 'Nette\AssertionException', "The first argument expects to be array, NULL given.");

Assert::throws(function() use ($arr) {
	Validators::assertField($arr, 'second', 'int');
}, 'Nette\AssertionException', "Missing item 'second' in array.");

Validators::assertField($arr, 'first');

Assert::throws(function() use ($arr) {
	Validators::assertField($arr, 'first', 'int');
}, 'Nette\AssertionException', "The item 'first' in array expects to be int, boolean given.");
