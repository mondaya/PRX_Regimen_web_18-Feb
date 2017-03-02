<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
 <div class="form-body">
 	<div class="form-group">
 		<label for="banner_name" class="control-label col-md-3"><font color="#FF0000">*</font>Store Name: &nbsp;</label>
 		<div class="col-md-4"> <input type="text" class="form-control logintextbox-bg required" name="storeName" id="storeName" value="%STORE_NM%"></div>
 	</div>
 	<div class="form-group">
		<label class="control-label col-md-3"> <font color="#FF0000">*</font>Category:&nbsp;
		</label>
		<div class="form-inline select-stores col-md-4">
  			<div class="form-group comment">
			<select name="categoryId[]" class="form-control selectBox-bg required" id="categoryId" multiple="multiple">
			%CATEGORY_OPTION%
			</select>
		</div>
	   </div>
	  </div>
	</div>

	<div class="form-group">
		<label class="control-label col-md-3"><font color="#FF0000">*</font>Sub Category:&nbsp;
		</label>
		<div class="form-inline select-stores col-md-4">
  			<div class="form-group comment subcate">
			<select name="subcategoryId[]" id="subcategoryId" class="form-control selectBox-bg required" multiple="multiple">%SUBCATEGORY_OPTION%</select>
		</div>
	   </div>
	  </div>
	</div>
 	<div class="form-group">
 		<label for="banner_name" class="control-label col-md-3"><font color="#FF0000">*</font>Store Link: &nbsp;</label>
 		<div class="col-md-4"> <input type="url" class="form-control logintextbox-bg required" name="storeLink" id="storeLink" value="%STORE_LINK%"></div>
 	</div>
 	 <div class="form-group">
 		<label for="banner_name" class="control-label col-md-3"><font color="#FF0000">*</font>Store Cart Link: &nbsp;</label>
 		<div class="col-md-4"> <input type="url" class="form-control logintextbox-bg required" name="storeCartLink" id="storeCartLink" value="%STORE_CART_LINK%"></div>
 	</div>
 	<div class="form-group">
 		<label for="banner_image" class="control-label col-md-3"><font color="#FF0000">*</font>Store Image: &nbsp;</label>
 		<div class="col-md-4">
 			<input type="file" class="form-control logintextbox-bg" name="storeImage" id="storeImage" />
	 		<?php if(!empty($this->id)) { ?>
	 			<label style="margin-top: 20px;"><font color="#FF0000">Current image</font></label><br />
	 			<img src="%STORE_SRC%" height="100" width="100" />
	 		<?php } ?>
			</div>
 	</div>
 	<!-- <div class="form-group">
 	 	<label class="control-label col-md-3">Scrap Status: &nbsp;</label>
 	 	 <div class="col-md-4">
 	 	 	<div class="radio-list" data-error-container="#form_2_Status: _error">
 	 	 <label class=""> <input class="radioBtn-bg required" id="y" name="isScrap" type="radio" value="y" %S_STATUS_A%> scrapped</label><span for="isScrap" class="help-block"></span>
 	 	 <label class="">
 	 	 <input class="radioBtn-bg required" id="n" name="isScrap" type="radio" value="n" %S_STATUS_D%> Not scrapped</label><span for="isScrap" class="help-block"></span>
 	 	 </div>
 	 	 <div id="form_2_Status: _error"></div>
 	 	  </div>
 	</div> -->
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
  <input type="hidden" name="old_image" id="old_image" value="%OLD_IMAGE%"><div class="flclear clearfix"></div>
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
$('#categoryId').change(function() {
    var categoryId = $('#categoryId').val();
    if(categoryId != ''){
		$.ajax({
	         url:"<?php echo SITE_ADM_MOD; ?>stores-nct/ajax.stores-nct.php",
	         type:"post",
	         data: {action:'changeSubcate','category':categoryId},
	         success: function(dataSubcate){
	          $('.subcate').html(dataSubcate);
	         }
	    });   
	}     
});
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#categoryId').multiselect();
        $('#subcategoryId').multiselect();
    });
</script>
