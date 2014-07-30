
<script type="text/javascript">

    window.addEvent( 'domready', function(){
        registerEvents();
    });

    var registerEvents = function(){
        if( $('Slideshows') ){
            $('Slideshows').removeEvents().addEvent( 'change', changeSlideshow );
        }
    };

    var changeSlideshow = function(event){
        var slideshow_id = $(event.target).value;
        var url = new URI(window.location);
        url.setData({slideshow_id:slideshow_id});
        window.location = url;
    }

</script>