function randomString() {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var string_length = 32;
	var randomstring = '';
	for (var i=0; i < string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum, rnum + 1);
	}
	return randomstring;
}

(function(i,s,o,g,r,a,m){
	i.GoogleAnalyticsObject=r;
	i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();
	a=s.createElement(o),m=s.getElementsByTagName(o)[0];
	a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create','UA-56486-1','auto',{allowLinker:true});
ga('require','linker');
ga('linker:autoLink',['qualityunit.com','ladesk.com','postaffiliatepro.com','unitminer.com','postaffiliatenetwork.com','getreply.com','swreg.org']);
ga('send','pageview');

jQuery(function($) {
	$.fn.setButton = function(buttonId) {
		var res = $("textarea#" + buttonId).val();
		$("textarea#la-config-button-code").val(res);
		$("#buttonId").val(buttonId);
		replacePlaceholder();
		$("#configForm").submit();
	};

	$.fn.getIframePreviewCode = function(id) {
		$('#iFramePreview' + id).contents().find("body").html($('#iFrame' + id).val());
	}
	
	var replacePlaceholder = function() {
		var widgetCode = $('textarea#' + $('#buttonId').val() ).val();
		var pos = widgetCode.indexOf("function(e){") + 13;
		var result = '';
		if ($('#configOptionName').is(':checked')) {
			result += "%%firstName%%%%lastName%%";
		}
		if ($('#configOptionEmail').is(':checked')) {
			result += "%%email%%";
		}
		if ($('#configOptionPhone').is(':checked')) {
			result += "%%phone%%";
		}

		$('textarea#la-config-button-code').val([widgetCode.slice(0, pos), result, widgetCode.slice(pos)].join(''));	
		$('.SaveWidgetCode').show();
	}
	
	$('textarea#la-config-button-code').on('change', function() {
		$('.SaveWidgetCode').show();
	});

	$('#connectButtontextSpan').on('click', function() {
		$('#configForm').submit();
	    return true;
	});
	
	$('.configOptions input').change(function () {
		replacePlaceholder();
	});
	
	$('#resetLink').on('click', function() {
		if (confirm("This will RESET everything so you could start over. Do you agree?")) {
			window.location.href = $('#resetUrl').val();
		}
	});
});