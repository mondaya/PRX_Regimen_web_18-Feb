function displayMessagePart(isAjax) {
	/*if(isAjax == true) {
		urlPath = siteName+'admin/includes/displayMessage.php';
		 $.ajax({
				type:"GET",
				url: urlPath,
				success:function(response){
					if(response!="")
					{
						//$('#msgPart').html(response).show(3000);
						$('#msgPart').html(response);
					};
				}
			});
	}
	*/
	$('#closeMsgPart').click(function(){
		  $('#msgPart').fadeOut(1000, "linear");		
	});
	
	setTimeout(function() {
		  $('#msgPart').fadeOut(1200, "linear");
	}, 9000);
	
}
function updatePageContent(divName, module, ajaxFileName, varAttached, pageNo) {

		urlPath = SITE_URL+'modules/'+module+'/'+ajaxFileName+varAttached+'&pageNo='+pageNo;

		$('#'+divName+'').html('<div style="margin:80px; text-align:center;"><div style="padding:18px;"><img src="'+SITE_URL+'themes/images/loadingWait.gif" alt="" border="0" /><\/div><\/div>');
	   		
		 $.ajax({
			type:"GET",
			url: urlPath,
			success:function(response){
				if(response!="")
				{
					//alert(divName +','+ module);
					displayMessagePart(true);					
                    $('#'+divName+'').html(response).show(30000);
					
				};
			}
		});
}
function partyInv(uId, divId,pid) {
	var urlPath = SITE_URL + 'modules/parties/ajax.parties.php' ;
		 $.ajax({
			type:"POST",
			url: urlPath,
			data: {'uId': uId, 'action': "inviteHost",  'id':pid },
			success:function(response){
				$('#'+divId).html(response);
			}
		});
	
}

