<?php

/**
 * Test: Nette\Web\SessionNamespace basic usage.
 *
 * @author     David Grudl
 * @category   Nette
 * @package    Nette\Web
 * @subpackage UnitTests
 */

use Nette\Web\Session;



require __DIR__ . '/../initialize.php';



$session = new Session;
$namespace = $session->getNamespace('one');
$namespace->a = 'apple';
$namespace->p = 'pear';
$namespace['o'] = 'orange';
foreach ($namespace as $key => $val) {
	T::dump( "$key=$val" );
}

Assert::true( isset($namespace['p']) );
Assert::true( isset($namespace->o) );
Assert::false( isset($namespace->undefined) );

unset($namespace['a']);
unset($namespace->p);
unset($namespace->o);
unset($namespace->undef);

Assert::same( '', http_build_query($namespace->getIterator()) );



__halt_compiler() ?>

------EXPECT------
"a=apple"

"p=pear"

"o=orange"
