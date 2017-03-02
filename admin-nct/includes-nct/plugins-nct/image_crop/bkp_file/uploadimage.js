/**
 *
 * Crop Image While Uploading With jQuery
 * 
 * Copyright 2013, Resalat Haque
 * http://www.w3bees.com/
 *
 */
// set info for cropping image using hidden fields
function setInfo(i, e) {
	$('#x').val(e.x1);
	$('#y').val(e.y1);
	$('#w').val(e.width);
	var a=$('#h').val(e.height);
	console.log(jQuery('#uploadPreview').width());
	jQuery('#img_width').val(jQuery('#uploadPreview').width());
	jQuery('#img_height').val(jQuery('#uploadPreview').height());

}

$(document).ready(function() {

	$( "#test" ).on( "click", function() {
		//$("#Edit_Profile").css("display", "none");

		$("#Edit_Profile").hide();
      // $("#modal").hide();
       $("#Edit_Profile1").show(); 
       //$("#avatar-modal").css("display", "block");

	})

	
	
});