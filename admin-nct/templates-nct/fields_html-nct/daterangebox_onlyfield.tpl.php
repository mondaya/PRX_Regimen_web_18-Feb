<input type="text" class="form-control required %CLASS%" name="%NAME1%" id="%ID1%" value="%VALUE1%" %EXTRA1%>
<span class="input-group-addon">to</span>
<input type="text" class="form-control required %CLASS%" name="%NAME2%" id="%ID2%" value="%VALUE2%" %EXTRA2%>
<script type="text/javascript" >
$(document).ready(function(){
 if (jQuery().datepicker) {
		$(".date-picker").datepicker({
			autoclose: true,
			format: "mm-dd-yyyy",
			startDate: "06/08/2015",
		});
	}
});		
</script>
