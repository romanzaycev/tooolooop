<?php $this->extend('layouts/middle') ?>

<section>
    <h1>This is <code>page.php</code> template</h1>
    <p><?=$this->e($text, ['escape', 'nl2br', 'replace' => ['text', 'other text']]) ?></p>
    <ul>
        <?php foreach ($someItems as $someItem):?>
            <li><?=$this->e($someItem, ['strtoupper']) ?></li>
        <?php endforeach; ?>
    </ul>
</section>
