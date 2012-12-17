var uploadUser = [];
$(function(){
	$("div.upUser").each(function(index){
		uploadUser.push(createSwfUpload(index + 1,$(this).attr('allowFile')));
	});

});

function createSwfUpload(index,allowFile){
	var upload = new SWFUpload({
		// Backend Settings
		upload_url: "/upload/upload.php?action=uploadAttachment",
		post_params: {isajax:"1",cookie:document.cookie},
		// File Upload Settings
		file_size_limit : "50MB",
		file_types : allowFile,
		file_types_description : "All Files",
		file_upload_limit : "0",
		file_queue_limit : "0",
		// Event Handler Settings (all my handlers are in the Handler.js file)
		file_dialog_start_handler : fileDialogStart,
		file_queued_handler : fileQueued,
		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
		// Button Settingsimg/fee
		button_image_url : "",
		button_placeholder_id : "user" + index +"UploadPlaceholder",
		button_width: 105,
		button_height: 24,
		button_text : '<span class="redText">上传文件</span>',
		button_text_style : ".redText {text-align:center;}",
		button_window_mode:"TRANSPARENT",
		button_cursor: SWFUpload.CURSOR.HAND,
		// Flash Settings
		flash_url : "/swfupload.swf",
		custom_settings : {
			progressTarget : "user" + index + "UploadProgress",
			callback : function(file,serverData){
				var tmp = eval("(" + serverData + ")");
				if(tmp.code == 200){
					$("#up_id_" + index).val(tmp.result.id);
					$("#up_path_" + index).val(tmp.result.path);
					$("#upload_" + index + "_flag").html('');
				}else{
					$("#upload_" + index + "_flag").html('<p class="error">附件上传失败，请重新上传!</p>');
				}
				
			}
		},

		// Debug Settings
		debug: false
	});

	return upload;
}