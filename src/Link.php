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
	* Generate the link to use for the provided $file and $line
	*
	* @param string $file
	* @param int $line
	* @return string
	*/
	function link(string $file, int $line) : string
	{
		return sprintf($this->format, rawurlencode($file), $line);
	}
}
