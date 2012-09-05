<?php

/**
 * Test: Nette\Validators\Validators::isInRange()
 *
 * @author     David Grudl
 * @package    Nette
 */

use Nette\Validators\Validators;



require __DIR__ . '/../bootstrap.php';



Assert::true( Validators::isInRange(1, array(0,2)) );
Assert::true( Validators::isInRange(-1, array(-1,1)) );
Assert::true( Validators::isInRange(1, array(-1,1)) );
Assert::true( Validators::isInRange(0.1, array(-0.5,0.5)) );
Assert::false( Validators::isInRange(2, array(-1,1)) );
Assert::false( Validators::isInRange(2.5, array(-1,1)) );
