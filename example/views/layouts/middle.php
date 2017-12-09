<?php $this->extend('./main')?>

<div>
    <small>Template inheritance</small>
    <?=$this->block()?>
</div>

<?php $this->start('footer')?>
<footer>
    <section>
        <h3>This is `footer` block, defined in <code>middle.php</code></h3>
    </section>
</footer>
<?php $this->end()?>