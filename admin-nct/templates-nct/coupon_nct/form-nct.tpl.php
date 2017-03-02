<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
 <div class="form-body">
 	<div class="form-group">
 		<label for="firstName" class="control-label col-md-3"><font color="#FF0000">*</font>Promo code: &nbsp;</label>
 		<div class="col-md-4"> <input type="text" class="form-control logintextbox-bg required" name="coupon_code" id="coupon_code" value="%COUPON_CODE%"></div>
 		<?php if(empty($this->id)) { ?>
 			<div class="col-md-4"><a id="generate" name="generate" href="javascript:void(0);" class="btn yellow"><i class="fa fa-pencil"></i> Generate</a></div>
 		<?php } ?>
 	</div>
 	<div class="form-group">
 		<label for="firstName" class="control-label col-md-3"><font color="#FF0000">*</font>Start date of Promo code: &nbsp;</label>
 		<div class="col-md-4"> <input type="text" class="form-control logintextbox-bg required datepicker" name="start_date" id="start_date" value="%START_DATE%"> </div>
 	</div>
 	<div class="form-group">
 		<label for="firstName" class="control-label col-md-3"><font color="#FF0000">*</font>End date of Promo code: &nbsp;</label>
 		<div class="col-md-4"> <input type="text" class="form-control logintextbox-bg required datepicker" name="end_date" id="end_date" value="%END_DATE%"> </div>
 	</div>
 	<div class="form-group">
 		<label for="firstName" class="control-label col-md-3"><font color="#FF0000">*</font>Discount(%): &nbsp;</label>
 		<div class="col-md-4"> <input type="number" class="form-control logintextbox-bg required" name="discount" id="discount" value="%DISCOUNT%"> </div>
 	</div>
 	<div class="form-group">
 	 	<label class="control-label col-md-3">Status: &nbsp;</label>
 	 	 <div class="col-md-4">
 	 	 	<div class="radio-list" data-error-container="#form_2_Status: _error">
 	 	 <label class=""> <input class="radioBtn-bg required" id="y" name="status" type="radio" value="y" %STATUS_A%> Active</label><span for="status" class="help-block"></span>
 	 	 <label class="">
 	 	 <input class="radioBtn-bg required" id="n" name="status" type="radio" value="n" %STATUS_D%> Deactive</label><span for="status" class="help-block"></span>
 	 	 </div>
 	 	 <div id="form_2_Status: _error"></div>
 	 	  </div>
 	</div>
 	<div class="flclear clearfix">
 </div>
  <input type="hidden" name="type" id="type" value="%TYPE%"><div class="flclear clearfix"></div>
  <input type="hidden" name="id" id="id" value="%ID%"><div class="padtop20"></div>
</div>
<div class="form-actions fluid">
	<div class="col-md-offset-3 col-md-9">
		<button type="submit" name="submitAddForm" class="btn green" id="submitAddForm">Submit</button><button type="button" name="cn" class="btn btn-toggler" id="cn">Cancel</button>
	</div>
</div>
</form>
<script type="text/javascript">
	$(document).ready(function() {
		$(".datepicker").datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			startDate: "d"
		});

		$(document).on('click', '#generate', function() {
			var len = 10; var coupon = '';
			var charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    	for( var i=0; i < len; i++ ){ coupon += charset.charAt(Math.floor(Math.random() * charset.length)); }
	    	$('#coupon_code').val(coupon);
		});
	});
</script>