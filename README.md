# Fuko\\Open [![Latest Version](http://img.shields.io/packagist/v/fuko-php/open.svg)](https://packagist.org/packages/fuko-php/open) [![GitHub license](https://img.shields.io/github/license/fuko-php/open.svg)](https://github.com/fuko-php/open/blob/master/LICENSE)

**Fuko\\Open** is a small PHP library that helps you to generate links for
opening referenced files directly in your IDE or editor, or have it linked to
an online repository.

# Editor Links

There are several IDEs and editors that support special URL format for local
files with the purpose to allow them to open them directly. This feature will
only work if you are running your code locally, so that your source code files
are accessible to the editor.

To generate such URLs you must use the format associated with that editor:
```php
include __DIR__ . '/vendor/autoload.php';
use \Fuko\Open\Editor;
use \Fuko\Open\Link;

/* I have Atom installed locally, so this is what I want to use */
$editor = new Link(Editor::ATOM);
```
Once you have created the `\Fuko\Open\Link` object, you call its `link()` method
to get the generated and formatted URL:
```php
echo $editor->link('/var/www/html/index.html', 2);
// atom://core/open/file?filename=%2Fvar%2Fwww%2Fhtml%2Findex.html&line=2
```
The `\Fuko\Open\Link::link()` method is also called if you do `\Fuko\Open\Link::__invoke()`, so
you can also just do this:
```php
echo $editor('/var/www/html/index.html', 2);
```

# Editor Sniff

You can *sniff* what editor is installed locally by using `\Fuko\Open\Sniff::detect()`. It
will either return a new `\Fuko\Open\Link` object with the format setup inside it to to
use, or if nothing is found it will return `NULL`.

```php
include __DIR__ . '/vendor/autoload.php';
use \Fuko\Open\Sniff;

/* I have Atom installed locally, so this is how you can detect it */
$editor = (new Sniff)->detect();
if ($editor)
{
	echo $editor('/var/www/html/index.html', 2);
	// atom://core/open/file?filename=%2Fvar%2Fwww%2Fhtml%2Findex.html&line=2
}
```

The sniffing is done using "sniffer" functions/methods. There are some that are built-in,
but you can add your own using `\Fuko\Open\Sniff::addSniffer()`. The sniffers must
return either the format to use in the `\Fuko\Open\Link` constructor, or an empty string if
there is no match.

```php
$sniff->addSniffer(function()
{
	return getenv('EDITOR') === 'subl -w'
		? \Fuko\Open\Editor::SUBLIME
		: '';
});
```

# Supported Editors

This is the list of the IDEs and editors supported by **Fuko\\Open**

| Editor                                              | Format Const                  |
|-----------------------------------------------------|-------------------------------|
| [Atom](https://atom.io)                             | `\Fuko\Open\Editor::ATOM`     |
| [GNU Emacs](https://www.gnu.org/software/emacs)     | `\Fuko\Open\Editor::EMACS`    |
| [Espresso](https://www.espressoapp.com)             | `\Fuko\Open\Editor::ESPRESSO` |
| [IntelliJ IDEA](https://www.jetbrains.com/idea)     | `\Fuko\Open\Editor::IDEA`     |
| [Mac Vim](https://macvim-dev.github.io/macvim)      | `\Fuko\Open\Editor::MACVIM`   |
| [Apache NetBeans](https://netbeans.apache.org)      | `\Fuko\Open\Editor::NETBEANS` |
| [PhpStorm](https://www.jetbrains.com/phpstorm)      | `\Fuko\Open\Editor::PHPSTORM` |
| [Sublime Text](http://www.sublimetext.com)          | `\Fuko\Open\Editor::SUBLIME`  |
| [TextMate](https://macromates.com/manual/en)        | `\Fuko\Open\Editor::TEXTMATE` |
| [Visual Studio Code](https://code.visualstudio.com) | `\Fuko\Open\Editor::VSCODE`   |
