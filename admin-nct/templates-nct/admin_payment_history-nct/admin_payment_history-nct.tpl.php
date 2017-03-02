<script type="text/javascript">
    $(function() {
		$('#dateFilter').hide();
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
				{ "sName": "firstName", 'sTitle' : 'Name'},
				{ "sName": "amount", 'sTitle' : 'Amount'},
				{ "sName": "transactionType", 'sTitle' : 'Payment Type'},
				{ "sName": "createdDate", 'sTitle' : 'Payment date'}
				<?php if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) ){ ?>
				,{ "sName": "operation", 'sTitle' : 'Operation' ,bSortable:false,bSearchable:false}
				<?php } ?>
			],
			"fnServerParams": function(aoData){setTitle(aoData, this);},
			fnDrawCallback: function( oSettings ) {
				$('.make-switch').bootstrapSwitch();
				$('.make-switch').bootstrapSwitch('setOnClass', 'success');
				$('.make-switch').bootstrapSwitch('setOffClass', 'danger');
			}
   		})
   		.columnFilter({ sPlaceHolder: "head:after",
			aoColumns: [
				{ type: "date-range", sRangeFormat: "From: {from} To: {to}",sSelector:"#dateFilter" }
			]
		});
		$('.dataTables_filter').css({float:'right'});
		$('.dataTables_filter input, .date_range_filter').addClass("form-control input-inline");
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
                	<div id="filter" class="btn yellow" onclick="$('#dateFilter').toggle();"><i class="fa fa-filter"></i> Search Filter</div>
                	<div class="btn-group"></div>
                </div>
            </div>

            <!-- filter menu start-->
            <div class="portlet-body portlet-toggler">
            	<div class="col-md-12" id="dateFilter"></div>
            </div>
            <!-- filter menu start-->

            <div class="portlet-body portlet-toggler">
                <table id="example123" class="table table-striped table-bordered table-hover"></table>
            </div>
            <div class="portlet-toggler pageform" style="display:none;"></div>
        </div>
    </div>
</div>