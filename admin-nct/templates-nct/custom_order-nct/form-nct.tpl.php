<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
	<div class="form-body">
		
		<div class="form-group"> 
	 		<label for="countryName" class="control-label col-md-3">Product Name : &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="text" class="form-control logintextbox-bg required" name="productName" id="productName" value="%PRODUCT_NAME%" readOnly>
	 		</div>
	 	</div>
	 	
	 	
		<div class="form-group">
			<label class="control-label col-md-3">Order Status: &nbsp;</label>
			<div class="col-md-4">
				<div class="radio-list" data-error-container="#form_2_Status: _error">
					<label class="">
						<input class="radioBtn-bg required" id="a" name="order_status" type="radio" value="a" %STATUS_A%>Accepted</label>
					<span for="isActive" class="help-block"></span>
					<label class="">
						<input class="radioBtn-bg required" id="n" name="order_status" type="radio" value="r" %STATUS_R%>Rejected</label>
					<span for="isActive" class="help-block"></span>
					<label class="">
						<input class="radioBtn-bg required" id="n" name="order_status" type="radio" value="p" %STATUS_P%>Pending</label>
					<span for="isActive" class="help-block"></span>
				</div>
				<div id="form_2_Status: _error"></div>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-md-3">Delivery Status: &nbsp;</label>
			<div class="col-md-4">
				<div class="radio-list" data-error-container="#form_2_Status: _error">
					<label class="">
						<input class="radioBtn-bg required" id="n" name="deliveryStatus" type="radio" value="s" %DELIVERY_S% %DISABLED%>Shipped</label>
					<span for="isActive" class="help-block"></span>
					<label class="">
						<input class="radioBtn-bg required" id="n" name="deliveryStatus" type="radio" value="d" %DELIVERY_D% %DISABLED%>Delivered</label>
					<span for="isActive" class="help-block"></span>
					<label class="">
						<input class="radioBtn-bg required" id="a" name="deliveryStatus" type="radio" value="p" %DELIVERY_P% %DISABLED%>Pending</label>
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

