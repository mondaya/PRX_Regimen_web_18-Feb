<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
  <div class="form-body">
    <div class="form-group">
      <label for="newsletter_name" class="control-label col-md-3"><font color="#FF0000">*</font>Newsletter Name: &nbsp;</label>
      <div class="col-md-4">
        <input type="text" class="form-control logintextbox-bg required" name="name" id="name" value="%NEWSLETTER_NAME%">
      </div>
    </div>
    <div class="form-group">
      <label for="newsletter_subject" class="control-label col-md-3"><font color="#FF0000">*</font>Newsletter Subject: &nbsp;</label>
      <div class="col-md-4">
        <input type="text" class="form-control logintextbox-bg required" name="subject" id="subject" value="%NEWSLETTER_SUBJECT%">
      </div>
    </div>
    <div class="padtop10 flclear"></div>
    <div class="form-group">
      <label class="control-label col-md-3"><font color="#FF0000">*</font>Newsletter Content: &nbsp;</label>
      <div class="col-md-9">
        <textarea class="ckeditor form-control textarea-bg required" name="description" id="description" data-error-container="#editor_error" style="display: none;">%NEWSLETTER_CONTENT%</textarea>
        <div id="editor_error"></div>
      </div>
    </div>
    <script type="text/javascript">$(function(){loadCKE("description");});</script>
    <div class="form-group">
      <label class="control-label col-md-3">Status: &nbsp;</label>
      <div class="col-md-4">
        <div class="radio-list" data-error-container="#form_2_Status: _error">
          <label class="">
            <input class="radioBtn-bg required" id="y" name="is_active" type="radio" value="y" %STATIC_A%>
            Active</label>
          <span for="status" class="help-block"></span>
          <label class="">
            <input class="radioBtn-bg required" id="n" name="is_active" type="radio" value="n" %STATIC_D%>
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
