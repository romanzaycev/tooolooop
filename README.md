# Tooolooop
---
[![Build status][travis-image]][travis-url] [![Code coverages][codecov-image]][codecov-url] [![Code climate][codeclimate-image]][codeclimate-image]


PHP7 lightweight native templates.

## Installation

via Composer:

```bash
composer require romanzaycev/tooolooop
```

## Usage

```php
<?php declare(strict_types = 1);

include "vendor/autoload.php";

use Romanzaycev\Tooolooop\Engine;

$engine = new Engine(__DIR__ . '/views');

$template = $engine->make('page');
$template->assign(['text' => 'Lorem ipsum']);

echo $template->render();
```

`views/page.php`:
```html
<?php $this->extend('layout')?>

<section>
	<?=$this->e($text)?>
</section>

<?php $this->start('footer')?>
<footer>
	Some footer content.
</footer>
<?php $this->end()?>
```

`views/layout.php`:
```html
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Example</title>
</head>
<body>
	<main>
		<h1>Example</h1>
		<?=$this->block('content')?>
	</main>
	
	<?=$this->block('footer')?>
</body>
</html>
```

Need more [examples](https://github.com/romanzaycev/tooolooop/tree/master/example)?

## Requires

PHP >= 7.1.0

## Documentation

`@TODO`

## Testing

```bash
composer run test
```

[travis-image]: https://travis-ci.org/romanzaycev/tooolooop.svg?branch=master
[travis-url]: https://travis-ci.org/romanzaycev/tooolooop

[codecov-image]: https://codecov.io/gh/romanzaycev/tooolooop/branch/master/graph/badge.svg
[codecov-url]: https://codecov.io/gh/romanzaycev/tooolooop

[codeclimate-image]: https://api.codeclimate.com/v1/badges/d36f92834ead870f1fbe/maintainability
[codeclimate-url]: https://codeclimate.com/github/romanzaycev/tooolooop/maintainability
