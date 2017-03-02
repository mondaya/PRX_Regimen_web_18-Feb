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
                	<i class="fa fa-shopping-cart"></i><?php echo $this->headTitle; ?>
                </div>
                <div class="actions portlet-toggler">
                <div id="filter" class="btn yellow"><i class="fa fa-filter"></i> Search Filter</div>
                <div class="btn-group"></div>
                </div>

            </div>

            

            <div class="portlet-body portlet-toggler">
            	
            	<!-- filter menu start-->
	            <div class="portlet-body portlet-toggler" id="filter_box">
	            	<!-- Date filter -->
					<div class="col-md-12" id="dateFilter"></div>

					<!-- country state filters -->
					<div class="col-md-3">
	                    <label>Country :</label>&nbsp;
	                    <select id="country" name="country" class="form-control required select_field">
	                    	%COUNTRY%
	                    </select>
	                </div>
	                <div class="col-md-3">
	                    <label>State :</label>&nbsp;
	                    <select id="state" name="state" class="form-control required select_field">
	                    	<option value="">Select State</option>
	                    </select>
	                </div>

	                <div class="col-md-3">
	                    <label>Delivery Status :</label>&nbsp;
	                    <select id="status" name="status" class="form-control required select_field">
	                    	<option value="">Select Delivery Status</option>
	                    	<option value="p">Pending</option>
	                    	<option value="s">Shipped</option>
	                    	<option value="d">Delivered</option>
	                    	<option value="r">Returned</option>
	                    </select>
	                </div>
	            </div>
	            <!-- filter menu start-->

                <table id="example123" class="table table-striped table-bordered table-hover"></table>
            </div>
            <div class="portlet-body portlet-toggler pageform" style="display:none;"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#filter_box').hide();
		$('#filter').click(function(){ $('#filter_box').slideToggle(300); });

		$(document).on('change', '#country', function() {
			var _this = $(this);
			var value = _this.val();
			if(value && value!='')  {
				addOverlay();
				$.ajax({
					url: "ajax.<?php echo $this->module;?>.php",
					type: 'POST',
					dataType: 'json',
					data: {action: 'getState', country_id: value},
				})
				.done(function(data) {
					$('#state').html(data.state_option);
				})
				.always(function() {
					removeOverlay();
				});
			}
		});
	});

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
				{ sName: "firstName", sTitle : 'Name'},
				{ sName: "email", sTitle : 'User Email',bSortable:false},
				{ sName: "productName", sTitle : 'Product name'},
				{ sName: "paidAmount", sTitle : 'Total amount', bSearchable:false}
				<?php if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) ){ ?>
					,{ "sName": "operation", 'sTitle' : 'Operation', bSortable:false, bSearchable:false}
				<?php } ?>
			],
			fnServerParams: function(aoData){
				setTitle(aoData, this);
				var country = $("#country").val(); 
				var state = $("#state").val();
				var status = $("#status").val();
				if(country && country!=''){ aoData.push({ "name": "country", "value": country}); }
				if(state && state!=''){ aoData.push({ "name": "state", "value": state}); }
				if(status && status!=''){ aoData.push({ "name": "status", "value": status}); }
			},
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
		$('.date_range_filter').addClass("form-control input-inline");
		$('.date_range_filter').attr("readonly", "readonly");

		// var oTable = $('#example123').dataTable();
		$('#country, #state,#status').on('change', function(e){
			oTable.fnDraw();
		});
	});
</script>
