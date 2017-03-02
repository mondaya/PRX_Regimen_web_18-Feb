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
                <div class="btn-group"></div>
                </div>
            </div>
            <div class="portlet-body portlet-toggler">
            	<p id="dateFilter">

                        </p>
                <table id="example123" class="table table-striped table-bordered table-hover"></table>
            </div>
            <div class="portlet-body portlet-toggler pageform" style="display:none;"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
function onAddTag(tag) {
		  alert("Added a tag: " + tag);
		}
		function onRemoveTag(tag) {
		  alert("Removed a tag: " + tag);
		}

		function onChangeTag(input,tag) {
		  alert("Changed a tag: " + tag);
		}



$(function() {
	OTable= $('#example123').dataTable({
		bProcessing: true,
		bServerSide: true,
		sAjaxSource: "ajax.<?php echo $this->module;?>.php",
		fnServerData: function (sSource, aoData, fnCallback) {
			$.ajax({
			   dataType: 'json',
			   type: "POST",
			   url: sSource,
			   data: aoData,
			   success: fnCallback
			});
		 },
		 aoColumns: [
			{ sName: "firstName", sTitle : 'User Name'},
			{ sName: "productName", sTitle : 'Product Name'},
			{ sName: "subject", sTitle : 'Subject'},
			{ sName: "paidAmount", sTitle : 'Amount'},
			{ sName: "adminPaid", sTitle : 'Paid Status'}


			<?php if(in_array('pay',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) ){ ?>
				,{ "sName": "operation", 'sTitle' : 'Operation' ,bSortable:false,bSearchable:false}
				<?php } ?>
		],
		fnServerParams: function(aoData){setTitle(aoData, this)},
		fnDrawCallback: function( oSettings ) {
			$('.make-switch').bootstrapSwitch();
			$('.make-switch').bootstrapSwitch('setOnClass', 'success');
			$('.make-switch').bootstrapSwitch('setOffClass', 'danger');
		}
		})
		.columnFilter({ sPlaceHolder: "head:after",
        aoColumns: [
			{ type: "date-range",sRangeFormat: "From: {from} To: {to}",sSelector:"#dateFilter" }
    	]
	});
	$('.dataTables_filter').css({float:'right'});
	$('.dataTables_filter input').addClass("form-control input-inline");

	$.validator.addMethod('pagenm',function (value, element) {
		return /^[a-zA-Z0-9][a-zA-Z0-9\-\_]*$/.test(value);
		},'Page name is not valid. Only alphanumeric and -,_ are allowed'
	);
	$(document).on('submit','#frmCont', function(e){
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
				blogcategory: {required: true},
				blogName: {required: true},
				blogDescription: {required:true},
				tags: {required:true}
			},
			messages:{
				blogcategory: {required: "&nbsp;Please select blog category"},
				blogName: { required: "&nbsp;Please enter blog title"},
				blogDescription: { required: "&nbsp;Please enter blog description"},
				tags: {required: "&nbsp;Please enter blog tag"}
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
});
</script>
