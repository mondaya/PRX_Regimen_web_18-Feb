    <script type="text/javascript">
        $(function() {
	  OTable = $('#example123').dataTable({
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
				{ "sName": "uName", 'sTitle' : 'Name'},
				{ "sName": "email", 'sTitle' : 'Email',bSortable:false},
				{ "sName": "created_date", 'sTitle' : 'Created Date'},
				{ "sName": "updated_date", 'sTitle' : 'Updated Date'}
				<?php if(in_array('status',$this->Permission)){ ?>
				,{ "sName": "status", 'sTitle' : 'Status' ,bSearchable:false}
				<?php } ?>
				<?php if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) ){ ?>
				,{ "sName": "operation", 'sTitle' : 'operation' ,bSortable:false,bSearchable:false}
				<?php } ?>
			],
			"fnServerParams": function(aoData){setTitle(aoData, this)},
			"fnDrawCallback": function( oSettings ) {
				$('.make-switch').bootstrapSwitch();
				$('.make-switch').bootstrapSwitch('setOnClass', 'success');
				$('.make-switch').bootstrapSwitch('setOffClass', 'danger');
			}
   });
	$('.dataTables_filter').css({float:'right'});
	$('.dataTables_filter input').addClass("form-control input-inline"); 

	$.validator.addMethod('pagenm',function (value, element) { 
		return /^[a-zA-Z0-9][a-zA-Z0-9\_\-]*$/.test(value); 
		},'Page name is not valid. Only alphanumeric and _ are allowed'
	);
	$(document).on('submit','#frmCont', function(e){
		$(".chk_group").prop('disabled',false);
		$("#frmCont").on('submit', function() {
			for(var instanceName in CKEDITOR.instances) {
				CKEDITOR.instances[instanceName].updateElement();
			}
		})
		$("#frmCont").validate({
			ignore:[],
			errorClass: 'help-block',
			errorElement: 'span',
			rules:{
				txt_uname:{
					pagenm:true,
					remote:{
						url:"<?php echo SITE_ADM_MOD.$this->module ?>/ajax.<?php echo $this->module;?>.php",
						type: "post",
						async:false,
						data: {ajaxvalidate:true,action:'checkUname',txt_uname: function() {return $("#txt_uname").val();},id: function() {return $("#id").val();}},
						complete: function(data){
							return data;
						}
					}
				},
				txt_email:{
					email:true,
					remote:{
						url:"<?php echo SITE_ADM_MOD.$this->module ?>/ajax.<?php echo $this->module;?>.php",
						type: "post",
						async:false,
						data: {ajaxvalidate:true,action:'checkEmail',txt_email: function() {return $("#txt_email").val();},id: function() {return $("#id").val();}},
						complete: function(data){
							return data;
						}
					}
				}
			},
			messages:{
				txt_email:{remote:'Email already exist'},
				txt_uname:{remote:'UserName already exist'}
			},
            highlight: function (element) {
			   $(element).closest('.form-group').addClass('has-error');
			},
			unhighlight: function (element) {
				$(element).closest('.form-group').removeClass('has-error');
			},
			errorPlacement: function (error, element) { 
				if (element.attr("data-error-container")) { 
					error.appendTo(element.attr("data-error-container"));
				} else {
					error.insertAfter(element);
				}
            }
		});
		if($("#frmCont").valid()){
			return true;
		}else{
			return false;
		}
	});
	$(document).on("click",".chk_group",function(){
		var page_name = $(this).attr("data-page");
		var page_id = $(this).attr("data-page_id");
		
		if($(this).val()!='1'){
			var len = $(".chk_"+page_name+"_"+page_id+":checked").length;
			if($(this).prop("checked")){
				$( ".chk_"+page_name+"_"+page_id).first().prop('checked',true);
				$( ".chk_"+page_name+"_"+page_id).first().prop('disabled',true);
			}
			else if(len<2){
				//$( ".chk_"+page_name+"_"+page_id).first().prop('checked',false);
				$( ".chk_"+page_name+"_"+page_id).first().prop('disabled',false);
			}
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
                <div class="caption"><i class="fa fa-list-alt"></i><?php echo $this->headTitle; ?></div>
                <div class="actions portlet-toggler">
                	 <?php
					 	if(in_array('add',$this->Permission)){
					 ?>
                	 <a href="ajax.<?php echo $this->module;?>.php?action=add" class="btn blue btn-add"><i class="fa fa-plus"></i> Add</a>
                     <?php } ?>
                    <div class="btn-group"></div>
                </div>
            </div>
            <div class="portlet-body portlet-toggler">
                <table id="example123" class="table table-striped table-bordered table-hover"></table>
            </div>
            <div class=" portlet-toggler pageform" style="display:none;"></div>
        </div>
    </div>
</div>     