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
				{ "sName": "coupon_code", 'sTitle' : 'Promo code'},
				{ "sName": "start_date", 'sTitle' : 'Star date of Promo code'},
				{ "sName": "end_date", 'sTitle' : 'Expiration date of Promo code'},
				{ "sName": "discount", 'sTitle' : ' Discount (In %)'}
				<?php if(in_array('status',$this->Permission)){ ?>
				,{ "sName": "isActive", 'sTitle' : 'Status' ,bSearchable:false,bSortable:false}
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
		OTable.fnSort([[0,'desc']]);

		$.validator.addMethod('minStrict', function (value, el, param) {
		    return value > param;
		});

		$(document).on('submit','#frmCont', function(e){
			$("#frmCont").validate({
				ignore:[],
				errorClass: 'help-block',
				errorElement: 'span',
				rules: {
					coupon_code: { required: true },
					start_date: { required: true },
					end_date: { required: true, minStrict: function() { return $('#start_date').val(); } },
					discount: { required: true }
				},
				messages:{
					coupon_code: { required: 'Please enter coupon code' },
					start_date: { required: 'Please select start date' },
					end_date: { required: 'Please select end date', minStrict: 'Plase select valid date' },
					discount: { required: 'Please enter discount' }
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