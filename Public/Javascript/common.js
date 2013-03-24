$(function() {
	var gallen = $("#gallery-home").length;
	if(gallen>0)$("#gallery-home").PikaChoose();
	initSelectBox();
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
		height: 200,
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

var SELECT_Z_INDEX = 100;
function initSelectBox(obj) {
	function clickSelectEvent(selectEl, selectList) {
		SELECT_Z_INDEX ++;
		if(selectList.style.display == 'block') {
			selectList.style.display = 'none';
		}
		else {
			selectList.style.display = 'block';
			selectEl.style.zIndex = SELECT_Z_INDEX;
		}
		$(selectEl).parents('.field-box:first').siblings('.error:first').remove();
	}

	function clickOptionEvent() {
		var selectID 		= this.selectid;

		var selectEl 		= document.getElementById(selectID.replace(/\./g, '-'));
		var $selectEl 		= $('#'+selectID.replace(/\./g, '-'));
		var optionID 		= 'select-item-' + selectID + '-' + selectEl.selectedIndex;
		var lastSelectedEl 	= document.getElementById(optionID);
		var selectInputEl 	= document.getElementById('select-input-' + selectID);
		
		selectInputEl.innerHTML = this.innerHTML;
		var selectListEl 	= document.getElementById('select-list-' + selectID);
		selectListEl.style.display = 'none';
		this.option.selected = true;
		this.className = 'selected';
		lastSelectedEl.className = '';
		// if(selectEl.onchange) selectEl.onchange();
		// if(selectEl.onblur) selectEl.onblur();
		// alert($selectEl.change);
		if($selectEl.change) $selectEl.trigger('change');
		if($selectEl.blur) $selectEl.trigger('blur');
	}

	function bindMouseEvent(selectEl, selectInput, selectList) {
		if($.browser.msie) {
			selectInput.attachEvent('onclick', function() {
				clickSelectEvent(selectEl, selectList);
				window.event.cancelBubble = true;
			});
		}
		else {
			selectInput.addEventListener('click', function(e) {
				clickSelectEvent(selectEl, selectList);
				e.stopPropagation();
			}, false);
		}
	}

	function rebuildOptionEl(selectID, parentEl, selectInputEl, options) {
		var firstOption = null, hasSelected = false, isSelected = false;
		var option = null;
		var maxWords = parseInt(parseInt(parentEl.style.width) / 6)-1;
		for (var n = 0; n < options.length; n ++){
			var option 		= options[n];
			var optionEl 		= document.createElement('li');
			optionEl.id		= 'select-item-' + selectID + '-' + n;
			optionEl.selectid	= selectID;
			optionEl.style.cursor 	= 'pointer';
			//optionEl.title 	= option.text;
			optionEl.title 	= option.title;
			optionEl.option 	= option;
			parentEl.appendChild(optionEl);

			var text			= _substr(option.text, maxWords);
			var optionText 		= document.createTextNode(text);
			optionEl.appendChild(optionText);

			isSelected 		= option.selected ? true : false;
			
			if(n == 0) firstOption = option;
			if(isSelected) {
				hasSelected 		= true;
				optionEl.className 	= 'selected';
				selectInputEl.appendChild(document.createTextNode(optionEl.innerHTML));
			}
			
			optionEl.onmouseover = function() {
				if(this.className != 'selected') {
					this.className = 'option-over';
				}
			}
			optionEl.onmouseout = function() {
				if(this.className != 'selected') {
					this.className = '';
				}
			}
			if($.browser.msie) {
				optionEl.onclick = clickOptionEvent;
			}
			else {
				optionEl.addEventListener('click', clickOptionEvent, false);
			}
		}
		if(hasSelected == false) {
			firstOption.className = 'selected';
			selectInputEl.appendChild(document.createTextNode(firstOption.innerHTML));
			firstOption.selected = true;
		}
	}
	
	function rebuildSelectEl(selects) {
		var selectEl = null, selectID = '';
		var selectWrapperEl = null, selectWrapperID = '';
		for (i = 0; i < selects.length; i ++) {
			selectEl 	= selects[i];
			$select	 	= $(selectEl);
			
			selectID 	= ('slt-' + new Date().getTime() + i);
			if(selectEl.id) {
				selectID 	= selectEl.id.replace(/\-/g, '.');
			}
			else {
				selectEl.id 	= selectID;
				selectID	= selectID.replace(/\-/g, '.');
			}
			
			selectWrapperID		= 'select-' + selectID;
			var selectWrapperEl	= document.getElementById(selectWrapperID);
			if(selectWrapperEl) {
				$(selectWrapperEl).remove();
			}

			// select外框
			//var outerOffset			= $select.parents('.field-box').offset();
			var selectOffset		= $(selectEl).offset();
			selectWrapperEl			= document.createElement('div');
			selectWrapperEl.id		= selectWrapperID;
			selectWrapperEl.className	= 'select-box';
			selectWrapperEl.style.zIndex 	= SELECT_Z_INDEX;
			var selectWidth			= $select.width() + 5;
			if(selectWidth) {
				selectWrapperEl.style.width = selectWidth + 'px';
			}
			
			// select输入框外框
			var selectInputBoxEl		= document.createElement('div');	
			selectInputBoxEl.id		= 'select-input-box-' + selectID;
			selectInputBoxEl.className	= 'select-input-box';
			selectInputBoxEl.style.cursor 	= 'pointer';
			
			// select输入框
			var selectInputEl		= document.createElement('div');	
			selectInputEl.id		= 'select-input-' + selectID;
			selectInputEl.className		= 'select-input';
			selectInputEl.style.cursor	= 'pointer';
			selectInputBoxEl.appendChild(selectInputEl);
			selectWrapperEl.appendChild(selectInputBoxEl);
			
			// select列表
			var selectListEl		= document.createElement('ul');	
			selectListEl.id			= 'select-list-' + selectID;
			selectListEl.className		= 'select-list';
			selectListEl.style.position	= 'absolute';
			selectListEl.style.display	= 'none';
			selectListEl.style.zIndex	= SELECT_Z_INDEX;
			if(selectWidth) {
				if(!$.browser.msie) selectWidth = selectWidth - 2;
				selectListEl.style.width = selectWidth + 'px';
			}
			selectWrapperEl.appendChild(selectListEl);
			
			var options 			= selectEl.getElementsByTagName('option');
			if(options.length > 0) {
				if(options.length > 6) { // 如果列表过长则自动高度
					selectListEl.style.height		= '160px';
					selectListEl.style.overflowY		= "auto";
					selectListEl.style.overflowX		= "hidden";
				}
				rebuildOptionEl(selectID, selectListEl, selectInputEl, options);
			}
			
			bindMouseEvent(selectWrapperEl, selectInputBoxEl, selectListEl);
			
			$select.parent().append(selectWrapperEl);
			selectEl.style.position		= 'absolute';
			selectEl.style.left		= '-9999px';
			// $select.remove();
		}
	}
	var allSelects = new Array();
	if(typeof(obj) == 'string') {
		allSelects.push(document.getElementById(obj));
	}
	else if(typeof(obj) == 'object') {
		allSelects.push(obj);
	}
	else {
		allSelects = document.getElementsByTagName('select');
	}
	rebuildSelectEl(allSelects);
}

function _substr(str, len) {
	var sLen = 0, suffix = '', zhCounter = 0, enCounter = 0, counter = 0;
	sLen = str.length;
	if (sLen > len) suffix = ' ...';
	if (typeof len == 'undefined') len = 0;
	for (var i = 0; i < len; i ++) {
		if(str.charCodeAt(i) > 255) {
			zhCounter += 2;
		}
		else {
			enCounter += 1;
		}
		counter += 1;
		if(zhCounter + enCounter == len) {
			return (str.substring(0, counter)) + suffix;
		}
		if(zhCounter + enCounter > len) {
			return (str.substring(0, counter - 1)) + suffix;
		}
	}
	return str;
}