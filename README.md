# Fuko\\Open [![Latest Version](http://img.shields.io/packagist/v/fuko-php/open.svg)](https://packagist.org/packages/fuko-php/open) [![GitHub license](https://img.shields.io/github/license/fuko-php/open.svg)](https://github.com/fuko-php/open/blob/master/LICENSE)

**Fuko\\Open** is a small PHP library that helps you to generate links for
opening referenced files directly in your IDE or editor, or have it linked to
an online repository.

## Basic Use

At the heart of it is all is the `\Fuko\Open\Link` class, which is really simple: it
takes a format to use in its constructor, and then using that format it creates a
formatted link to a code reference identified by its **file** and **line**:

```php
include __DIR__ . '/vendor/autoload.php';
use \Fuko\Open\Link;

$link = new Link('source-code://%s:%d');
$href = $link->link(__FILE__, __LINE__);
```

The format is `sprintf()`-based, with two placeholders: first one is `%s` for
the file, and the second one is `%d` for the line. That's it, it's pretty simple.

### Translating Paths

There are occasions when leading portions of the filenames must be "translated" to
something different, like when:

-  like when you get the real path to a file after some of its parent folders
	was a symlink that was resolved to its real source;

-  like when you've mounted a shared network volume with your web server machine,
	and you want to use the locally mounted names, and not the remote ones

- or like when you are using Docker and you want to translate the Docker-based
	filenames to your locally-accessible filenames.

For all of those cases, the `\Fuko\Open\Link` objects have the ability to replace
leading filename prefixes. You can add a new prefix for translating a file path
like this:

```php
include __DIR__ . '/vendor/autoload.php';
use \Fuko\Open\Link;

$link = new Link('source-code://%s:%d');
$link->addPrefix(getcwd() . '/', '/upside/down/');
$href = $link->link(__FILE__, __LINE__);
// source-code://%2Fupside%2Fdown%2Fdemo.php:23
```

You can add multiple prefixes, as usually there is more than one symlinked folder
in most projects.

```php
include __DIR__ . '/vendor/autoload.php';
use \Fuko\Open\{Link, Editor};

$href = (new Link(Editor::ATOM))
	->addPrefix(getcwd() . '/', '/upside/down/')
	->addPrefix('/private/tmp', 'tmp')
	->link(__FILE__, __LINE__);
```

## Editor Links

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

### Editor Sniff

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

### Supported Editors

This is the list of the IDEs and editors supported by **Fuko\\Open**

| Editor                                              | Format Const                  |
|-----------------------------------------------------|-------------------------------|
| [Atom](https://atom.io)                             | `\Fuko\Open\Editor::ATOM`     |
| [GNU Emacs](https://www.gnu.org/software/emacs)     | `\Fuko\Open\Editor::EMACS`    |
| [Espresso](https://www.espressoapp.com)             | `\Fuko\Open\Editor::ESPRESSO` |
| [IntelliJ IDEA](https://www.jetbrains.com/idea)     | `\Fuko\Open\Editor::IDEA`     |
| [Mac Vim](https://macvim-dev.github.io/macvim)      | `\Fuko\Open\Editor::MACVIM`   |
| [Apache NetBeans](https://netbeans.apache.org)      | `\Fuko\Open\Editor::NETBEANS` |
| [Nova](https://nova.app)                            | `\Fuko\Open\Editor::NOVA`     |
| [PhpStorm](https://www.jetbrains.com/phpstorm)      | `\Fuko\Open\Editor::PHPSTORM` |
| [Sublime Text](http://www.sublimetext.com)          | `\Fuko\Open\Editor::SUBLIME`  |
| [TextMate](https://macromates.com/manual/en)        | `\Fuko\Open\Editor::TEXTMATE` |
| [Visual Studio Code](https://code.visualstudio.com) | `\Fuko\Open\Editor::VSCODE`   |

## Repo Links

There are situations in which you do not want to create links to local source code files,
but instead link to your code repository. Code repo source links usually contain not
just the workspace/account/project and the repo name, but also the branch/tag/commit at
which you reviewing the code. To create repo links use the `Fuko\Open\Repo` class, which
will help you to get a new `\Fuko\Open\Link` object with the repo link format setup inside:

```php
include __DIR__ . '/vendor/autoload.php';
use \Fuko\Open\Repo;

$repo = new Repo(Repo::GITHUB,
	getcwd() . '/',	// cloned repo root folder which must be stripped from the link
	'fuko-php',	// workspace (aka project or account)
	'open',		// name of the repository
	'master'	// branch, tag or commit
	);

echo $repo->getLink()->link(__FILE__, 42);
// https://github.com/fuko-php/open/blob/master/tests%2FRepoTest.php#L42
```

There constants inside the `Fuko\Open\Repo` class to help you with the formats for
the different source-code hosting websites:

-  `Fuko\Open\Repo::BITBUCKET` is for [Bitbucket Cloud](https://bitbucket.org)
-  `Fuko\Open\Repo::GITHUB` is for [GitHub](https://github.com)
