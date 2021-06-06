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
	* @var string website hosting the repos, e.g. "https://github.com"
	*	or "https://bitbucket.org"
	*/
	protected $website = '';

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
		string $folder,
		string $workspace,
		string $repository,
		string $ref,
		string $website = null)
	{
		$this->folder = $folder;

		$this->workspace = $workspace;
		$this->repository = $repository;
		$this->ref = $ref;

		if (null !== $website)
		{
			$this->website = $website;
		}
	}



	function useFormat(string $format) : self
	{
		$this->format = $format;
		return $this;
	}

	function getLink() : Link
	{
		$format = sprintf($this->format,
			$this->website,
			$this->workspace,
			$this->repository,
			$this->ref
			);

		return (new Link($format))->addPrefix($folder, '');
	}
}
