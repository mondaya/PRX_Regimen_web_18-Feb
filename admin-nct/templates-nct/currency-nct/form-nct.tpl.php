<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
	 <div class="form-body">
	 	<div class="form-group"> 
	 		<label for="countryName" class="control-label col-md-3">%MEND_SIGN%Currency : &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="text" class="form-control logintextbox-bg required" name="currency" id="currency" value="%CURRENCY%" %DISABLED%>
	 		</div>
	 	</div>
	 	<div class="form-group"> 
	 		<label for="countryName" class="control-label col-md-3">%MEND_SIGN%Code : &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="text" class="form-control logintextbox-bg required" name="code" id="code" value="%CODE%" %DISABLED%>
	 		</div>
	 	</div>
	 	<div class="form-group"> 
	 		<label for="countryName" class="control-label col-md-3">%MEND_SIGN%Sign : &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="text" class="form-control logintextbox-bg required" name="sign" id="sign" value="%SIGN%" %DISABLED%>
	 		</div>
	 	</div>
	 	<div class="form-group"> 
	 		<label for="countryName" class="control-label col-md-3">%MEND_SIGN%Currency Value : &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="text" class="form-control logintextbox-bg required" name="currencyValue" id="currencyValue" value="%CURRENCY_VALUE%">
	 		</div>
	 	</div>
	 	<div class="form-group"> 
	 		<label class="control-label col-md-3">Status: &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<div class="radio-list" data-error-container="#form_2_Status: _error"> 
	 				<label class=""> <input class="radioBtn-bg required" id="y" name="isactive" type="radio" value="y" %STATUS_A%> Active</label>
	 				<span for="isactive" class="help-block"></span> 
	 				<label class=""> <input class="radioBtn-bg required" id="n" name="isactive" type="radio" value="n" %STATUS_D%> Deactive</label>
	 				<span for="isactive" class="help-block"></span> 
	 			</div>
	 			<div id="form_2_Status: _error"></div> 
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