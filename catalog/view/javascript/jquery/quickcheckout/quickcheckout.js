function moduleLoad(element, spinner) {
	if (spinner) {
		element.find('.quickcheckout-content').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-5x"></i></div>');
	} else {
		moduleLoaded(element, spinner);
		
		var width = element.width();
		var height = element.height();
		var margin = height / 2 - 30;
		
		if (height > 30) {
			html = '<div class="overlay" style="position:absolute;bottom:0;left:0;z-index:99999;background:none;width:' + width + 'px;height:' + height + 'px;text-align:center;"><i class="fa fa-spinner fa-spin fa-5x" style="margin-top:' + margin + 'px;"></i></div>';
			
			element.append(html);
			
			element.css({
				'opacity': '0.5',
				'position': 'relative'
			});
		}
	}
}

function moduleLoaded(element, spinner) {
	if (!spinner) {
		element.find('.overlay').remove();
		
		element.removeAttr('style');
	}
}

function disableCheckout() {
	$('#quickcheckout-disable').css('opacity', '0.5');
	
	var width = $('#quickcheckout-disable').width();
	var height = $('#quickcheckout-disable').height();

	html = '<div class="disable-overlay" style="position:absolute;top:0;left:0;z-index:99999;background:none;width:' + width + 'px;height:' + height + 'px;text-align:center;"></div>';
	
	$('#quickcheckout-disable').css('position', 'relative').append(html);
}