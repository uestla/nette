/**
 * Debugger Bar
 *
 * This file is part of the Nette Framework (http://nette.org)
 * Copyright (c) 2004 David Grudl (http://davidgrudl.com)
 */

/// <reference path="netteQ.ts"/>

module Nette.Debug {

	var $ = Nette.Query.factory;

    export class Panel {

		static PEEK = 'nette-mode-peek';
		static FLOAT = 'nette-mode-float';
		static WINDOW = 'nette-mode-window';
		static FOCUSED = 'nette-focused';
		static zIndex = 20000;

		static get(id) {
			return new Panel(id);
		}

		private static toggle(link) {
			var rel = link.rel, el = rel.charAt(0) === '#' ? $(rel) : $(link).next(rel.substring(4));
			if (el.css('display') === 'none') {
				el.show(); link.innerHTML = link.innerHTML.replace("\u25ba", "\u25bc");
			} else {
				el.hide(); link.innerHTML = link.innerHTML.replace("\u25bc", "\u25ba");
			}
		}

		id: string;
		elem: Nette.Query;

		constructor(id) {
			this.id = 'nette-debug-panel-' + id;
			this.elem = $('#' + this.id);
		}

		init() {
			this.elem.data().onmove = (coords) => {
				this.moveConstrains(coords);
			}

			$(window).bind('resize', () => {
				this.reposition();
			});

			this.elem.draggable({
				rightEdge: true,
				bottomEdge: true,
				handle: this.elem.find('h1'),
				stop: () => {
					this.toFloat();
				}

			}).bind('mouseenter', () => {
				this.focus();

			}).bind('mouseleave', () => {
				this.blur();
			});

			this.initToggler();

			var _this = this;
			this.elem.find('.nette-icons').find('a').bind('click', function(e) {
				if (this.rel === 'close') {
					_this.toPeek();
				} else {
					_this.toWindow();
				}
				e.preventDefault();
			});

			this.restorePosition();
		}

		is(mode) {
			return this.elem.hasClass(mode);
		}

		focus() {
			var elem = this.elem;
			if (this.is(Panel.WINDOW)) {
				elem.data().win.focus();
			} else {
				clearTimeout(elem.data().blurTimeout);
				elem.addClass(Panel.FOCUSED).show();
				elem[0].style.zIndex = Panel.zIndex++;
			}
		}

		blur() {
			var elem = this.elem;
			elem.removeClass(Panel.FOCUSED);
			if (this.is(Panel.PEEK)) {
				elem.data().blurTimeout = setTimeout(() => { elem.hide() }, 50);
			}
		}

		toFloat() {
			this.elem.removeClass(Panel.WINDOW).
				removeClass(Panel.PEEK).
				addClass(Panel.FLOAT).
				show();
			this.reposition();
		}

		toPeek() {
			this.elem.removeClass(Panel.WINDOW).
				removeClass(Panel.FLOAT).
				addClass(Panel.PEEK).
				hide();
			document.cookie = this.id + '=; path=/'; // delete position
		}

		toWindow() {
			var offset = this.elem.offset();
			offset.left += typeof window.screenLeft === 'number' ? window.screenLeft : (window.screenX + 10);
			offset.top += typeof window.screenTop === 'number' ? window.screenTop : (window.screenY + 50);

			var win = window.open('', this.id.replace(/-/g, '_'), 'left='+offset.left+',top='+offset.top+',width='+offset.width+',height='+(offset.height+15)+',resizable=yes,scrollbars=yes');
			if (!win) {
				return;
			}

			var doc = win.document;
			doc.write('<!DOCTYPE html><meta http-equiv="Content-Type" content="text\/html; charset=utf-8"><style>' + $('#nette-debug-style').dom().innerHTML + '<\/style><script>' + $('#nette-debug-script').dom().innerHTML + '<\/script><body id="nette-debug">');
			doc.body.innerHTML = '<div class="nette-panel nette-mode-window" id="' + this.id + '">' + this.elem.dom().innerHTML + '<\/div>';
			var winPanel = win.Nette.DebugPanel.get(this.id.replace('nette-debug-panel-', ''));
			winPanel.initToggler();
			winPanel.reposition();
			doc.title = this.elem.find('h1').dom().innerHTML;

			$([win]).bind('unload', () => {
				this.toPeek();
				win.close(); // forces closing, can be invoked by F5
			});

			$(doc).bind('keyup', (e) => {
				if (e.keyCode === 27 && !e.shiftKey && !e.altKey && !e.ctrlKey && !e.metaKey) {
					win.close();
				}
			});

			document.cookie = this.id + '=window; path=/'; // save position
			this.elem.hide().
				removeClass(Panel.FLOAT).
				removeClass(Panel.PEEK).
				addClass(Panel.WINDOW).
				data().win = win;
		}

