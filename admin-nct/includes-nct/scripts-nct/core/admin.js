function displayMessagePart(isAjax) {
	if(isAjax == true) {
		urlPath = siteName+'admin-nct/includes-nct/displayMessage.php';
		 $.ajax({
				type:"GET",
				url: urlPath,
				success:function(response){
					if(response!="")
					{
						$('#msgPart').html(response).show(1);
					};
				}
			});
	}
	
	$('#closeMsgPart').click(function(){
		  $('#msgPart').fadeOut(1000, "linear");		
	})
	
	setTimeout(function() {
		  $('#msgPart').fadeOut(2500, "linear");
	}, 9000);	
	
}
/* email validation function */
function is_validate(email) {
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	if(reg.test(email) == false) {
		return 'false';
	}
}
function ajaxListingFunction(divID, module, action, id, value) {
	var myConfirm;
	var urlPath;
	if(action == 'delete') {
		myConfirm = confirm('Are you sure to delete?');
	}
	else 
		myConfirm = true;

	if(myConfirm == true) {
       $('#'+divID+'').html('<div style="margin:80px; text-align:center;"><div style="padding:18px;"><img src="'+siteName+'themes/images/loadingWait.gif" alt="" border="0" /><\/div><\/div>');
		
		urlPath = siteName+'admin/modules/'+module+'/ajax.'+module+'.php?action='+action+'&id='+id+'&value='+value;
        $.ajax({
            type:"GET",
            url: urlPath,
		    success:function(response){

                if(response!="") {
					displayMessagePart(true);
                    $('#'+divID+'').html(response);
					/*var DTable = $('#example').dataTable();					
					DTable.fnDraw();*/
                };
            }
        });
	}
}

 function noResultsFound(sel){
	 var row = $(sel).datagrid('getRows',true);
	 if(row.length == 0){
		//alert(first_column);
		totalCols = $(sel).datagrid('getColumnFields');
		cnt = 0;first_column = '';
		for(i=0;i<totalCols.length;i++){
			a = $(sel).datagrid('getColumnOption', totalCols[i]).hidden;
			if(!a){
				cnt+=1;
				if(cnt == 1)
					first_column = totalCols[i]
			}
		}
		var test2 = {};
		test2[first_column] = 'No result found'; 
		$(sel).datagrid('insertRow',{row: test2});
		$(sel).datagrid('mergeCells',{
			index:0,
			field: first_column,
			colspan:cnt
		});
		$('#datagrid-row-r1-2-0 td div').css({'text-align':'center'});

	 }
 }
function setTitle( aoData,a ) {
	aoTitles = []; // this array will hold title-based sort info
	oSettings = a.fnSettings();  // the oSettings will give us access to the aoColumns info
	i = 0;
	for (ao in aoData) {
		name = aoData[ao].name;
		value = aoData[ao].value;

		if (name.substr(0,"iSortCol_".length) == "iSortCol_") {
			// get the column number from "ao"
			iCol = parseInt(name.replace("iSortCol_", ""));
			sName = "";
			if (oSettings.aoColumns[value]) sName = oSettings.aoColumns[value].sName;
			// create an entry in aoTitles (which will later be appended to aoData) for this column
			aoTitles.push( { name: "iSortTitle_"+iCol, value: sName});
			i++;
		}
		 
	}
 
	// for each entry in aoTitles, push it onto aoData
	 for (ao in aoTitles)   aoData.push( aoTitles[ao] );
}

/*$(document).on('switch-change','.make-switch', function(event, state) {
	$(this).prop('checked', state.value);
	var val = state.value ? 'y' : 'n';
	var action = $(this).data('action');
	var switch_action = $(this).data('switch_action');
	switch_action=(switch_action!=null)? switch_action : 'updateStatus';
	
	$.getJSON(action,{action:switch_action,value:val},function(r){		
		toastr[r['type']](r[0]);			
	});
});*/

$(document).on('switch-change','.make-switch', function(event, state) {
	$(this).prop('checked', state.value);
	var val = state.value ? 'y' : 'n';
	var action = $(this).data('action');
	//alert(action);
	var switch_action = $(this).data('switch_action');
	switch_action=(switch_action!=null)? switch_action : 'updateStatus';
	var data_array;
	if(switch_action=='updateStatus'){
		data_array={action:switch_action,value:val}
	}else{
		data_array={value:val}
	}
	/*if(action =='updateverifiedtatus')
	{
		$.getJSON(action,{action:switch_action,value:val},function(r){		
		toastr[r['type']](r[0]);			
	});
	}*/
	$.getJSON(action,data_array,function(r){		
		toastr[r['type']](r[0]);			
	});
});

$(document).on('click','.btn-toggler',function(){
	$('.portlet-toggler').toggle();
});
$(document).on('click','.btnEdit',function(e){
	e.preventDefault();
	var $this = $(this);
	var editLink = $this.attr('href');
	addOverlay();
	$.get(editLink,function(r){
		$(".pageform").html(r);
		$(".numeric").numeric();
		removeOverlay();		
		if($('#draw_month').length) {
			$('#draw_month').datepicker({ minViewMode: 1,format: 'mm/yyyy'});			
		}
		$('.portlet-toggler').toggle();
	});
});
$(document).on('click','.btn-add',function(e){
	e.preventDefault();
	var $this = $(this);
	var editLink = $this.attr('href');
	addOverlay();
	$.get(editLink,function(r){
		$(".pageform").html(r);
		$(".numeric").numeric();
		if($('#draw_month').length) {
			$('#draw_month').datepicker({ minViewMode: 1,format: 'mm/yyyy'});			
		}
		removeOverlay();
		$('.portlet-toggler').toggle();
	});
});
$(document).on('click','.btnAppr',function(e){
	var ans=confirm("Are you sure, Do you want to approve winner?");
	if(ans==false)
		return false;
	e.preventDefault();
	var $this = $(this);
	var editLink = $this.attr('href');
	addOverlay();
	$.get(editLink,function(r){
		$(".pageform").html(r);
		var p = JSON.parse(r);
		toastr[p['type']](p[0]);
		removeOverlay();
		location.reload();
	});
});