/* $Id: slider.js 2010-08-02 16:02 idris $ */

var GoodCarousel = new Class({

    container: null,
    inner: null,
    animate: null,
    nav: null,

    data: null,
    timer: null,
    ticks: 0,

    options: {
        width: 624,
        height: 282,
        delay: 5100,
        duration: 700,
        transition: Fx.Transitions.Sine.easeInOut,
        structure: {
            header: "h3",
            data: ".carousel-data",
            link: "p.data a",
            img: "img"
        },
        data: null
    },

    Implements: [Options],

    initialize: function (container, options) {
        this.setOptions(options);
        this.container = document.id(container);
        this.parseData();
        this.setup();
    },

    parseData: function () {

        var data = [];
        if (this.options.data == null) {
            this.container.getElements(this.options.structure.data).each(function (el, i) {
                var slide = {},
                link = el.getElement(this.options.structure.link),
                img = el.getElement(this.options.structure.img);
                slide.title = (el.getElement(this.options.structure.header)) ? el.getElement(this.options.structure.header).get("text") : "";
                if (link){
	                slide.desc = link.get("text") ? link.get("text") : "";
	                slide.url = link.get("href") ? link.get("href") : "";
	                slide.rel = link.get("rel") ? link.get("rel") : "";
	                slide.alt = img.get("title") ? img.get("title") : "";
                    slide.target = link.get('target') ? link.get('target') : "_blank";
                }
                slide.img = img.get("src") ? img.get("src") : "";
                slide.index = i;
                data.push(slide); // Because I don't trust data[] = slide; in js.
            },
            this);
        }

        this.data = data;
        this.container.empty();

    },

    setup: function () {

        /*
			I didn't want to make this a String method so I'll make my own private method.
			elementize: return an element based on a string.
		*/
        function elementize(string) {
            return new Element("div", {
                html: string
            }).getFirst();
        }

        // Set the container's width, add class
        this.container.setStyle("width", this.options.width).addClass("carousel");

        // Make an inner container, set width and height.
        this.inner = new Element("div", {
            "class": "carousel-inner",
            "styles": {
                "width": this.options.width,
                "height": this.options.height
            }
        });
        // Container grabs inner.
        this.container.grab(this.inner);
        // Make the animate div.
        this.animate = new Element("div", {
            "class": "carousel-animate",
            "styles": {
                "width": this.options.width
            }
        }).set("tween", {
            duration: this.options.duration,
            transition: this.options.transition
        });
        // Inner Container grabs this one.
        this.inner.grab(this.animate);

        // Get the link elements in the container.
        var slides = this.data;

        /*
			So we know how much space to allot.
		*/
        this.animate.setStyle("width", this.options.width * slides.length);

        // Make the under-slide navigation bar.
        this.nav = new Element("ul", {
            "class": "carousel-nav"
        });
        this.container.grab(this.nav);

        // Make the separate blocks here.
        slides.each(function (data, i) {

            var biglink_data = {
                url: data.url,
                img: data.img,
                height: this.options.height,
                width: this.options.width,
                title: data.title,
                desc: data.desc
            },
            biglink = ('<a class="carousel-item" id="carousel-slide-'+ i +'" href="{url}" target="_blank"><img src="{img}" height="{height}" width="{width}" border="0" /><span class="carousel-item-info"><strong><span>{title}</span></strong><br /><span>{desc}</span></span></a>').substitute(biglink_data);
            biglink = elementize(biglink);

            biglink.setStyle("width", this.options.width).set("rel", data.rel);

            this.animate.grab(biglink);

            //======
            // Again, using substitue.
            var nav_data = {
                link: data.url,
                title: data.title
            },

            nav = ('<li id="slide_link_' + i + '" ><a id="carousel-link" href="{link}" data="{data}" title="{title}" target="_blank">{title}</a></li>').substitute(nav_data);
            nav = elementize(nav);

            nav.store("data", data).setStyle("width", Math.floor(this.options.width / slides.length)).getFirst().set("rel", data.rel);


            // By default, first block is selected.
            if (i === 0) {
                nav.addClass("selected");
            }
            // Notice the stupid links.length-1? Arrays index from 0 even in foreach!
            // Last class takes off the borders and other things.
            if (i === (slides.length - 1)) {
                nav.addClass("last");
            }

            // Add the mouseenter/leave events.
            nav.addEvents({
                "mouseenter": this.events.over.bind(this),
                "mouseleave": this.events.out.bind(this)
            });

            // Let the nav grab the element.
            this.nav.grab(nav);
        },
        this); // This links.each loop is bound to _THIS_.
        this.tick(); // Start it up.
    },

    /*
		The recuring function that triggers the slides to move.
			Removes selected class
			Adds selected class to whatever needs it
			Runs the tween
			Increments tick or resets it
			Clears and restarts timer.
	*/
    tick: function () {
        // Get the links in nav.
        var links = this.nav.getElements("li a");


        // selected is on one of them, so take it off, then add it back.
        links.removeClass("selected").each(function (el, i) {
            if (this.ticks === i) {
                el.addClass("selected");
            }
        },
        this);

        // Slide now.
        this.slide();

        // Increment.
        this.ticks++;

        // Remember, (links.length-1) looks stupid but length doesn't _really_ start at 0.
        // If tick meets the length-1 of the links, restart.
        if (this.ticks > (links.length - 1)) {
            this.ticks = 0;

        }
        $clear(this.timer);
        this.timer = this.tick.delay(this.options.delay, this); // Set.
    },

    events: {
        over: function (event) {
            // Mouseenter: when they move over the block.
            var data = event.target.getParent().retrieve("data", {
                index: 0
            });

//            Bekbolot customize {
            if(Browser.name == 'firefox' || Browser.name == 'ie')
                data = event.target.retrieve("data", {
                    index: 0
                });
//            } Bekbolot customize

            // Set tick to the current block.
            this.ticks = data.index;
            this.tick();
            $clear(this.timer); // This clear pauses movement until mouseleave.

        },
        out: function (event) {
            // Resume movement.
            this.timer = this.tick.delay(this.options.delay, this);
        }
    },

    slide: function () {
        // This animates the slides
        this.animate.tween("left", 0 - (this.ticks * this.options.width));
    }

});