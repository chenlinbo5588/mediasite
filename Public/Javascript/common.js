$(function() {
	var gallen = $("#gallery-home").length;
	if(gallen>0)$("#gallery-home").PikaChoose();
});
var App = {};
/**
 * title: 弹出窗口的title body: 弹出窗口的body options: 参数
 */
App.alert = function(title, body, fn, options) {
	var id = 'goo-app-alert';
	var bodyHtml = '';
	var cfmLabel = (options && options.cfmLabel) ? options.cfmLabel : 'Confirm';
	var settings = $.extend({
		modal: true,
		title: title,
		width: 400,
		height: 170,
		msgType: 'alert'
	}, options);

	bodyHtml += '<div id="' + id + '" class="dialog">';
	bodyHtml += '<table height="70"><tr><td><div class="icon-' + settings.msgType + '">&nbsp;</div></td>';
	bodyHtml += '<td><div class="confirm-body">' + body + '</div></td></tr></table>';
	bodyHtml += '<table cellpadding="0" cellspacing="0" border="0">';
	bodyHtml += '<tr><td><div id="' + id + '-confirm" class="button"><span class="minbtn"><a href="javascript:;" >' + cfmLabel + '</span></a></div></td></tr>';
	bodyHtml += '</table></div>';

	$('#' + id).remove();
	$('body').append(bodyHtml);
	$('#' + id).dialog(settings);
	$('#' + id + '-confirm').click(function(event) {
		if(window.event) event.cancelBubble = true;
		else event.stopPropagation();
		if (fn) fn(true);
		$('#' + id).dialog("close").remove();
	});
};

/**
 * title: 弹出窗口的title body: 弹出窗口的body fn: 回调函数，第一参数即confirm返回的状态(true or false)
 * options: 参数
 */
App.confirm = function(title, body, fn, options) {
	var id = 'goo-app-confirm';
	var bodyHtml = '';
	var cfmLabel = (options && options.cfmLabel) ? options.cfmLabel : 'Confirm';
	var cnlLabel = (options && options.cnlLabel) ? options.cnlLabel : 'Cancel';
	var settings = $.extend({
		modal: true,
		title: title,
		width: 400,
		height: 170,
		open: function(){
			var thisCloseObj = $(this).prev('.ui-dialog-titlebar').find('.ui-dialog-titlebar-close');
			thisCloseObj.hide();
		}
	}, options);
	
	bodyHtml += '<div id="' + id + '" class="dialog">';
	bodyHtml += '<table height="50"><tr><td><div class="icon">&nbsp;</div></td>';
	bodyHtml += '<td><div class="confirm-body">' + body + '</div></td></tr></table>';
	bodyHtml += '<table cellpadding="0" cellspacing="0" border="0">';
	bodyHtml += '<tr><td><div id="' + id + '-confirm" class="button"><span class="minbtn"><a href="javascript:;" >' + cfmLabel + '</span></a></div></td>';
	bodyHtml += '<td><div id="' + id + '-cancel" class="button ml-10"><span class="minbtn"><a href="javascript:;" >' + cnlLabel + '</span></a></div></td><tr/>';
	bodyHtml += '</table></div>';
	
	$('#' + id).remove();
	$('body').append(bodyHtml);
	$('#' + id).dialog(settings);
	$('#' + id + '-confirm').click(function(event) {
		if(window.event) event.cancelBubble = true;
		else event.stopPropagation();
		if (fn) fn(true);
		$('#' + id).dialog("close").remove();
	});
	$('#' + id + '-cancel').click(function(event){
		if(window.event) event.cancelBubble = true;
		else event.stopPropagation();
		$('#' + id).dialog("close").remove();
	})
};

jQuery.fn.loadTpl = function(url, callback, append, async) {
	if ( typeof url !== "string" ) {
		return _load.call( this, url );
	} else if ( !this.length ) {
		return this;
	}
	if(typeof append == 'undefined') append = true;

	var off = url.indexOf(" ");
	if ( off >= 0 ) {
		var selector = url.slice(off, url.length);
		url = url.slice(0, off);
	}
	var type = "GET";
	var self = $(this);
	var tagName = $(this)[0].tagName.toLowerCase();
	if (tagName == 'div') {
		if(!append) this.empty();
		this.append('<span class="tpl-loading">Loading...</span>');
	}
	jQuery.ajax({
		url: url,
		type: type,
		dataType: "html",
		async: (typeof async != 'undefined') ? async : true,
		complete: function( res, status ) {
		if (tagName == 'div') $('.tpl-loading', self).remove();
			if ( status === "success" || status === "notmodified" ) {
				if(append) {
					self.append( selector ?
						jQuery("<div />").append(res.responseText.replace(rscript, "")).find(selector) :
						res.responseText );
				}
				else {
					self.empty();
					self.html( selector ?
						jQuery("<div />").append(res.responseText.replace(rscript, "")).find(selector) :
						res.responseText );
				}
			}

			if ( callback ) {
				self.each( callback, [res.responseText, status, res] );
			}
		}
	});
	return this;
}