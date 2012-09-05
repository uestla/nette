<?php

/**
 * Test: Nette\Tokenizer::tokenize simple
 *
 * @author     David Grudl
 * @package    Nette
 */

use Nette\Tokenizer;



require __DIR__ . '/../bootstrap.php';



$tokenizer = new Tokenizer(array(
	'\d+',
	'\s+',
	'\w+',
));
$tokenizer->tokenize('say 123');
Assert::same( array('say', ' ', '123'), $tokenizer->tokens );

Assert::throws(function() use ($tokenizer) {
	$tokenizer->tokenize('say 123;');
}, 'Nette\TokenizerException', "Unexpected ';' on line 1, column 8.");
