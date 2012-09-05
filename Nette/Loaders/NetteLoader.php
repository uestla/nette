<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Nette\Loaders;

use Nette;



/**
 * Nette auto loader is responsible for loading Nette classes and interfaces.
 *
 * @author     David Grudl
 */
class NetteLoader extends AutoLoader
{
	/** @var NetteLoader */
	private static $instance;

	/** @var array */
	public $renamed = array(
		'Nette\Configurator' => 'Nette\Config\Configurator',
		'Nette\Http\User' => 'Nette\Security\User',
		'Nette\Templating\DefaultHelpers' => 'Nette\Templating\Helpers',
		'Nette\Latte\ParseException' => 'Nette\Latte\CompileException',
		'Nette\Utils\PhpGenerator\ClassType' => 'Nette\PhpGenerator\ClassType',
		'Nette\Utils\PhpGenerator\Helpers' => 'Nette\PhpGenerator\Helpers',
		'Nette\Utils\PhpGenerator\Method' => 'Nette\PhpGenerator\Method',
		'Nette\Utils\PhpGenerator\Parameter' => 'Nette\PhpGenerator\Parameter',
		'Nette\Utils\PhpGenerator\PhpLiteral' => 'Nette\PhpGenerator\PhpLiteral',
		'Nette\Utils\PhpGenerator\Property' => 'Nette\PhpGenerator\Property',
		'Nette\Utils\Arrays' => 'Nette\Arrays',
		'Nette\Utils\Finder' => 'Nette\Finder',
		'Nette\Utils\Html' => 'Nette\Html',
		'Nette\Utils\Json' => 'Nette\Json',
		'Nette\Utils\LimitedScope' => 'Nette\LimitedScope',
		'Nette\Utils\MimeTypeDetector' => 'Nette\MimeTypeDetector',
		'Nette\Utils\Neon' => 'Nette\Neon',
		'Nette\Utils\Paginator' => 'Nette\Paginator',
		'Nette\Utils\SafeStream' => 'Nette\SafeStream',
		'Nette\Utils\Strings' => 'Nette\Strings',
		'Nette\Utils\Tokenizer' => 'Nette\Tokenizer',
		'Nette\Utils\AssertionException' => 'Nette\AssertionException',
		'Nette\Utils\Validators' => 'Nette\Validators\Validators',
	);

	/** @var array */
	public $list = array(
		'NetteModule\ErrorPresenter' => '/Application/ErrorPresenter',
		'NetteModule\MicroPresenter' => '/Application/MicroPresenter',
		'Nette\Application\AbortException' => '/Application/exceptions',
		'Nette\Application\ApplicationException' => '/Application/exceptions',
		'Nette\Application\BadRequestException' => '/Application/exceptions',
		'Nette\Application\ForbiddenRequestException' => '/Application/exceptions',
		'Nette\Application\InvalidPresenterException' => '/Application/exceptions',
		'Nette\ArgumentOutOfRangeException' => '/common/exceptions',
		'Nette\ArrayHash' => '/common/ArrayHash',
		'Nette\ArrayList' => '/common/ArrayList',
		'Nette\Arrays' => '/common/Arrays',
		'Nette\AssertionException' => '/common/exceptions',
		'Nette\Callback' => '/common/Callback',
		'Nette\DI\MissingServiceException' => '/DI/exceptions',
		'Nette\DI\ServiceCreationException' => '/DI/exceptions',
		'Nette\DateTime' => '/common/DateTime',
		'Nette\DeprecatedException' => '/common/exceptions',
		'Nette\DirectoryNotFoundException' => '/common/exceptions',
		'Nette\Environment' => '/Config/Environment',
		'Nette\FatalErrorException' => '/common/exceptions',
		'Nette\FileNotFoundException' => '/common/exceptions',
		'Nette\Finder' => '/common/Finder',
		'Nette\Framework' => '/common/Framework',
		'Nette\FreezableObject' => '/common/FreezableObject',
		'Nette\Html' => '/common/Html',
		'Nette\IFreezable' => '/common/IFreezable',
		'Nette\IOException' => '/common/exceptions',
		'Nette\Image' => '/common/Image',
		'Nette\InvalidArgumentException' => '/common/exceptions',
		'Nette\InvalidStateException' => '/common/exceptions',
		'Nette\Iterators\CachingIterator' => '/common/Iterators/CachingIterator',
		'Nette\Iterators\Filter' => '/common/Iterators/Filter',
		'Nette\Iterators\Mapper' => '/common/Iterators/Mapper',
		'Nette\Iterators\RecursiveFilter' => '/common/Iterators/RecursiveFilter',
		'Nette\Iterators\Recursor' => '/common/Iterators/Recursor',
		'Nette\Json' => '/common/Json',
		'Nette\JsonException' => '/common/Json',
		'Nette\Latte\CompileException' => '/Latte/exceptions',
		'Nette\LimitedScope' => '/common/LimitedScope',
		'Nette\Mail\SmtpException' => '/Mail/SmtpMailer',
		'Nette\MemberAccessException' => '/common/exceptions',
		'Nette\MimeTypeDetector' => '/common/MimeTypeDetector',
		'Nette\Neon' => '/common/Neon',
		'Nette\NeonEntity' => '/common/Neon',
		'Nette\NeonException' => '/common/Neon',
		'Nette\NotImplementedException' => '/common/exceptions',
		'Nette\NotSupportedException' => '/common/exceptions',
		'Nette\Object' => '/common/Object',
		'Nette\ObjectMixin' => '/common/ObjectMixin',
		'Nette\OutOfRangeException' => '/common/exceptions',
		'Nette\Paginator' => '/common/Paginator',
		'Nette\RegexpException' => '/common/Strings',
		'Nette\SafeStream' => '/common/SafeStream',
		'Nette\StaticClassException' => '/common/exceptions',
		'Nette\Strings' => '/common/Strings',
		'Nette\Tokenizer' => '/common/Tokenizer',
		'Nette\TokenizerException' => '/common/Tokenizer',
		'Nette\UnexpectedValueException' => '/common/exceptions',
		'Nette\UnknownImageFileException' => '/common/Image',
	);



	/**
	 * Returns singleton instance with lazy instantiation.
	 * @return NetteLoader
	 */
	public static function getInstance()
	{
		if (self::$instance === NULL) {
			self::$instance = new static;
		}
		return self::$instance;
	}



	/**
	 * Handles autoloading of classes or interfaces.
	 * @param  string
	 * @return void
	 */
	public function tryLoad($type)
	{
		$type = ltrim($type, '\\');
		/**/if (isset($this->renamed[$type])) {
			class_alias($this->renamed[$type], $type);
			trigger_error("Class $type has been renamed to {$this->renamed[$type]}.", E_USER_WARNING);

		} else/**/if (isset($this->list[$type])) {
			Nette\LimitedScope::load(NETTE_DIR . $this->list[$type] . '.php', TRUE);
			self::$count++;

		}/**/ elseif (substr($type, 0, 6) === 'Nette\\' && is_file($file = NETTE_DIR . strtr(substr($type, 5), '\\', '/') . '.php')) {
			Nette\LimitedScope::load($file, TRUE);
			self::$count++;
		}/**/
	}

}
