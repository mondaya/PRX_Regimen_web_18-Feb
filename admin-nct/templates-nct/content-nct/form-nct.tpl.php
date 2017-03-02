<form action="" method="post" name="frmCont" id="frmCont" class="form-horizontal" enctype="multipart/form-data" novalidate="novalidate">
  <div class="form-body">
    <div class="form-group">
      <label for="page_name" class="control-label col-md-3"><font color="#FF0000">*</font>Page Name: &nbsp;</label>
      <div class="col-md-4">
        <input type="text" class="form-control logintextbox-bg required" name="pageName" id="pageName" value="%PAGE_NAME%">
      </div>
    </div>
    <div class="form-group">
      <label for="page_title" class="control-label col-md-3"><font color="#FF0000">*</font>Page Title: &nbsp;</label>
      <div class="col-md-4">
        <input type="text" class="form-control logintextbox-bg required" name="pageTitle" id="pageTitle" value="%PAGE_TITLE%">
      </div>
    </div>
    <div class="form-group">
      <label for="meta_keyword" class="control-label col-md-3"><font color="#FF0000">*</font>Meta Keyword: &nbsp;</label>
      <div class="col-md-4">
        <textarea class="form-control textarea-bg required" name="metaKeyword" id="metaKeyword">%META_KEYWORD%</textarea>
      </div>
    </div>
    <div class="form-group">
      <label for="meta_desc" class="control-label col-md-3"><font color="#FF0000">*</font>Meta Description: &nbsp;</label>
      <div class="col-md-4">
        <textarea class="form-control textarea-bg required" name="metaDesc" id="metaDesc">%META_DESCRIPTION%</textarea>
      </div>
    </div>
    <div class="padtop10 flclear"></div>
    <div class="form-group">
      <label class="control-label col-md-3"><font color="#FF0000">*</font>Page Description: &nbsp;</label>
      <div class="col-md-9">
        <textarea class="ckeditor form-control textarea-bg required" name="pageDesc" id="pageDesc" data-error-container="#editor_error" style="display: none;">%PAGE_DESCRIPTION%</textarea>
        <div id="editor_error"></div>
      </div>
    </div>
    <script type="text/javascript">$(function(){loadCKE("pageDesc");});</script>

    <div class="form-group">
      <label class="control-label col-md-3">Section: &nbsp;</label>
      <div class="col-md-4">
        <div class="radio-list" data-error-container="#form_2_Status: _error">
          <label class="">
            <input class="radioBtn-bg required" id="y" name="section" type="radio" value="s" %STATIC_S%>
            SHOPPING</label>
          <span for="status" class="help-block"></span>
          <label class="">
            <input class="radioBtn-bg required" id="n" name="section" type="radio" value="h" %STATIC_H%>
            HELP & SUPPORT</label>
          <span for="status" class="help-block"></span>
          <label class="">
            <input class="radioBtn-bg required" id="n" name="section" type="radio" value="w" %STATIC_W%>
            EWALLET</label>
          <span for="status" class="help-block"></span> </div>
        <div id="form_2_Status: _error"></div>
      </div>
    </div>

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
