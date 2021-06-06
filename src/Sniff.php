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
use function get_cfg_var;
use function getenv;
use function ini_get;
use function is_file;
use function is_link;
use function shell_exec;
use function str_replace;

/**
* Sniff (detect) what IDE\editor is installed localy
*
* This class will help you detect what editor is installed locally, and use
* that to generate reference links to files from the source code
*
* @package Fuko\Open
*/
class Sniff
{
	/**
	* @var array default list of sniffers
	* @todo sort the sniffers based on the popularity of the IDEs\editors
	*/
	const DEFAULT = array(
		'\\Fuko\\Open\\Sniff::detectSublime',
		'\\Fuko\\Open\\Sniff::detectAtom',
		'\\Fuko\\Open\\Sniff::detectTextMate',
		'\\Fuko\\Open\\Sniff::detectXDebug',
		);

	/**
	* @var array list of sniffers
	*/
	protected $sniffers = array();

	/**
	* Constructor
	*
	* @param array $sniffers
	*/
	function __construct(array $sniffers = null)
	{
		if (null === $sniffers)
		{
			$sniffers = static::DEFAULT;
		}

		foreach ($sniffers as $sniffer)
		{
			$this->addSniffer($sniffer);
		}
	}

	/**
	* Add a new sniffer
	*
	* @param callable $sniffer
	* @param int $pos position, "-1" means append at the end, "0" is to prepend
	* @return self
	*/
	function addSniffer(callable $sniffer, int $pos = -1) : self
	{
		if ($pos < 0)
		{
			// negative position, add it at the end of the list
			//
			$this->sniffers[] = $sniffer;
		} else
		if ($pos >= ($count = count($this->sniffers)))
		{
			// out of range, add it at the end of the list
			//
			$this->sniffers[] = $sniffer;
		} else
		{
			// insert it at a specific position
			//
			$result = array_merge(
				array_slice($this->sniffers, 0, $pos),
				array($pos => $sniffer),
				array_slice(
					$this->sniffers, $pos,
					$count - $pos, true)
				);

			$this->sniffers = $result;
		}

		return $this;
	}

	function clearSniffers() : self
	{
		$this->sniffers = [];
		return $this;
	}

	/**
	* Get the list of current sniffers
	*
	* @return array
	*/
	function getSniffers() : array
	{
		return $this->sniffers;
	}

	/**
	* Run the sniffers and try to sniff what editor is installed
	*
	* @return null|\Fuko\Open\Editor
	*/
	function detect() :? Editor
	{
		foreach ($this->sniffers as $sniffer)
		{
			if ($format = $sniffer())
			{
				return new Editor($format);
			}
		}

		return null;
	}

	/**
	* Check $command against the EDITOR env
	*
	* @param string $command
	* @return boolean
	*/
	static function isEnvEditor(string $command) : bool
	{
		return getenv('EDITOR') == $command;
	}

	/**
	* Check whether the $command binary is available
	*
	* @param string $command
	* @return boolean
	*/
	static function isBin(string $command) : bool
	{
		if (DIRECTORY_SEPARATOR === '/') /* unix, linux, mac */
		{
			$bin = shell_exec("which {$command}");
			return trim($bin) ? true : false;
		}

		if (DIRECTORY_SEPARATOR === '\\') /* windows */
		{
			// split PATH and scan each folder ??!
		}

		return false;
	}

	/**
	* Detect if Atom is installed locally
	*
	* @return string empty string or the format to use
	*
	* @link https://flight-manual.atom.io/getting-started/sections/installing-atom/
	* @link https://discuss.atom.io/t/atom-does-not-appear-in-program-files-windows-10/31550
	*/
	static function detectAtom() : string
	{
		switch (true)
		{
			case self::isBin('atom'):

			case is_link('/usr/local/bin/atom') :
			case is_file('/usr/local/bin/atom') :

			// Mac
			//
			case is_file('/Applications/Atom.app/Contents/Resources/app/atom.sh'):

			// Windows: "Atom doesn't install into Program Files, it installs into %USER%\AppData\Local\atom\atom.exe"
			//
			case is_file('C:\\Users\\' . getenv('USER'). '\\AppData\\Local\\atom\\atom.exe'):

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
			case self::isEnvEditor('subl -w'):
			case self::isBin('subl'):

			case is_link('/usr/local/bin/subl') :
			case is_file('/usr/local/bin/subl') :

			// Mac
			//
			case is_file('/Applications/Sublime Text.app/Contents/SharedSupport/bin/subl'):

			// Linux
			//
			case is_file('/opt/sublime_text/sublime_text'):

			// Windows
			//
			case is_file('C:\\Program Files\\Sublime Text\\subl.exe'):
			case is_file('C:\\Program Files (x86)\\Sublime Text\\subl.exe'):

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
			case self::isEnvEditor('mate -w'):
			case self::isBin('mate'):

			case is_link('/usr/local/bin/mate') :
			case is_file('/usr/local/bin/mate') :

			// Mac
			//
			case is_file('/Applications/TextMate.app/Contents/Resources/mate'):

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
