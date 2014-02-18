jQuery(document).ready(function() {
    jQuery(".dial").knob();
	jQuery('.dial').css('font-size', '36px/123px');
	jQuery('.dial').css('z-index', '99999');
});


/**
 * Get all items
 * @param  string dom 
 * @return array     
 */
function getAllItems(dom)
{
	var arr = new Array();
	jQuery(dom).each(function(index){
		arr.push({
			active: jQuery(this).hasClass('active'), 
			id: jQuery(this).attr('id')});		
	});
	return arr;
}

/**
 * Next slide
 */
function nextSlide()
{
	var slides     = getAllItems('.slides li');
	var indicators = getAllItems('.switcher li');
	var len        = slides.length-1;   
	
	for(key in slides)
	{
		if(slides[key]['active'])
		{
			jQuery('#' + slides[key]['id']).removeClass('active');
			jQuery('#' + indicators[key]['id']).removeClass('active');
			if(key < len)
			{
				jQuery('#' + slides[parseInt(key)+1]['id']).addClass('active');
				jQuery('#' + indicators[parseInt(key)+1]['id']).addClass('active');
			}
			else
			{
				jQuery('#' + slides[0]['id']).addClass('active');
				jQuery('#' + indicators[0]['id']).addClass('active');
			}
		}
	}
}

/**
 * Jump to some slide
 * @param  integer index    
 */
function jumpToSlide(index)
{
	index--;
	var slides     = getAllItems('.slides li');
	var indicators = getAllItems('.switcher li');

	for(key in slides)
	{
		if(slides[key]['active'])
		{
			jQuery('#' + slides[key]['id']).removeClass('active');
			jQuery('#' + indicators[key]['id']).removeClass('active');
		}
	}

	jQuery('#' + slides[index]['id']).addClass('active');
	jQuery('#' + indicators[index]['id']).addClass('active');
}
