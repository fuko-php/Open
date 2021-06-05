<?php

namespace Fuko\Open\Tests;

use Fuko\Open\Editor;
use Fuko\Open\Sniff;
use PHPUnit\Framework\TestCase;

use function array_values;
use function putenv;

class SniffTest extends TestCase
{
	protected $detectors = [];

	protected function clearDetectors()
	{
		$detectors = Sniff::getDetectors();
		foreach ($detectors as $pos => $dummy)
		{
			Sniff::dropDetector($pos);
		}
	}

	function setUp()
	{
		$this->detectors = Sniff::getDetectors();
		$this->clearDetectors();
	}

	function tearDown()
	{
		$this->clearDetectors();
		foreach ($this->detectors as $detector)
		{
			Sniff::addDetector($detector);
		}
	}

	/**
	* @covers Fuko\Open\Sniff::detect()
	* @covers Fuko\Open\Sniff::detectSublime()
	* @covers Fuko\Open\Sniff::addDetector()
	*/
	function testDetectSublime()
	{
		putenv("EDITOR=subl -w");
		Sniff::addDetector('\\Fuko\\Open\\Sniff::detectSublime');

		$this->assertEquals(
			Sniff::detectSublime(),
			Editor::SUBLIME
			);

		$this->assertEquals(
			(Sniff::detect())->link('/var/www/html/index.html', 2),
			'subl://open?url=file://%2Fvar%2Fwww%2Fhtml%2Findex.html&line=2'
			);
	}

	/**
	* @covers Fuko\Open\Sniff::detect()
	* @covers Fuko\Open\Sniff::detectTextMate()
	* @covers Fuko\Open\Sniff::addDetector()
	*/
	function testDetectTextMate()
	{
		putenv("EDITOR=mate -w");
		Sniff::addDetector('\\Fuko\\Open\\Sniff::detectTextMate');

		$this->assertEquals(
			Sniff::detectTextMate(),
			Editor::TEXTMATE
			);

		$this->assertEquals(
			(Sniff::detect())->link('/var/www/html/index.html', 2),
			'txmt://open?url=file://%2Fvar%2Fwww%2Fhtml%2Findex.html&line=2'
			);
	}

	/**
	* @covers Fuko\Open\Sniff::addDetector()
	* @covers Fuko\Open\Sniff::getDetectors()
	* @covers Fuko\Open\Sniff::dropDetector()
	*/
	function testDetectors()
	{
		Sniff::addDetector('\\Fuko\\Open\\Sniff::detectAtom');
		Sniff::addDetector('\\Fuko\\Open\\Sniff::detectSublime');
		Sniff::addDetector('\\Fuko\\Open\\Sniff::detectTextMate');
		Sniff::addDetector('\\Fuko\\Open\\Sniff::detectXDebug');

		$detectors = Sniff::getDetectors();
		$this->assertEquals(
			array_values( $detectors ),
			array(
			'\\Fuko\\Open\\Sniff::detectAtom',
			'\\Fuko\\Open\\Sniff::detectSublime',
			'\\Fuko\\Open\\Sniff::detectTextMate',
			'\\Fuko\\Open\\Sniff::detectXDebug',
		));

		$this->clearDetectors();
		$this->assertEquals(
			Sniff::getDetectors(),
			array()
		);
	}
}
