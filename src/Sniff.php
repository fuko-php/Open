<?php /**
* @category Fuko
* @package Fuko\Open
*
* @author Kaloyan Tsvetkov (KT) <kaloyan@kaloyan.info>
* @link https://github.com/fuko-php/open/
* @license https://opensource.org/licenses/MIT
*/

namespace Fuko\Open;

use \Fuko\Open\Editor;

use const DIRECTORY_SEPARATOR;

use function array_key_exists;
use function array_merge;
use function array_slice;
use function count;
use function file_exists;
use function get_cfg_var;
use function getenv;
use function ini_get;
use function shell_exec;

/**
* Detect what IDE\editor is installed localy
*
* This class will help you detect what editor is installed locally, and use
* that to generate reference links to files from the source code 
*
* @package Fuko\Open
*/
class Sniff
{
	/**
	* @var array list of detector callbacks to check whether certain editors are available
	*/
	static protected $detectors = array(
		'\\Fuko\\Open\\Sniff::detectAtom',
		'\\Fuko\\Open\\Sniff::detectSublime',
		'\\Fuko\\Open\\Sniff::detectTextMate',
		'\\Fuko\\Open\\Sniff::detectXDebug',
		);

	/**
	* Add a new detector
	*
	* @param callable $detector
	* @param int $pos position, "-1" means append at the end, "0" is to prepend
	*/
	static function addDetector(callable $detector, int $pos = -1)
	{
		if ($pos < 0)
		{
			// negative position, add it at the end of the list
			//
			$this->detectors[] = $detector;
		} else
		if ($pos >= ($count = count($this->detectors)))
		{
			// out of range, add it at the end of the list
			//
			$this->detectors[] = $detector;
		} else
		{
			// insert it at a specific position
			//
			$result = array_merge(
				array_slice($this->detectors, 0, $pos),
				array($pos => $detector),
				array_slice(
					$this->detectors, $pos,
					$count - $pos, true)
				);

			$this->detectors = $result;
		}
	}

	static function dropDetector(int $pos) : bool
	{
		if (!isset(self::$detectors[ $pos ]))
		{
			return false;
		}

		unset(self::$detectors[ $pos ]);
		return true;
	}

	/**
	* Get the list of current detectors
	*
	* @return array
	*/
	static function getDetectors() : array
	{
		return self::$detectors;
	}

	/**
	* Run the detectors and try to sniff what editor is installed
	*
	* @return null|\Fuko\Open\Editor
	*/
	static function detect() :? Editor
	{
		foreach (self::$detectors as $detector)
		{
			if ($format = $detector())
			{
				return new Editor($format);
			}
		}

		return null;
	}

	/**
	* Check against the EDITOR env
	*
	* @param string $match
	* @return boolean
	*/
	private static function isEditor(string $match) : bool
	{
		return getenv('EDITOR') == $match;
	}

	/**
	* Check whether the binary is availables
	*
	* @param string $match
	* @return boolean
	*/
	private static function isBin(string $match) : bool
	{
		if (DIRECTORY_SEPARATOR === '/') /* unix, linux, mac */
		{
			$bin = shell_exec("which {$match}");
			return trim($bin) ? true : false;
		}

		if (DIRECTORY_SEPARATOR === '\\') /* windows */
		{
			// split PATH and scan each folder ??!
		}

		return false;
	}

	/**
	* Check whether provided list of files has a match
	*
	* @param string ...$files
	* @return boolean
	*/
	private static function filesExists(...$files) : bool
	{
		foreach ($files as $file)
		{
			if (file_exists($file))
			{
				return true;
			}
		}

		return false;
	}

	/**
	* Detect if Atom is installed locally
	*
	* @return string empty string or the format to use
	* @link https://flight-manual.atom.io/getting-started/sections/installing-atom/
	*/
	static function detectAtom() : string
	{
		switch (true)
		{
			case self::isBin('atom'):
			case self::filesExists(
				'/usr/local/bin/atom',
				/* Mac */ '/Applications/Atom.app/Contents/Resources/app/atom.sh'
				/* Linux */ // ?
				/* Win */ // ?
				):
				return Editor::ATOM;
				break;
		}

		return '';
	}

	/**
	* Detect if Sublime is installed locally
	*
	* @return string empty string or the format to use
	* @link http://www.sublimetext.com/docs/command_line.html#editor
	*/
	static function detectSublime() : string
	{
		switch (true)
		{
			case self::isEditor('subl -w'):
			case self::isBin('subl'):
			case self::filesExists(
				'/usr/local/bin/subl',
				/* Mac */ '/Applications/Sublime Text.app/Contents/SharedSupport/bin/subl',
				/* Linux */ '/opt/sublime_text/sublime_text',
				/* Win */ 'C:\\Program Files\\Sublime Text\\subl.exe',
				/* Win */ 'C:\\Program Files (x86)\\Sublime Text\\subl.exe'
				):
				return Editor::SUBLIME;
				break;
		}

		return '';
	}

	/**
	* Detect if TextMate is installed locally
	*
	* @return string empty string or the format to use
	* @link https://macromates.com/manual/en/using_textmate_from_terminal
	*/
	static function detectTextMate() : string
	{
		switch (true)
		{
			case self::isEditor('mate -w'):
			case self::isBin('mate'):
			case self::filesExists(
				'/usr/local/bin/mate',
				/* Mac */ '/Applications/TextMate.app/Contents/Resources/mate'
				):
				return Editor::TEXTMATE;
				break;
		}

		return '';
	}

	/**
	* Detect if XDebug is available to open source files
	*
	* @return string empty string or the format to use
	* @link https://xdebug.org/docs/all_settings#file_link_format
	*/
	static function detectXDebug() : string
	{
		$format = ini_get('xdebug.file_link_format')
			?: get_cfg_var('xdebug.file_link_format');

		if ($format)
		{
			return str_replace(['%f', '%l'], ['%s', '%d'], $format);
		}

		return '';
	}
}