		reposition() {
			if (this.is(Panel.WINDOW)) {
				var dE = document.documentElement;
				window.resizeBy(dE.scrollWidth - dE.clientWidth, dE.scrollHeight - dE.clientHeight);
			} else {
				var pos = this.elem.position();
				if (pos.width) { // is visible?
					this.elem.position({right: pos.right, bottom: pos.bottom});
					document.cookie = this.id + '=' + pos.right + ':' + pos.bottom + '; path=/';
				}
			}
		}

		moveConstrains(coords) { // forces constrained inside window
			var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth,
				height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight,
				el = this.elem[0];
			coords.right = Math.min(Math.max(coords.right, -.2 * el.offsetWidth), width - .8 * el.offsetWidth);
			coords.bottom = Math.min(Math.max(coords.bottom, -.2 * el.offsetHeight), height - el.offsetHeight);
		}

		initToggler() { // enable <a rel="..."> togglers
			this.elem.bind('click', (e) => {
				var $link = $(e.target).closest('a'), link = $link.dom();
				if (link && link.rel) {
					Panel.toggle(link);
					e.preventDefault();
					this.reposition();
				}
			});
		}

		restorePosition() {
			var m = document.cookie.match(new RegExp(this.id + '=(window|(-?[0-9]+):(-?[0-9]+))'));
			if (m && m[2]) {
				this.toFloat();
				this.elem.position({right: m[2], bottom: m[3]});
			} else if (m) {
				this.toWindow();
			} else {
				this.elem.addClass(Panel.PEEK);
			}
		}

	}




    export class Bar {

		id = 'nette-debug-bar';

		init() {
			var elem = $('#' + this.id);

			elem.data().onmove = (coords) => {
				this.moveConstrains(coords);
			}

			$(window).bind('resize', () => {
				elem.position({right: elem.position().right, bottom: elem.position().bottom});
			});

			elem.draggable({
				rightEdge: true,
				bottomEdge: true,
				draggedClass: 'nette-dragged',
				stop: () => {
					this.savePosition();
				}
			});

			var _this = this;
			elem.find('a').bind('click', function(e) {
				if (this.rel === 'close') {
					_this.close();

				} else if (this.rel) {
					var panel = Panel.get(this.rel);
					if (e.shiftKey) {
						panel.toFloat();
						panel.toWindow();

					} else if (panel.is(Panel.FLOAT)) {
						panel.toPeek();

					} else {
						panel.toFloat();
						panel.elem.position({
							right: panel.elem.position().right + Math.round(Math.random() * 100) + 20,
							bottom: panel.elem.position().bottom + Math.round(Math.random() * 100) + 20
						});
					}
				}
				e.preventDefault();

			}).bind('mouseenter', function(e) {
				if (this.rel && this.rel !== 'close' && !elem.hasClass('nette-dragged')) {
					var panel = Panel.get(this.rel), link = $(this);
					panel.focus();
					if (panel.is(Panel.PEEK)) {
						panel.elem.position({
							right: panel.elem.position().right - link.offset().left + panel.elem.position().width - link.position().width - 4 + panel.elem.offset().left,
							bottom: panel.elem.position().bottom - elem.offset().top + panel.elem.position().height + 4 + panel.elem.offset().top
						});
					}
				}

			}).bind('mouseleave', function(e) {
				if (this.rel && this.rel !== 'close' && !elem.hasClass('nette-dragged')) {
					Panel.get(this.rel).blur();
				}
			});

			this.restorePosition();

			elem.find('a').each(function() {
				if (this.rel && this.rel !== 'close') {
					Panel.get(this.rel).init();
				}
			});
		}

		close() {
			$('#nette-debug').hide();
			if (window.opera) {
				$('body').show();
			}
		}

		moveConstrains(coords) { // forces constrained inside window
			var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth,
				height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight,
				el = $('#' + this.id)[0];
			coords.right = Math.min(Math.max(coords.right, 0), width - el.offsetWidth);
			coords.bottom = Math.min(Math.max(coords.bottom, 0), height - el.offsetHeight);
		}

		savePosition() {
			var pos = $('#' + this.id).position();
			document.cookie = this.id + '=' + pos.right + ':' + pos.bottom + '; path=/';
		}

		restorePosition() {
			var m = document.cookie.match(new RegExp(this.id + '=(-?[0-9]+):(-?[0-9]+)'));
			if (m) {
				$('#' + this.id).position({right: m[1], bottom: m[2]});
			}
		}

	}

}