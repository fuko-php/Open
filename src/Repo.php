<?php /**
* @category Fuko
* @package Fuko\Open
*
* @author Kaloyan Tsvetkov (KT) <kaloyan@kaloyan.info>
* @link https://github.com/fuko-php/open/
* @license https://opensource.org/licenses/MIT
*/

namespace Fuko\Open;

use Fuko\Open\Link;

use sprintf;

/**
* Online Source Code Repository
*
* Use this class to create links to source code on online code repos.
*
* @package Fuko\Open
*/
class Repo
{
	/**
	* @var string the link format using all of the repo link
	*	components (workspace, repo and ref) plus the code
	*	reference (file and line)
	*/
	protected $format = '';

	/**
	* @var string root folder of the repository, this will be used to
	*	"translate" the local files to links on the repo
	*/
	protected $folder = '';

	/**
	* @var string workspace, aka project or account, e.g. "fuko-php"
	*	for https://github.com/fuko-php
	*/
	protected $workspace = '';

	/**
	* @var string repository from the provided workspace, e.g. "open"
	*	for https://github.com/fuko-php/open
	*/
	protected $repository = '';

	/**
	* @var string branch, tag or commit from the selected repository,
	*	e.g. "master", or "main", or "5.x" or whatever
	*/
	protected $ref = '';

	function __construct(
		string $format,
		string $folder,

		string $workspace,
		string $repository,
		string $ref)
	{
		$this->format = $format;
		$this->folder = $folder;

		$this->workspace = $workspace;
		$this->repository = $repository;
		$this->ref = $ref;
	}

	function getLink() : Link
	{
		$format = sprintf($this->format,
			$this->workspace,
			$this->repository,
			$this->ref
			);

		return (new Link($format))->addPrefix($this->folder, '');
	}

	/**
	* Bitbucket Cloud
	* format: "https://bitbucket.org/{$workspace}/{$repository}/src/{$ref}/{$file}#lines-{$line}"
	*/
	const BITBUCKET = 'https://bitbucket.org/%s/%s/src/%s/%%s#lines-%%d';

	/**
	* GitHub
	* format: "https://github.com/{$workspace}/{$repository}/blob/{$ref}/{$file}#L{$line}"
	*/
	const GITHUB = 'https://github.com/%s/%s/blob/%s/%%s#L%%d';
}
