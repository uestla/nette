<?php

/**
 * Test: Nette\Mail\Message invalid email addresses.
 *
 * @author     David Grudl
 * @package    Nette\Mail
 */

use Nette\Mail\Message;



require __DIR__ . '/../bootstrap.php';



$mail = new Message();

Assert::throws(function() use ($mail) {
	// From
	$mail->setFrom('John Doe <doe@example. com>');
}, 'Nette\AssertionException', "The header 'From' expects to be email, string 'doe@example. com' given.");


Assert::throws(function() use ($mail) {
	$mail->setFrom('John Doe <>');
}, 'Nette\AssertionException', "The header 'From' expects to be email, string '' given.");


Assert::throws(function() use ($mail) {
	$mail->setFrom('John Doe <doe@examplecom>');
}, 'Nette\AssertionException', "The header 'From' expects to be email, string 'doe@examplecom' given.");


Assert::throws(function() use ($mail) {
	$mail->setFrom('John Doe');
}, 'Nette\AssertionException', "The header 'From' expects to be email, string 'John Doe' given.");


Assert::throws(function() use ($mail) {
	$mail->setFrom('doe;@examplecom');
}, 'Nette\AssertionException', "The header 'From' expects to be email, string 'doe;@examplecom' given.");


Assert::throws(function() use ($mail) {
	// addReplyTo
	$mail->addReplyTo('@');
}, 'Nette\AssertionException', "The header 'Reply-To' expects to be email, string '@' given.");


Assert::throws(function() use ($mail) {
	// addTo
	$mail->addTo('@');
}, 'Nette\AssertionException', "The header 'To' expects to be email, string '@' given.");


Assert::throws(function() use ($mail) {
	// addCc
	$mail->addCc('@');
}, 'Nette\AssertionException', "The header 'Cc' expects to be email, string '@' given.");


Assert::throws(function() use ($mail) {
	// addBcc
	$mail->addBcc('@');
}, 'Nette\AssertionException', "The header 'Bcc' expects to be email, string '@' given.");
