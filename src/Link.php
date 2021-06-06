<?php /**
* @category Fuko
* @package Fuko\Open
*
* @author Kaloyan Tsvetkov (KT) <kaloyan@kaloyan.info>
* @link https://github.com/fuko-php/open/
* @license https://opensource.org/licenses/MIT
*/

namespace Fuko\Open;

use function rawurlencode;
use function sprintf;
use function strlen;
use function strpos;
use function substr;

/**
* Code Reference Link
*
* Use this class to create links to code references that consist of filename and line
*
* @package Fuko\Open
*/
class Link
{
	/**
	* @var string {@link sprintf()}-based format to use
	*	when generating the link, where the first
	*	placeholder "%s" is the filename, and the
	*	second one "%d" is the line
	*/
	protected $format = '';

	/**
	* Constructor
	*
	* @param string $format link format
	*/
	function __construct(string $format)
	{
		$this->format = $format;
	}

	/**
	* @var array two-elements arrays that will be used to replace
	*	leading prefixes of filenames
	*/
	protected $prefix = array();

	/**
	* Add new filename prefix to replace
	*
	* @param string $prefix
	* @param string $replace
	* @return self
	*/
	function addPrefix(string $prefix, string $replace) : self
	{
		$this->prefix[] = array($prefix, $replace);
		return $this;
	}

	/**
	* Clear all prefixes
	*
	* @return self
	*/
	function clearPrefixes() : self
	{
		$this->prefix = array();
		return $this;
	}

	/**
	* Get all existing prefixes
	*
	* @return array
	*/
	function getPrefixes() : array
	{
		return $this->prefix;
	}

	/**
	* Generate the link to use for the provided $file and $line
	*
	* @param string $file
	* @param int $line
	* @return string
	*/
	function link(string $file, int $line) : string
	{
		$source_file = $file;
		foreach ($this->prefix as $prefix)
		{
			if (0 === strpos($source_file, $prefix[0]))
			{
				$source_file = $prefix[1] . substr(
					$source_file,
					strlen($prefix[0])
					);
				break;
			}
		}

		return sprintf($this->format, rawurlencode($source_file), $line);
	}
}