function followUser(userId,divId) {
	var urlPath = SITE_URL + 'modules/follow/ajax.follow.php' ;
	 $.ajax({
		type:"POST",
		url: urlPath,
		data: { 'id': userId, 'action': "userFollow" },
		success:function(response){
			if(response!=""){
				$('#'+divId).html(response);
			};
		}
	});
}
function compare_load(id) {
	$("#comp_loader").show();
    $("#comp_loader").fadeIn(400).html('Loading <img src="'+SITE_URL+'themes/images/ajax-loading.gif"/>');
	var page_id = $("#page_id").val();
	var data_string="action=compare_load&id="+id+"&page_id="+page_id;
	var urlPath = SITE_URL+'modules/product_detail/ajax.product_detail.php';
	$.ajax({
		type:"POST",
		data:data_string,
		url: urlPath,
		success:function(response){
		  if(response!="")
		  {	
		    $("#comp_loader").hide();
			var next_val = parseInt(page_id) + 1;
			$('#prod_compare').append(response);
			$("#page_id").val(next_val);
		  }
		  else {			
			$("#load_btn").hide();
		  }
		}
	});	
}
function related_load(id) {
	$("#rel_loader").show();
    $("#rel_loader").fadeIn(400).html('Loading <img src="'+SITE_URL+'themes/images/ajax-loading.gif"/>');
	var page_id = $("#rel_page_id").val();
	var data_string="action=related_load&id="+id+"&page_id="+page_id;
	//alert(data_string);
	var urlPath = SITE_URL+'modules/product_detail/ajax.product_detail.php';
	$.ajax({
		type:"POST",
		data:data_string,
		url: urlPath,
		success:function(response){
		  if(response!="")
		  {
			$("#rel_loader").hide();   
			var next_val = parseInt(page_id) + 1;
			$('#prod_related').append(response);
			$("#rel_page_id").val(next_val);
		  }
		  else {
			$("#rel_load_btn").hide();
		  }
		}
	});	
}
$(function(){
	$(document).on('click', ".share", function(){
		var mainParentDiv = $(this).parents('.box').children('.col_wrap');
		var img = mainParentDiv.children('a').children('img').attr('src');
		var val = mainParentDiv.find('h3 a').html();
		var urlToshare = mainParentDiv.find('h3 a').attr('href');
		var detail = '<ul>'+mainParentDiv.find('ul').html()+'</ul>';
		// defnedd in FB_APP_ID, SITE_NM, REDIRECT_URI default theme
		$.magnificPopup.open({
			preloader:true,
			modal:false,
			items: {
				src: '<div class="white_popup share_popup_new"><div class="share_popup_heading">Share this listing</div><div class="share_popup_details"><div class="lft_share"><img src="'+img+'" width="60" /></div><div class="lft_share_txt"><h3>'+val+'</h3>'+detail+'</div><div class="clr"></div></div><div class="share_link_icon"><ul><li><a href="javascript:void(0)" onclick="window.open(\'https://www.facebook.com/dialog/feed?app_id='+FB_APP_ID+'&display=popup&caption='+SITE_NM+'&link='+encodeURI(urlToshare)+'&redirect_uri='+REDIRECT_URI+'&picture=&name='+encodeURI(val)+'\');"><div class="fb_my"></div>facebook</a></li><li><a href="https://twitter.com/share?url='+encodeURI(urlToshare)+'&text='+encodeURI(val)+'" class="twitter-share-button" ><div class="tweet_my"></div>Tweet</a></li></ul><div class="clr"></div></div></div>',
				type: 'inline'
			},
			alignTop:false,
			closeBtnInside: true
		});
	});
});
function tellfriend(draw_id)
{
	var csv_email=$("#csv_email").val();	
	if(csv_email == '')
		return false;
	var data_string="action=tell_frnd&email_csv="+csv_email+"&draw_id="+draw_id;
	var urlPath = SITE_URL+'modules/home/ajax.home.php';
	$.ajax({
			type:"POST",
			data:data_string,
			url: urlPath,
			success:function(response){
				if(response!="")
				{					
					$('#msgPart').html(response).show(1);
				};
			}
		});
	setTimeout(function() {
		  $('#msgPart').fadeOut(2500, "linear");
	}, 9000);
}
function drawsubscribe(draw_id)
{
	var email=$("#drawEmail").val();
	if(email == '')
		return false;
	var data_string="action=drawing&email="+email+"&draw_id="+draw_id;
	var urlPath = SITE_URL+'modules/home/ajax.home.php';
	$.ajax({
			type:"POST",
			data:data_string,
			url: urlPath,
			success:function(response){
				if(response!="")
				{					
					$('#msg_draw').html(response).show(1);
					//$('#msgPart').html(dataRes).show();
				};
			}
		});
	setTimeout(function() {
		  $('#msg_draw').fadeOut(2500, "linear");
	}, 9000);
}
function submit_form()
{		
	var cat_id = urlTitle($("#cat_id").val());
	var subcat_id = urlTitle($("#subcat_id").val());
	var cat_name = urlTitle($("#cat_id option:selected").text());
	var subcat_name = urlTitle($("#subcat_id option:selected").text());
	var q = urlTitle($("#q").val());
	var price_range = urlTitle($("#price_range").val());
	var url=SITE_URL+"find_products/"+cat_id+"/"+cat_name+"/"+subcat_id+"/"+subcat_name+"/"+q+"/"+price_range;
	window.location.replace(url);
}
function search_products()
{
	var q = encodeURIComponent($("#q").val());
	var url = SITE_URL+"find_products/q/"+q;
	window.location.replace(url);
}
function urlTitle(text) {
	if(text!='' & text!='undefined')
	{
		var characters = [' ', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '=', '_', '{', '}', '[', ']', '|', '/', '<', '>', ',', '.', '?', '--'];
		for (var i = 0; i < characters.length; i++) {
			 var char = String(characters[i]);
			 text = text.replace(new RegExp("\\" + char, "g"), '-');
		}
	    text = text.toLowerCase();
	    return text;
	}
    return "-";
}
function show_subcat()
{
	var cat_id=$("#cat_id").val();
	var str="&action=subcat&cat_id="+cat_id;
	urlPath = SITE_URL+'modules/search_products/ajax.search_products.php';
	$.ajax({
		type:"POST",
		url: urlPath,
		data:str,
		success:function(response){
			if(response!="") {
				$("#subcat_dd").html(response);
			}
			else {
				$("#subcat_dd").html('');
				alert("No sub-category found");
			}
		}
	});
}
function add_review(product_id)
{
	if($("#review_title").validationEngine('validate') == true)
	   return false;
	if($("#review_text").validationEngine('validate') == true)
	   return false;
	   var dname=$("#dname").val();
	var review_text=$("#review_text").val();
	var review_title=$("#review_title").val();
	var rating=$("#rating").val();	
	var str="&action=add_review&product_id="+product_id+"&review_text="+review_text+"&dname="+dname+"&review_title="+review_title+"&rating="+rating;
	var urlPath = SITE_URL+'modules/product_detail/ajax.product_detail.php';
	$.ajax({
			type:"POST",
			url: urlPath,
			data:str,
			success:function(response){
				try {
					var json_data = JSON.parse(response);
					$("#suc_msg").html(json_data['msg']);
				} catch(e) {
					
					$(".review_box").html(response);
					$('#myModal').modal('hide');
				}
			}
	});
	return false;
}
function set_rating(id) {
  var diff=5-id;
  for(var i=1;i<=id;i++) {	      
     $("#"+i).attr('src', SITE_URL+'themes/images/star-full.png');
  }
  for(var j=(id+1);j<=5;j++) {
     $("#"+j).attr('src', SITE_URL+'themes/images/star-blank.png');
  }
  $("#rating").val(id);
}