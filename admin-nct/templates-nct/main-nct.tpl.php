<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<?php print $this->head;?>
	</head>
    <?php $class=($this->module=="login-nct")?"login":"page-header-fixed"; ?>
    <body class="<?php echo $class; ?>">
    	<?php print $this->site_header;?>
        <?php if($this->adminUserId>0){ echo '<div class="page-container">';} ?>
        <?php print $this->left; ?>
        <div class="page-content-wrapper">
        <?php if($this->adminUserId>0){ echo '<div class="page-content">';} ?>
        <?php print $this->body; ?>
        <?php if($this->adminUserId>0){ echo '</div>';} ?>
        </div>
        <?php print $this->right; ?>
        <?php if($this->adminUserId>0){ echo '</div>';} ?>
        <?php print $this->footer; ?>

        	<!-- new coding added  start-->
                    <!--[if lt IE 9]>
            <script src="<?php echo SITE_ADM_PLUGIN; ?>respond.min.js"></script>
            <script src="<?php echo SITE_ADM_PLUGIN; ?>excanvas.min.js"></script>
            <![endif]-->
            <!--Main table End-->
            <script src="<?php echo SITE_ADM_PLUGIN; ?>jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
            <!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
            <script src="<?php echo SITE_ADM_PLUGIN; ?>jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_ADM_PLUGIN; ?>bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
             <script type="text/javascript" src="<?php echo SITE_ADM_PLUGIN; ?>bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
            <script src="<?php echo SITE_ADM_PLUGIN; ?>bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_ADM_PLUGIN; ?>jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_ADM_PLUGIN; ?>jquery.blockui.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_ADM_PLUGIN; ?>jquery.cokie.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_ADM_PLUGIN; ?>uniform/jquery.uniform.min.js" type="text/javascript"></script>
            <script src="<?php echo SITE_ADM_PLUGIN; ?>jquery-validation/dist/jquery.validate.min.js" type="text/javascript"></script>
            <script type="text/javascript" src="<?php echo SITE_ADM_PLUGIN; ?>jquery-validation/dist/additional-methods.min.js"></script>
            <script type="text/javascript" src="<?php echo SITE_ADM_PLUGIN; ?>select2/select2.min.js"></script>
            <script type="text/javascript" src="<?php echo SITE_ADM_PLUGIN; ?>ckeditor/ckeditor.js"></script>
            <script type="text/javascript" src="<?php echo SITE_ADM_PLUGIN; ?>bootstrap-toastr/toastr.min.js"></script>
            <script type="text/javascript">
            toastr.options = {
              "closeButton": true,
              "debug": false,
              "positionClass": "toast-top-full-width",
              "onclick": null,
              "showDuration": "300",
              "hideDuration": "1000",
              "timeOut": "5000",
              "extendedTimeOut": "1000",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"
            }
            //toastr['error']('Hello test', '');
            </script>
            <?php if($this->msgType != "" || !empty($this->msgType) ){ echo $this->msgType;} ?>

            <!-- BEGIN PAGE LEVEL SCRIPTS -->
            <script src="<?php echo SITE_ADM_JS; ?>core/app.js" type="text/javascript"></script>
            <script src="<?php echo SITE_ADM_JS; ?>core/admin.js" type="text/javascript"></script>

            <script type="text/javascript">
            jQuery(document).ready(function() {
               App.init();

            });
            $(document).on('click','.btn-delete',function(e){
                e.preventDefault();
                if(confirm("Are you sure to delete record?")){
                    var $this = $(this);
                    var editLink = $this.attr('href');
                    $.get(editLink,function(r){OTable.fnDraw();toastr['success']('<?php echo disMessage(array('var'=>'recDeleted'),false);?>');}).fail(function() {
    alert('you don\'t have permission to access this page.'); // or whatever
});
                }
            });
            $(document).on('click','.btn-send',function(e){
                e.preventDefault();
                if(confirm("Are you sure to send newsletter?")){
                    var $this = $(this);
                    var editLink = $this.attr('href');
                    $.get(editLink,function(r){OTable.fnDraw();toastr['success']('<?php echo disMessage(array('var'=>'newssendsuccess'),false);?>');});
                }
            });
            $(document).on('click','.send',function(e){
                e.preventDefault();
                if(confirm("Are you sure to send password to this dealer?")){
                    var $this = $(this);
                    var editLink = $this.attr('href');
                    $.get(editLink,function(r){OTable.fnDraw();
                    toastr['success']('<?php echo disMessage(array('var'=>'Password sent successfully.'),false);?>');});
                }
            });

            $(document).on('click','.btn-viewbtn',function(e){
                e.preventDefault();
                var $this = $(this);
                var viewLink = $this.attr('href');
                var PageTitle = $this.attr('data-page_title');
                PageTitle = (PageTitle!=null)?PageTitle:'View details';
                $(".modal-title").html(PageTitle);
                $(".modal-body").html('<div class="popup-loader"><img src="<?php echo SITE_ADM_IMG;?>ajax-loading.gif" align="middle" /></div>');
                $("#myModal_autocomplete").modal();
                $.get(viewLink,function(r){$(".modal-body").html(r);});
            });

            function addOverlay(){$('<div id="overlayDocument"><img src="<?php echo SITE_ADM_IMG; ?>ajax-modal-loading.gif" /></div>').appendTo(document.body)}
            function removeOverlay(){$('#overlayDocument').remove();}
            function loadCKE(id){
                var instance = CKEDITOR.instances[id];
                if(instance){ CKEDITOR.remove(instance); }
                CKEDITOR.replace(id,{
                    filebrowserUploadUrl: "<?php echo SITE_ADM_MOD; ?>upload.php",

                });
            }
            $(document).on("click","#close_popup",function(e){
               $("#Edit_Profile1").hide();
              //$(".close").click();
            });
            var img_incr=-1;
            function showdata()
                {
                    var formnew=document.getElementById("avtar_form");
                    //var a=form.imageupload.value;
                    //var formdata = $("#projectCreatefield").serialize();
                    var formData = new FormData(formnew);
                    var pathname = window.location.pathname.split('/');
                    var mod=pathname['4'];
                    var which_types=$("#hidden_image_id").html();
                    $("#which_types").val(which_types);
                    if(which_types=='images' || which_types=='header_slider'){ var url_send='crop.php'; }
                    $(window).scrollTop(0);
                    $(".avatar-wrapper").append("<img class='loading' src='<?php echo SITE_IMG;?>loading.gif' style='margin-left:300px; margin-top:100px;'/>");
                    jQuery.ajax({
                      url: url_send,
                      type: 'post',
                      dataType:'json',
                      data:  formData,
                      processData: false,  // tell jQuery not to process the data
                      contentType: false ,  // tell jQuery not to set contentType
                      enctype: 'multipart/form-data',
                      mimeType: 'multipart/form-data',
                      cache: false,
                      success: function(data) {
                        //window.location.href=data.url;
                        //$('#thumb_video').attr('src', data)
                              if(data.state == 200 && data.message !='' && data.message != null )
                              {

                                  $("#Edit_Profile1").hide();
                                  $(".close").click();
                                  toastr["error"](data.message);

                              }else{
                                  var mod = '<?php echo $this->module; ?>';
                                  var src = '';
                                  if(mod == '<?php echo $this->module; ?>') {
                                    var src="<?php echo SITE_UPD.'temp_files/';?>"+"th1_"+data['filename'];
                                  }
                                  $("#Edit_Profile1").hide();
                                  if(which_types=='images' || which_types=='header_slider'){
                                    img_incr=img_incr+1;
                                    $('#dvPreview').css({'margin-left':'5px'});
                                    $('#dvPreview').append('<div style="float:left;" class="delete_images"><img src="'+src +'" height="100px" width="100px" style="margin-right: 5px; border: 1px dashed;"><span style="position:absolute;margin-left:-15px;cursor:pointer" data-image_id='+img_incr+' class="fa fa-times delete_images_btn_direct"></span></div>');
                                    $('#business_hiddent_images').val(src);
                                  }
                              }
                              $(".loading").hide();
                        }

                    });
                }

            </script>

            <script type="text/javascript">
              $(document).on('click', '.delete_images_btn_direct', function(event) {

                    //$(this).parent('.delete_services').remove();

                    var del_confirm= confirm("Do you want to Delete it?");
                    if(del_confirm){
                    var image_id=$(this).data('image_id');
                    $this=$(this);
                     $.ajax({
                  type: "POST",
                  dataType:'json',
                  url: "ajax.<?php echo $this->module;?>.php",
                  data: {'action':'delete_images_direct','image_id':image_id},
                  cache: false,
                  success: function(dataRes) {

                    $this.closest('.delete_images').remove();

                  }
                });
                    }
              });

            </script>
            <?php echo load_js($this->scripts); ?>
            <!-- END PAGE LEVEL SCRIPTS -->
            <!-- new coding added  start-->


    </body>
</html>
