<?php

/**
 * Test: Nette\Neon::decode errors.
 *
 * @author     David Grudl
 * @package    Nette
 * @subpackage UnitTests
 */

use Nette\Neon;



require __DIR__ . '/../bootstrap.php';



Assert::throws(function() {
	Neon::decode("Hello\nWorld");
}, 'Nette\NeonException', "Unexpected 'World' on line 2, column 1." );


Assert::throws(function() {
	Neon::decode("- Dave,\n- Rimmer,\n- Kryten,\n");
}, 'Nette\NeonException', "Unexpected ',' on line 1, column 6." );


Assert::throws(function() {
	Neon::decode("- first: Dave\n last: Lister\n gender: male\n");
}, 'Nette\NeonException', "Unexpected ':' on line 1, column 7." );


Assert::throws(function() {
	Neon::decode('item [a, b]');
}, 'Nette\NeonException', "Unexpected ',' on line 1, column 7." );


Assert::throws(function() {
	Neon::decode('{,}');
}, 'Nette\NeonException', "Unexpected ',' on line 1, column 1." );


Assert::throws(function() {
	Neon::decode('{a, ,}');
}, 'Nette\NeonException', "Unexpected ',' on line 1, column 4." );
