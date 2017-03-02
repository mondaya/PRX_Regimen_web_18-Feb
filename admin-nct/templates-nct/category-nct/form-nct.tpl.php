<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
	 <div class="form-body">
	 	<div class="form-group"> 
	 		<label for="countryName" class="control-label col-md-3">%MEND_SIGN%Category : &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="text" class="form-control logintextbox-bg required" name="categoryName" id="categoryName" value="%CATEGORY_NAME%">
	 		</div>
	 	</div>
	 	<div class="form-group">
     		<label class="control-label col-md-3"><font color="#FF0000">*</font>Category Description: &nbsp;</label>
      		<div class="col-md-4">
        		<textarea class="form-control textarea-bg" name="description" id="description">%DESCRIPTION%</textarea> 
      		</div>
    	</div>
		<!-- <div class="form-group"> 
	 		<label class="control-label col-md-3">Category Image: &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<input type="file" name="cateImage"> 
	 		</div>
	 		<span>Please select image with 370*270</span>
	 	</div> -->
	 	<div class="form-group"> 
	      <label for="image" class="control-label col-md-3">Category Image:&nbsp;</label> 
	       <div class="col-md-6">
	          <div id="dvPreview">
	          <div class="">
	           <input type="hidden" class="places_image" name="placeimage" id="placeimage" >
	                      
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
	 	%CAT_PHOTO%
	 	<div class="form-group"> 
	 		<label class="control-label col-md-3">Display on home: &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<div class="radio-list" data-error-container="#form_2_Status: _error"> 
	 				<label class=""> <input class="radioBtn-bg required" id="y" name="is_display" type="radio" value="y" %HOME_A%> Yes</label>
	 				<span for="is_display" class="help-block"></span> 
	 				<label class=""> <input class="radioBtn-bg required" id="n" name="is_display" type="radio" value="n" %HOME_D%> No</label>
	 				<span for="is_display" class="help-block"></span> 
	 			</div>
	 			<div id="form_2_Status: _error"></div> 
	 		</div>
	 	</div>
	 	<div class="form-group"> 
	 		<label class="control-label col-md-3">Status: &nbsp;</label> 
	 		<div class="col-md-4"> 
	 			<div class="radio-list" data-error-container="#form_2_Status: _error"> 
	 				<label class=""> <input class="radioBtn-bg required" id="y" name="isActive" type="radio" value="y" %STATUS_A%> Active</label>
	 				<span for="isActive" class="help-block"></span> 
	 				<label class=""> <input class="radioBtn-bg required" id="n" name="isActive" type="radio" value="n" %STATUS_D%> Deactive</label>
	 				<span for="isActive" class="help-block"></span> 
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