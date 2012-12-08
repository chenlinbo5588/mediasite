$(function() {
	$("#gallery-home").PikaChoose();
	$("#gallery-contact").PikaChoose();
	$( "#dialog" ).dialog({
			autoOpen: false,
			width: 400,
			modal:true,
			buttons: [
				{
					text: "Save",
					click: function() {
						$( this ).dialog( "close" );
					}
				},
				{
					text: "Cancel",
					click: function() {
						$( this ).dialog( "close" );
					}
				}
			]
		});
	$( "#dialog-link" ).click(function( event ) {
			$( "#dialog" ).dialog( "open" );
	});
});