<div class="form-group">
  <label for="%NAME%" class="control-label col-md-3">%LABEL%&nbsp;</label>
  <div class="col-md-4">
    <div class="input-group input-large date-picker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy">
      <input type="text" class="form-control required %CLASS%" name="%NAME1%" id="%ID1%" value="%VALUE1%" %EXTRA1%>
      <span class="input-group-addon">to</span>
      <input type="text" class="form-control required" name="%NAME2%" id="%ID2%" value="%VALUE2%" %EXTRA2%>
    </div>
    <span class="help-block"></span> </div>
</div>
<script type="text/javascript" >
$(document).ready(function(){
 if (jQuery().datepicker) {
		$(".date-picker").datepicker({
			autoclose: true,
			format: "dd-mm-yyyy",
			startDate: "%START_DATE%",
		});
	}
});		
</script>