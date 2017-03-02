<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
	<div class="form-body">
		
		<div class="form-group"> 
	 		<label for="countryName" class="control-label col-md-3">%MEND_SIGN%Product Name : &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="text" class="form-control logintextbox-bg required" name="productName" id="productName" value="%PRODUCT_NAME%">
	 		</div>
	 	</div>
	 	
	 	<div class="form-group">
			<label class="control-label col-md-3"> %MEND_SIGN%
				Product Category:&nbsp;
			</label>
			<div class="col-md-4">
				<select name="categoryId" id="categoryId" class="form-control selectBox-bg required">
				<option value="">Please select category</option>
				%CATEGORY_OPTION%
				</select>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-md-3"> %MEND_SIGN%
				Product Sub Category:&nbsp;
			</label>
			<div class="col-md-4">
				<select name="subcategoryId" id="subcategoryId" class="form-control selectBox-bg required">%SUBCATEGORY_OPTION%</select>
			</div>
		</div>
		
	 	<div class="form-group"> 
	      <label for="image" class="control-label col-md-3">Product Image:&nbsp;</label> 
	       <div class="col-md-6">
	          <div id="dvPreview">
	          <div class="">
	           <input type="hidden" class="places_image" name="productImage" id="productImage">
	                      
	           </div>
	        </div>
	        
	      </div>
	    </div>
	    <div class="form-group"> 
	      <label for="image" class="control-label col-md-3"> &nbsp;</label> 
	       <div   class="col-md-6">
	              <img src="%ADD_MORE%"  class="places_image" alt="" width="50px" height="50px">
	      </div>
	    </div>

	 	%PRODUCT_PHOTO%

	 	<div class="form-group"> 
	 		<label for="countryName" class="control-label col-md-3">%MEND_SIGN%Quantity : &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="number" min="0" class="form-control logintextbox-bg required" name="quantity" id="quantity" value="%PRODUCT_QUA%">
	 		</div>
	 	</div>

	 	<div class="form-group">
     		<label class="control-label col-md-3">%MEND_SIGN%Description: &nbsp;</label>
      		<div class="col-md-4">
        		<textarea class="form-control textarea-bg" name="productDescription" id="productDescription">%DESCRIPTION%</textarea> 
      		</div>
    	</div>

    	<div class="form-group"> 
	 		<label for="countryName" class="control-label col-md-3">%MEND_SIGN%Price(%SITE_CURR%) : &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="number" min="0" class="form-control logintextbox-bg required" name="actualPrice" id="actualPrice" value="%PRODUCT_PRICE%">
	 		</div>
	 	</div>
		
	 	<div class="form-group">
			<label class="control-label col-md-3">%MEND_SIGN%Allow Discount: &nbsp;</label>
			<div class="col-md-4">
				<div class="radio-list" data-error-container="#form_2_Status: _error">
					<label class="">
						<input class="radioBtn-bg required" id="y" name="isDiscount" type="radio" value="y" %DISCOUNT_Y%>Yes</label>
					<span for="isDiscount" class="help-block"></span>
					<label class="">
						<input class="radioBtn-bg required" id="n" name="isDiscount" type="radio" value="n" %DISCOUNT_N%>No</label>
					<span for="isDiscount" class="help-block"></span>
				</div>
				<div id="form_2_Status: _error"></div>
			</div>
		</div>

		<div class="form-group discountPrice" %DISPLAY_DISCOUNT%> 
	 		<label for="countryName" class="control-label col-md-3">%MEND_SIGN%Discount Price : &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="number" min="0" class="form-control logintextbox-bg" name="discountPrice" id="discountPrice" value="%DISCOUNT_PRICE%">
	 		</div>
	 	</div>

	 	<div class="form-group"> 
	 		<label for="countryName" class="control-label col-md-3">%MEND_SIGN%Weight : &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="number" min="0" class="form-control logintextbox-bg required" name="weight" id="weight" value="%WEIGHT%">
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

<script type="text/javascript">
$(document).ready(function() {
	$('#categoryId').change(function() {
	        var categoryId = $('#categoryId').val();
			if(categoryId != ''){
				$.ajax({
			         url:"<?php echo SITE_ADM_MOD; ?>products-nct/ajax.products-nct.php",
			         type:"post",
			         data: {action:'changeSubcate','category':categoryId},
			         success: function(dataSubcate){
			          $('#subcategoryId').empty(); 	
			          $('#subcategoryId').append(dataSubcate);
			         }
			    });   
			}     
	    });

	$("input:radio[name=isDiscount]").change(function(e){
	    if ($(this).val() === 'y') {
	      $('.discountPrice').show();
	    } else if ($(this).val() === 'n') {
	      $('.discountPrice').hide();
	    } 
	});

});

</script>