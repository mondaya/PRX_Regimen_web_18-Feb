<script type="text/javascript">
    $(function() {
	  	OTable = $('#example123').dataTable( {
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "ajax.<?php echo $this->module;?>.php",
			"fnServerData": function (sSource, aoData, fnCallback) {
				$.ajax({
				   "dataType": 'json',
				   "type": "POST",
				   "url": sSource,
				   "data": aoData,
				   "success": fnCallback
				});
			 },
			 "aoColumns": [
				{ "sName": "banner_name", 'sTitle' : 'Banner Name'},
				{ "sName": "banner_image", 'sTitle' : 'Banner Image', bSearchable:false, bSortable: false},
				{ "sName": "banner_link", 'sTitle' : 'Banner Link'}
				<?php if(in_array('status',$this->Permission)){ ?>
					,{ "sName": "isActive", 'sTitle' : 'Status' ,bSearchable:false}
				<?php } ?>
				<?php if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) ){ ?>
					,{ "sName": "operation", 'sTitle' : 'Operation' ,bSortable:false,bSearchable:false}
				<?php } ?>
			],
			"fnServerParams": function(aoData){setTitle(aoData, this);},
			"fnDrawCallback": function( oSettings ) {
				$('.make-switch').bootstrapSwitch();
				$('.make-switch').bootstrapSwitch('setOnClass', 'success');
				$('.make-switch').bootstrapSwitch('setOffClass', 'danger');
			}
   		});
		$('.dataTables_filter').css({float:'right'});
		$('.dataTables_filter input').addClass("form-control input-inline");

		$(document).on('submit','#frmCont', function(e){
			$("#frmCont").validate({
				ignore:[],
				errorClass: 'help-block',
				errorElement: 'span',
				rules: {
					banner_name: { required: true },
					banner_image: {extension: 'jpg|jpeg|png' },
					banner_link: { required: true, url: true }
				},
				messages:{
					banner_name: { required: 'Please enter banner name' },
					banner_image: {extension: 'Only jpeg and png image type are supported.' },
					banner_link: { required: 'Please enter banner link', url: 'Please enter valid url Ex: http://www.website.com' },
					banner_image:{ required: 'Please select banner image' }
				},
	            errorPlacement: function (error, element) {
					if (element.attr("data-error-container")) {
						error.appendTo(element.attr("data-error-container"));
					} else {
						error.insertAfter(element);
					}
	            },
	            highlight: function (element) {
				   $(element).closest('.form-group').addClass('has-error');
				},
				unhighlight: function (element) {
					$(element).closest('.form-group').removeClass('has-error');
				}
			});
			$('#banner_image').on('change', function() {
			       $(this).valid();                  
			});
			if($("#frmCont").valid()){
				return true;
			}else{
				return false;
			}
		});

		
	});
</script>
 <!-- BEGIN PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN PAGE TITLE & BREADCRUMB-->
           	<?php
				echo $this->breadcrumb;
			?>
            <!-- END PAGE TITLE & BREADCRUMB-->
        </div>
    </div>
    <!-- END PAGE HEADER-->
<div class="row">
    <div class="col-md-12">
    <!-- Begin: life time stats -->
        <div class="portlet box blue-dark">
            <div class="portlet-title ">
                <div class="caption">
                	<i class="fa fa-dot-circle-o"></i><?php echo $this->headTitle; ?>
                </div>
                <div class="actions portlet-toggler">
                    <?php if(in_array('add',$this->Permission)){?>
                    	<a href="ajax.<?php echo $this->module;?>.php?action=add" class="btn blue btn-add"><i class="fa fa-plus"></i> Add</a>
                    <?php } ?>
                    <div class="btn-group"></div>
                </div>
            </div>

            <!-- filter menu start-->
            <div class="portlet-body portlet-toggler">
            </div>
            <!-- filter menu start-->

            <div class="portlet-body portlet-toggler">
                <table id="example123" class="table table-striped table-bordered table-hover"></table>
            </div>
			
            <div class="portlet-toggler pageform" style="display:none;"></div>
        </div>
    </div>
</div>