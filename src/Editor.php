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
* Editor\IDE Link
*
* This class also contains a list of known editors and their code reference formats
*
* @package Fuko\Open
*/
class Editor
{
	/**
	* "Blank" format that only contains the file and the line
	*/
	const BLANK = '%s:%d';

	/**
	* @var string {@link sprintf}-based format to use when generating
	*	the IDE\editor link, where the first placeholder "%s" is
	*	the filename, and the second one "%d" is the line
	*/
	protected $format = '';

	/**
	* Editor Constructor
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

	/**
	* Atom Editor
	* @link https://atom.io
	* @see https://github.com/atom/atom/pull/15935
	*/
	const ATOM = 'atom://core/open/file?filename=%s&line=%d';

	/**
	* GNU Emacs
	* @link https://www.gnu.org/software/emacs
	*/
	const EMACS = 'emacs://open?url=file://%s&line=%d';

	/**
	* Espresso
	* @link https://www.espressoapp.com
	*/
	const ESPRESSO = 'x-espresso://open?filepath=%s&lines=%d';

	/**
	* IntelliJ IDEA
	* @link https://www.jetbrains.com/idea
	*/
	const IDEA = 'idea://open?file=%s&line=%d';

	/**
	* Mac Vim
	* @link https://macvim-dev.github.io/macvim
	*/
	const MACVIM = 'mvim://open/?url=file://%s&line=%d';

	/**
	* Apache NetBeans
	* @link https://netbeans.apache.org
	*/
	const NETBEANS = 'netbeans://open/?f=%s:%d';

	/**
	* PhpStorm
	* @link https://www.jetbrains.com/phpstorm
	*/
	const PHPSTORM = 'phpstorm://open?file=%s&line=%d';

	/**
	* Sublime Text
	* @link http://www.sublimetext.com
	*/
	const SUBLIME = 'subl://open?url=file://%s&line=%d';

	/**
	* TextMate
	* @link https://macromates.com/manual/en
	* @see https://macromates.com/manual/en/using_textmate_from_terminal#url_scheme_html
	* @see https://macromates.com/blog/2007/the-textmate-url-scheme/
	*/
	const TEXTMATE = 'txmt://open?url=file://%s&line=%d';

	/**
	* Visual Studio Code
	* @link https://code.visualstudio.com
	* @see https://code.visualstudio.com/docs/editor/command-line#_opening-vs-code-with-urls
	*/
	const VSCODE = 'vscode://file/%s:%d';
}
