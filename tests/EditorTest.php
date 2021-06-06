<?php

namespace Fuko\Open\Tests;

use Fuko\Open\Editor;
use Fuko\Open\Link;
use PHPUnit\Framework\TestCase;

class EditorTest extends TestCase
{
	function testAtom()
	{
		$link = new Link(Editor::ATOM);
		$this->assertEquals(
			$link->link('a/b/c.d', 12),
			'atom://core/open/file?filename=a%2Fb%2Fc.d&line=12'
			);
	}

	function testEmacs()
	{
		$link = new Link(Editor::EMACS);
		$this->assertEquals(
			$link->link('a/b/c.d', 12),
			'emacs://open?url=file://a%2Fb%2Fc.d&line=12'
			);
	}

	function testEspresso()
	{
		$link = new Link(Editor::ESPRESSO);
		$this->assertEquals(
			$link->link('a/b/c.d', 12),
			'x-espresso://open?filepath=a%2Fb%2Fc.d&lines=12'
			);
	}

	function testIdea()
	{
		$link = new Link(Editor::IDEA);
		$this->assertEquals(
			$link->link('a/b/c.d', 12),
			'idea://open?file=a%2Fb%2Fc.d&line=12'
			);
	}

	function testMacVim()
	{
		$link = new Link(Editor::MACVIM);
		$this->assertEquals(
			$link->link('a/b/c.d', 12),
			'mvim://open/?url=file://a%2Fb%2Fc.d&line=12'
			);
	}

	function testNetbeans()
	{
		$link = new Link(Editor::NETBEANS);
		$this->assertEquals(
			$link->link('a/b/c.d', 12),
			'netbeans://open/?f=a%2Fb%2Fc.d:12'
			);
	}


	function testPhpStorm()
	{
		$link = new Link(Editor::PHPSTORM);
		$this->assertEquals(
			$link->link('a/b/c.d', 12),
			'phpstorm://open?file=a%2Fb%2Fc.d&line=12'
			);
	}

	function testSublimeText()
	{
		$link = new Link(Editor::SUBLIME);
		$this->assertEquals(
			$link->link('a/b/c.d', 12),
			'subl://open?url=file://a%2Fb%2Fc.d&line=12'
			);
	}

	function testTextMate()
	{
		$link = new Link(Editor::TEXTMATE);
		$this->assertEquals(
			$link->link('a/b/c.d', 12),
			'txmt://open?url=file://a%2Fb%2Fc.d&line=12'
			);
	}

	function testVisualStudioCode()
	{
		$link = new Link(Editor::VSCODE);
		$this->assertEquals(
			$link->link('a/b/c.d', 12),
			'vscode://file/a%2Fb%2Fc.d:12'
			);
	}
}
