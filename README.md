# Tooolooop
---
[![Build status][travis-image]][travis-url] [![Code coverages][codecov-image]][codecov-url] [![Code climate][codeclimate-image]][codeclimate-url] [![Latest Stable Version](https://poser.pugx.org/romanzaycev/tooolooop/v/stable)](https://packagist.org/packages/romanzaycev/tooolooop) [![Total Downloads](https://poser.pugx.org/romanzaycev/tooolooop/downloads)](https://packagist.org/packages/romanzaycev/tooolooop)


PHP7 lightweight native templates.

## Installation

via Composer:

```bash
composer require romanzaycev/tooolooop
```

## Usage

```php
<?php declare(strict_types = 1);

require "vendor/autoload.php";

use Romanzaycev\Tooolooop\Engine;

$engine = new Engine(__DIR__ . '/views');

$template = $engine->make('page');
$template->assign(['text' => 'Lorem ipsum']);

echo $template->render();
```

`views/page.php`:
```html
<?php $this->extend('layout') ?>

<section>
  <?=$this->e($text)?>
</section>

<?php $this->start('footer') ?>
<footer>
  Some footer content.
</footer>
<?php $this->end() ?>
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

PHP >= 7.2.0

## Extending library

### PSR-11 container support 

You can use a PSR-11 compatible
container and inject dependencies into objects
that are generated inside the library (Scope):

```php
<?php

use Romanzaycev\Tooolooop\Engine;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $container */
$container = ...; // Initialize PSR-11 container
                  // and define implementation of Romanzaycev\Tooolooop\Scope\ScopeInterface
$engine = new Engine(__DIR__ . '/views');
$engine->setContainer($container);
$template = $engine->make('page'); // <-- Scope in this template will be obtained from container
```

You can define the implementation of `Romanzaycev\Tooolooop\Scope\ScopeInterface` in the
container configuration and engine instances Scope through it.


### User scope

Otherwise you can specify a custom implementation of the class via `$engine->setScopeClass()`:
```php
<?php

use Romanzaycev\Tooolooop\Engine;
use Romanzaycev\Tooolooop\Scope\Scope;
use Romanzaycev\Tooolooop\Scope\ScopeInterface;

class UserSpecificScope extends Scope implements ScopeInterface {
    // Realize your additions, ex. widget system :-)
}

$engine = new Engine(__DIR__ . '/views');
$engine->setScopeClass(UserSpecificScope::class);
```

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
