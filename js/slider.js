jQuery(document).ready(function() {
	var jcarousel = jQuery('.jcarousel-slider');

    jcarousel.on('jcarousel:reload jcarousel:create', function () {
			jcarousel.jcarousel('items').width(jcarousel.innerWidth()); }).jcarousel({
                wrap: 'circular'});
    setInterval("jQuery('.jcarousel-slider').jcarousel('scroll', '+=1')", 4000);

    jQuery('.jcarousel-slider-control-prev').jcarouselControl({ target: '-=1' });

    jQuery('.jcarousel-slider-control-next').jcarouselControl({ target: '+=1' });

    jQuery('.jcarousel-slider-pagination').on('jcarouselpagination:active', 'li', function() {
        jQuery(this).addClass('active'); }).on('jcarouselpagination:inactive', 'li', function() {
            jQuery(this).removeClass('active'); }).on('click', function(e) {
            e.preventDefault(); }).jcarouselPagination({
            item: function(page) {
                return '<li><a href="#' + page + '"></a></li>';
            }
        });
});