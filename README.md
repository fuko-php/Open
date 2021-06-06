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

/* I have Atom installed locally, so this is what I want to use */
$editor = new Editor(Editor::ATOM);
```
Once you have created the `\Fuko\Open\Editor` object, you call its `link()` method
to get the generated and formatted URL:
```php
echo $editor->link('/var/www/html/index.html', 2);
// atom://core/open/file?filename=%2Fvar%2Fwww%2Fhtml%2Findex.html&line=2
```

# Editor Sniff

You can *sniff* what editor is installed locally by using `\Fuko\Open\Sniff::detect()`. It
will either return a new `\Fuko\Open\Editor` object with the format setup inside it to to
use, or if nothing is found it will return `NULL`.

```php
include __DIR__ . '/vendor/autoload.php';
use \Fuko\Open\Sniff;

/* I have Atom installed locally, so this is how you can detect it */
$editor = (new Sniff)->detect();
if ($editor)
{
	echo $editor->link('/var/www/html/index.html', 2);
	// atom://core/open/file?filename=%2Fvar%2Fwww%2Fhtml%2Findex.html&line=2
}
```

The sniffing is done using "sniffer" functions/methods. There are some that are built-in,
but you can add your own using `\Fuko\Open\Sniff::addSniffer()`. The sniffers must
return either the format to use in the `\Fuko\Open\Editor` constructor, or an empty string if
there is no match.

```php
$sniff->addSniffer(function()
{
	return getenv('EDITOR') === 'subl -w'
		? \Fuko\Open\Editor::SUBLIME
		: '';
});
```
