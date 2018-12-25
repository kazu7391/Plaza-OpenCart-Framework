//<![CDATA[
$(window).load(function () {
	ptquickview.initQuickViewContainer();
});

var ptquickview = {
	'initQuickViewContainer' : function () {
		$('body').append('<div class="quickview-container"></div>');
		$('div.quickview-container').load('index.php?route=plaza/quickview/appendcontainer');
	},

	'appendCloseFrameLink' : function () {
		$('div#quickview-content').prepend("<a href='javascript:void(0);' class='a-qv-close' onclick='ptquickview.closeQVFrame()'>" + $('#qv-text-close').val() + "</a>");
	},

	'closeQVFrame' : function () {
		$('#quickview-bg-block').hide();
    	$('.quickview-load-img').hide();
    	$('div#quickview-content').hide(600).html('');
	},

	'ajaxView'	: function (url) {
		if(url.search('route=product/product') != -1) {
			url = url.replace('route=product/product', 'route=plaza/quickview');
		} else {
			url = 'index.php?route=plaza/quickview/seoview&ourl=' + url;
		}

		$.ajax({
			url 		: url,
			type		: 'get',
			beforeSend	: function() {
				$('#quickview-bg-block').show();
				$('.quickview-load-img').show();
			},
			success		: function(json) {
				if(json['success'] == true) {
					$('.quickview-load-img').hide();
					$('#quickview-content').html(json['html']);
					ptquickview.appendCloseFrameLink();
					$('#quickview-content').show(600);
				}
			}
		});
	}
};
//]]>
