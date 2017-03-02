<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
  <div class="form-body">

    <div class="form-group">
      <label for="image" class="control-label col-md-3"><font color="#FF0000">*</font>Slider Image:&nbsp;</label>
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
       <div class="col-md-6">
          <img src="%ADD_MORE%"  class="slider_places_image" alt="" width="50px" height="50px">
      </div>
    </div>
    %CAT_PHOTO%
    <div class="form-group" style="margin-top: 10px;">
      <label class="control-label col-md-3"><font color="#FF0000">*</font>Slider Description: &nbsp;</label>
      <div class="col-md-8">
        <textarea id="slider_description" name="slider_description" class="form-control logintextbox-bg required">%DESCRIPTON%</textarea>
      </div>
    </div>
    <script type="text/javascript">$(function(){loadCKE("slider_description");});</script>
    <div class="form-group">
      <label class="control-label col-md-3">Status: &nbsp;</label>
      <div class="col-md-4">
        <div class="radio-list" data-error-container="#form_2_Status: _error">
          <label class="">
            <input class="radioBtn-bg required" id="y" name="isActive" type="radio" value="y" %STATIC_A%>
            Active</label>
          <span for="status" class="help-block"></span>
          <label class="">
            <input class="radioBtn-bg required" id="n" name="isActive" type="radio" value="n" %STATIC_D%>
            Inactive</label>
          <span for="status" class="help-block"></span> </div>
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
