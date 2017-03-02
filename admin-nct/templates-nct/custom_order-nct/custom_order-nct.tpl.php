 <style type="text/css">
 	.cls-hidden { display: none; }
 </style>
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

            <!-- filter menu start-->
            <!-- filter menu start-->

            <div class="portlet-body portlet-toggler">
            	<div class="portlet-body portlet-toggler" id="filter_box">
            	<!-- Date filter -->
				<div class="col-md-12" id="dateFilter"></div>
				<div style="height:52px;"></div>
            </div>


            
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
				{ sName: "createdDate", sTitle : 'Created Date', sClass:'cls-hidden'},
				{ sName: "firstName", sTitle : 'Name'},
				{ sName: "email", sTitle : 'User Email',bSortable:false},
				{ sName: "productName", sTitle : 'Product name'},
				{ sName: "productPrice", sTitle : 'Total amount'},
				{ sName: "productUrl", sTitle : 'Product URL',bSortable:false},
				{ sName: "order_status", sTitle : 'Order Status'}
				<?php if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) ){ ?>
					,{ "sName": "operation", 'sTitle' : 'Operation',bSortable:false, bSearchable:false}
				<?php } ?>
			],
			fnServerParams: function(aoData){ setTitle(aoData, this) },
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
	});
</script>
