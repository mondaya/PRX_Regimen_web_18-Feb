<script type="text/javascript">
$(function() {
 OTable = $('#example123_activity').dataTable({
			"bProcessing": true,
			"bServerSide": true,
			"bFilter": false,
			"bSort": false,
			"sAjaxSource": "ajax.<?php echo $this->module;?>.php?action=activity_datagrid&id=<?php echo $this->id; ?>",
			"fnServerData": function (sSource, aoData, fnCallback) {
				$.ajax({
				   "dataType": 'json',
				   "type": "POST",
				   "url": sSource,
				   "data": aoData,
				   "success": fnCallback
				});
			 },
			 "aaSorting": [],
			 "aoColumns": [
				{ "sName": "Activity", 'sTitle' : 'Activity'},
				{ "sName": "Date", 'sTitle' : 'Date'}
			],
			"fnServerParams": function(aoData){setTitle(aoData, this)}
   });
});
</script>

<div class="row">
    <div class="col-md-12">
    <!-- Begin: life time stats -->
        <div class="portlet box blue-dark">
            <div class="portlet-title ">
                <div class="caption"><i class="fa fa-list-alt"></i>View All Activities</div>
                <div class="actions portlet-toggler">
                	 <?php
					 	if(in_array('delete',$this->Permission)){
					 ?>
	                    <a href="ajax.<?php echo $this->module; ?>.php?action=delete_activity&id=<?php echo $this->id; ?>" class="btn default btn-xs red btn-delete" ><i class="fa fa-trash-o"></i> Delete All Activities</a>
    	               <?php } ?>
                    <div class="btn-group"></div>
                </div>
            </div>
            <div class="portlet-body portlet-toggler">
<table id="example123_activity" class="table table-striped table-bordered table-hover">
</table>
</div>
<div class="portlet-toggler pageform" style="display:none;"> </div>
</div>
</div>
</div>
