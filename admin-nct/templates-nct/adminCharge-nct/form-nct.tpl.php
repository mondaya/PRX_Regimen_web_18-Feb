<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
	<div class="form-body">
		<div class="form-group">
			<label class="control-label col-md-3"> %MEND_SIGN%
				Country:&nbsp;
			</label>
			<div class="col-md-4">
				<select name="country" id="country" class="form-control selectBox-bg required">
					<option value="">Select country</option>
					%COUNTRY_OPTION%
				</select>
			</div>
		</div>

		<div class="form-group">
	 		<label for="countryName" class="control-label col-md-3">%MEND_SIGN%Admin Charge(%) : &nbsp;</label>
	 		<div class="col-md-4">
	 			<input type="number" min="1" class="form-control logintextbox-bg required" name="amount" id="amount" value="%ADMIN_CHARGE%">
	 		</div>
	 	</div>

	 	<div class="form-group">
	 		<label for="countryName" class="control-label col-md-3">%MEND_SIGN%Minimum Admin Charge(%SITE_CURR%) : &nbsp;</label>
	 		<div class="col-md-4">
	 			<input type="number" min="1" class="form-control logintextbox-bg required" name="minimumAmount" id="minimumAmount" value="%MINIMUM_AMOUNT%">
	 		</div>
	 	</div>

		<div class="form-group">
			<label class="control-label col-md-3">Status: &nbsp;</label>
			<div class="col-md-4">
				<div class="radio-list" data-error-container="#form_2_Status: _error">
					<label class="">
						<input class="radioBtn-bg required" id="y" name="isActive" type="radio" value="y" %STATUS_A%>Active</label>
					<span for="isActive" class="help-block"></span>
					<label class="">
						<input class="radioBtn-bg required" id="n" name="isActive" type="radio" value="n" %STATUS_D%>Deactive</label>
					<span for="isActive" class="help-block"></span>
				</div>
				<div id="form_2_Status: _error"></div>
			</div>
		</div>
		<div class="flclear clearfix"></div>
		<input type="hidden" name="type" id="type" value="%TYPE%">
		<div class="flclear clearfix"></div>
		<input type="hidden" name="id" id="id" value="%ID%">
		<div class="padtop20"></div>
	</div>
	<div class="form-actions fluid">
		<div class="col-md-offset-3 col-md-9">
			<button type="submit" name="submitAddForm" class="btn green" id="submitAddForm">Submit</button>
			<button type="button" name="cn" class="btn btn-toggler" id="cn">Cancel</button>
		</div>
	</div>
</form>

