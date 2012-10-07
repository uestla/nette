<?php

/**
 * This file is part of the Nette Framework (http://nette.org)
 *
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Nette\Diagnostics;

use Nette;



/**
 * Debug Bar.
 *
 * @author     David Grudl
 */
class Bar extends Nette\Object
{
	/** @var array */
	private $panels = array();

	private $handle;



	public function init()
	{
		if (isset($_GET['_nette_debug']) && preg_match('#^\w+$#', $_GET['_nette_debug'])) {
			$file = $this->getFileName($_GET['_nette_debug']);
			header('Content-Type: text/javascript');
			$html = @file_get_contents($file);
			if ($html) {
				echo 'Nette.Debug.newContent(' . json_encode(Nette\Utils\Strings::fixEncoding($html)) . ');';
				unlink($file);
			}
			exit;
		}
		$id = uniqid();
		$this->handle = fopen($this->getFileName($id), 'w');
		flock($this->handle, LOCK_EX);
		setCookie('ajax', $id, 0, '/');
	}



	public function getFileName($id)
	{
		return Debugger::$logDirectory . "/data.$id.dat";
	}



	/**
	 * Add custom panel.
	 * @param  IBarPanel
	 * @param  string
	 * @return Bar  provides a fluent interface
	 */
	public function addPanel(IBarPanel $panel, $id = NULL)
	{
		if ($id === NULL) {
			$c = 0;
			do {
				$id = get_class($panel) . ($c++ ? "-$c" : '');
			} while (isset($this->panels[$id]));
		}
		$this->panels[$id] = $panel;
		return $this;
	}



	/**
	 * Returns panel with given id
	 * @param  string
	 * @return IBarPanel|NULL
	 */
	public function getPanel($id)
	{
		return isset($this->panels[$id]) ? $this->panels[$id] : NULL;
	}



	/**
	 * Renders debug bar.
	 * @return void
	 */
	public function render()
	{
		if (!$this->handle) {
			return;
		}
		$obLevel = ob_get_level();
		$panels = array();
		foreach ($this->panels as $id => $panel) {
			try {
				$panels[] = array(
					'id' => preg_replace('#[^a-z0-9]+#i', '-', $id),
					'tab' => $tab = (string) $panel->getTab(),
					'panel' => $tab ? (string) $panel->getPanel() : NULL,
				);
			} catch (\Exception $e) {
				$panels[] = array(
					'id' => "error-" . preg_replace('#[^a-z0-9]+#i', '-', $id),
					'tab' => "Error in $id",
					'panel' => '<h1>Error: ' . $id . '</h1><div class="nette-inner">' . nl2br(htmlSpecialChars($e)) . '</div>',
				);
				while (ob_get_level() > $obLevel) { // restore ob-level if broken
					ob_end_clean();
				}
			}
		}

		@session_start();
		$session = & $_SESSION['__NF']['debuggerbar'];
		if (preg_match('#^Location:#im', implode("\n", headers_list()))) {
			$session[] = $panels;
			return;
		}

		foreach (array_reverse((array) $session) as $reqId => $oldpanels) {
			$panels[] = array(
				'tab' => '<span title="Previous request before redirect">previous</span>',
				'panel' => NULL,
				'previous' => TRUE,
			);
			foreach ($oldpanels as $panel) {
				$panel['id'] .= '-' . $reqId;
				$panels[] = $panel;
			}
		}
		$session = NULL;
		ob_start();
		require __DIR__ . '/templates/bar.content.phtml';
		fwrite($this->handle, ob_get_clean());
		fclose($this->handle);

		if (preg_match('#^Content-Type: text/html#im', implode("\n", headers_list())) && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			require __DIR__ . '/templates/bar.phtml';
		}
	}

}
