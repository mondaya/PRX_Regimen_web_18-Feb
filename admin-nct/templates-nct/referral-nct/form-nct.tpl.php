<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
	 <div class="form-body">
	 	<div class="form-group"> 
	 		<label for="amount" class="control-label col-md-3">%MEND_SIGN%Referral Amount (%) : &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="number" min="0" class="form-control logintextbox-bg required" name="referral" id="referral" value="%REFERRAL%">
	 		</div>
	 	</div>
	 	
	 	<div class="flclear clearfix"></div>
	 	<input type="hidden" name="type" id="type" value="%TYPE%"><div class="flclear clearfix"></div>
	 	<input type="hidden" name="id" id="id" value="%ID%"><div class="padtop20"></div>
	 </div>
	 <div class="form-actions fluid">
	 	<div class="col-md-offset-3 col-md-9">
	 		<button type="submit" name="submitAddForm" class="btn green" id="submitAddForm">Submit</button>
	 		<button type="button" name="cn" class="btn btn-toggler" id="cn">Cancel</button>
	 	</div>
	 </div>
</form>