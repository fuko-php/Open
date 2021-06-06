<?php

namespace Fuko\Open\Tests;

use Fuko\Open\Editor;
use Fuko\Open\Sniff;
use PHPUnit\Framework\TestCase;

use function putenv;

class SniffTest extends TestCase
{
	/**
	* @covers Fuko\Open\Sniff::detect()
	* @covers Fuko\Open\Sniff::detectSublime()
	* @covers Fuko\Open\Sniff::addSniffer()
	* @covers Fuko\Open\Sniff::isEnvEditor()
	*/
	function testDetectSublime()
	{
		putenv("EDITOR=subl -w");
		$this->assertEquals(
			Sniff::detectSublime(),
			Editor::SUBLIME
			);
		$this->assertTrue(
			Sniff::isEnvEditor('subl -w')
			);

		$sniff = new Sniff([]);
		$sniff->addSniffer('\\Fuko\\Open\\Sniff::detectSublime');

		$this->assertEquals(
			$sniff->detect()->link('/var/www/html/index.html', 2),
			'subl://open?url=file://%2Fvar%2Fwww%2Fhtml%2Findex.html&line=2'
			);
	}

	/**
	* @covers Fuko\Open\Sniff::detect()
	* @covers Fuko\Open\Sniff::detectTextMate()
	* @covers Fuko\Open\Sniff::addSniffer()
	*/
	function testDetectTextMate()
	{
		putenv("EDITOR=mate -w");
		$this->assertEquals(
			Sniff::detectTextMate(),
			Editor::TEXTMATE
			);

		$sniff = new Sniff([]);
		$sniff->addSniffer('\\Fuko\\Open\\Sniff::detectTextMate');

		$this->assertEquals(
			$sniff->detect()->link('/var/www/html/index.html', 2),
			'txmt://open?url=file://%2Fvar%2Fwww%2Fhtml%2Findex.html&line=2'
			);
	}

	/**
	* @covers Fuko\Open\Sniff::addSniffer()
	* @covers Fuko\Open\Sniff::getSniffers()
	* @covers Fuko\Open\Sniff::clearSniffers()
	*/
	function testAddClearSniffers()
	{
		$sniff = new Sniff([]);

		$sniff->addSniffer('\\Fuko\\Open\\Sniff::detectAtom');
		$sniff->addSniffer('\\Fuko\\Open\\Sniff::detectSublime');
		$sniff->addSniffer('\\Fuko\\Open\\Sniff::detectTextMate');
		$sniff->addSniffer('\\Fuko\\Open\\Sniff::detectXDebug');

		$this->assertEquals(
			$sniff->getSniffers(),
			array(
			'\\Fuko\\Open\\Sniff::detectAtom',
			'\\Fuko\\Open\\Sniff::detectSublime',
			'\\Fuko\\Open\\Sniff::detectTextMate',
			'\\Fuko\\Open\\Sniff::detectXDebug',
		));

		$this->assertEquals(
			$sniff->clearSniffers()->getSniffers(),
			array()
		);
	}
}
