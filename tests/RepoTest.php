<?php

namespace Fuko\Open\Tests;

use Fuko\Open\Repo;
use PHPUnit\Framework\TestCase;

use function getcwd;

class RepoTest extends TestCase
{
	/**
	* @covers Fuko\Open\Repo::__construct()
	* @covers Fuko\Open\Repo::getLink()
	*/
	function testBitbucket()
	{
		$repo = new Repo(Repo::BITBUCKET,
			getcwd() . '/',
			'fuko-php', 'open', 'main'
			);

		$this->assertEquals(
			$repo->getLink()->link(__FILE__, 3),
			'https://bitbucket.org/fuko-php/open/src/main/tests%2FRepoTest.php#lines-3'
			);
	}

	/**
	* @covers Fuko\Open\Repo::__construct()
	* @covers Fuko\Open\Repo::getLink()
	*/
	function testGitHub()
	{
		$repo = new Repo(Repo::GITHUB,
			getcwd() . '/',
			'fuko-php', 'open', 'master'
			);

		$this->assertEquals(
			$repo->getLink()->link(__FILE__, 42),
			'https://github.com/fuko-php/open/blob/master/tests%2FRepoTest.php#L42'
			);
	}
}
