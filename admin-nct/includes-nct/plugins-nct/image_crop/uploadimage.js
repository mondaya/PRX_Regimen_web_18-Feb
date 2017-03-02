function setInfo(i, e) {
    $('#x').val(e.x1);
    $('#y').val(e.y1);
    $('#w').val(e.width);
    var a = $('#h').val(e.height);
    console.log(jQuery('#uploadPreview').width());
    jQuery('#img_width').val(jQuery('#uploadPreview').width());
    jQuery('#img_height').val(jQuery('#uploadPreview').height());
}

$(document).ready(function() {

    $(document).on('click', '.slider_places_image', function(event) {
        event.preventDefault();
        $("#avatarInput").val('');
        $("#hidden_image_id").html('header_slider');
        $("#which_types").val('header_slider');
        $(".cropper-bg").remove();
        $(".preview-md>img").remove();
        $(".preview-lg>img").remove();
        $(".preview-sm>img").remove();
        //$(".preview-sm").children('img').remove();
        //$(".preview-lg").children('img').remove();
        $("#Edit_Profile1").show();
    });

    $(document).on('click', '.places_image', function(event) {
        event.preventDefault();
        $("#avatarInput").val('');
        $("#hidden_image_id").html('images');
        $("#which_types").val('images');
        $(".cropper-bg").remove();
        $(".preview-md>img").remove();
        $(".preview-lg>img").remove();
        $(".preview-sm>img").remove();
        //$(".preview-sm").children('img').remove();
        //$(".preview-lg").children('img').remove();
        $("#Edit_Profile1").show();
    });

    $(document).on('click', '.test_slider', function(event) {
        event.preventDefault();
        $("#avatarInput").val('');
        $("#Edit_Profile1").show();
        $("#which_types").val('slider');
        $(".cropper-bg").remove();
        $(".preview-md>img").remove();
        $(".preview-lg>img").remove();
        $(".preview-sm>img").remove();
        $("#hidden_image_id").html('slider');
    });

    $(document).on('click', '.ADVERTISE_SLIDER', function(event) {
        event.preventDefault();
        $("#avatarInput").val('');
        $("#Edit_Profile1").show();
        $("#which_types").val('advertise_slider');
        $(".cropper-bg").remove();
        $(".preview-md>img").remove();
        $(".preview-lg>img").remove();
        $(".preview-sm>img").remove();
        $("#hidden_image_id").html('advertise_slider');
    });


    $(document).on('click', '.SITE_FAVICON', function(event) {
        event.preventDefault();
        $("#avatarInput").val('');
        $("#hidden_image_id").html('favicon');
        $("#which_types").val('favicon');
        $(".cropper-bg").remove();
        $(".preview-md>img").remove();
        $(".preview-lg>img").remove();
        $(".preview-sm>img").remove();
        //$(".preview-sm").children('img').remove();
        //$(".preview-lg").children('img').remove();
        $("#Edit_Profile1").show();
    });

    $(document).on('click', '.SITE_LOGO', function(event) {
        event.preventDefault();
        $("#avatarInput").val('');
        $("#hidden_image_id").html('site_logo');
        $("#which_types").val('site_logo');
        $(".cropper-bg").remove();
        $(".preview-md>img").remove();
        $(".preview-lg>img").remove();
        $(".preview-sm>img").remove();
        //$(".preview-sm").children('img').remove();
        //$(".preview-lg").children('img').remove();
        $("#Edit_Profile1").show();

    });
    $(document).on('click', '.HEADER_SLIDER', function(event) {
        event.preventDefault();
        $("#avatarInput").val('');
        $("#hidden_image_id").html('header_slider');
        $("#which_types").val('header_slider');
        $(".cropper-bg").remove();
        $(".preview-md>img").remove();
        $(".preview-lg>img").remove();
        $(".preview-sm>img").remove();
        //$(".preview-sm").children('img').remove();
        //$(".preview-lg").children('img').remove();
        $("#Edit_Profile1").show();

    });
    $(document).on('click', '.change-profile', function(event) {
        event.preventDefault();
        $("#avatarInput").val('');
        $("#hidden_image_id").html('change_profile');
        $("#which_types").val('change_profile');
        $(".cropper-bg").remove();
        $(".preview-md>img").remove();
        $(".preview-lg>img").remove();
        $(".preview-sm>img").remove();
        //$(".preview-sm").children('img').remove();
        //$(".preview-lg").children('img').remove();
        $("#Edit_Profile1").show();

    });

});
