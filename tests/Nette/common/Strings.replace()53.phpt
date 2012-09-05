<?php

/**
 * Test: Nette\Strings::replace()
 *
 * @author     David Grudl
 * @package    Nette
 * @phpversion 5.3
 */

use Nette\Strings;



require __DIR__ . '/../bootstrap.php';



Assert::same( '@o wor@d!', Strings::replace('hello world!', '#[e-l]+#', function() { return '@'; }) );
