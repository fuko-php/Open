<?php

namespace Fuko\Open\Tests;

use Fuko\Open\Editor;
use Fuko\Open\Link;
use PHPUnit\Framework\TestCase;

use function getcwd;

class LinkTest extends TestCase
{
	/**
	* @covers Fuko\Open\Link::__construct()
	*/
	function testBlankLinkFormat()
	{
		$link = new Link(Editor::BLANK);
		$this->assertEquals( $link->link('a', 1), 'a:1');
	}

	/**
	* @covers Fuko\Open\Link::addPrefix()
	* @covers Fuko\Open\Link::getPrefixes()
	* @covers Fuko\Open\Link::clearPrefixes()
	*/
	function testLinkPrefixes()
	{
		$link = new Link(Editor::BLANK);
		$link->addPrefix($cwd = getcwd() . '/', '/upside/down/');

		$this->assertEquals(
			$link->getPrefixes(),
			array([$cwd, '/upside/down/'])
		);

		$this->assertEquals(
			$link->clearPrefixes()->getPrefixes(),
			array()
		);
	}

	/**
	* @covers Fuko\Open\Link::addPrefix()
	* @covers Fuko\Open\Link::link()
	*/
	function testLinkPrefix()
	{
		$link = new Link(Editor::BLANK);
		$link->addPrefix($cwd = getcwd() . '/', '/upside/down/');

		$this->assertEquals(
			$link->link(__FILE__, 42),
			'%2Fupside%2Fdown%2Ftests%2FLinkTest.php:42'
		);

		$link->clearPrefixes()->addPrefix($cwd = getcwd() . '/', '/');

		$this->assertEquals(
			$link->link(__FILE__, 42),
			'%2Ftests%2FLinkTest.php:42'
		);
	}
}
