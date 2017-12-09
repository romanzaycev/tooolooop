<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?=$this->e($title, ['ucfirst', 'replace' => ['oo', 'ooo']])?></title>
</head>
<body>
    <header>
        Tooolooop example page
    </header>

    <main>
        <?=$this->block()?>
    </main>
    <?=$this->load('partials/sidebar', ['sidebarMessage' => '<Escaped sidebar content>'])?>
    <?=$this->block('footer')?>
</body>
</html>