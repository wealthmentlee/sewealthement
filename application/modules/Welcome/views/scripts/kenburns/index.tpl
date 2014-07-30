

<?php $this->headLink()->appendStylesheet($this->baseUrl().'/application/modules/Welcome/externals/styles/kenburns/slideshow.css'); ?>
<?php $this->headScript()->appendFile($this->baseUrl().'/application/modules/Welcome/externals/scripts/kenburns/slideshow.js'); ?>
<?php $this->headScript()->appendFile($this->baseUrl().'/application/modules/Welcome/externals/scripts/kenburns/slideshow.kenburns.js'); ?>

<script type="text/javascript">
    en4.core.runonce.add(function () {

        var data = <?php echo $this->data ?>;

        new Slideshow.KenBurns('<?php echo $this->containerId ?>', data, {
            duration: <?php echo $this->settings['duration']; ?>,
            height: <?php echo $this->height; ?>,
            width: <?php echo $this->width; ?>,
            thumbnails: false
        });
    });
</script>

<div id="<?php echo $this->containerId; ?>" style="width: <?php echo $this->width; ?>px;height: <?php echo $this->height; ?>px" class="slideshow">
    <img src="<?php echo $this->firstImage; ?>" alt="">
</div>
