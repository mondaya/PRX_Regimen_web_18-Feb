<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate"> 
	<div class="form-body">
		<div class="clearfix"></div>
			<div class="form-group" id="123"> 
				<label class="control-label col-md-3">Newletter: &nbsp;</label> <div class="col-md-9"> <p class="form-control-static">%NEWS_LTR%</p> </div>
			</div>
			<div class="clearfix"></div>
			<div class="form-group manage-nwsltr"> 
				<label class="control-label col-md-3"><font color="#FF0000">*</font>Send to: &nbsp;</label>
		 		<div class="col-md-6">
		 			<select name="subscriber[]" multiple="" id="subscriber" class="form-control selectBox-bg required">
		 				%SUBSCRIBER%
		 			</select>
		 		</div>
			</div>
			<div class="flclear clearfix"></div>
			<input type="hidden" name="action" id="action" value="send"><div class="flclear clearfix"></div>
			<input type="hidden" name="id" id="id" value="%ID%"><div class="padtop20"></div>
	</div>
	<div class="form-actions fluid">
		<div class="col-md-offset-3 col-md-9">
			<button type="submit" name="submitSendForm" class="btn green" id="submitSendForm">Send</button>
		</div>
	</div>
</form>
<script>
	$('#subscriber').multiselect({includeSelectAllOption: true, nonSelectedText: 'Select Subscriber(s)'});
</script>