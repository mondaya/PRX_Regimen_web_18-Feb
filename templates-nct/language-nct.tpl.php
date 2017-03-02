<li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">%CURRENT_LANGUAGE%<span class="caret"></span></a>
  <ul class="dropdown-menu" role="menu" id="language_dropdown">
    %LANGUAGE_ITEM%
  </ul>
</li>
<script type="text/javascript">
$(document).ready(function(){
	$("#language_dropdown a").click(function(){
		var id=$(this).attr("data-id");
		$.ajax({
			type: "POST",
			url: '%AJAX_URL%',
			data: "id=" + id + "",
			dataType: "json",
			success: function(result){
				if(result.status=='200'){
					window.location.reload();
				}
			}
		});
	});
});
</script> 
