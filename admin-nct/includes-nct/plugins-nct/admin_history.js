var req, req_val;
var $scrollbar = [];
var after_login_url;
var last_login_url_flag = false;

if (typeof(loggedin) == "undefined")
{
        var loggedin;
}
if (typeof(first_time_load) == "undefined")
{
        var first_time_load = true;
}

var window_focus = false;


if ( window.history && 'pushState' in history) {
	
    // encapsulate with an IIFE
    (function () {
		
        // because JSHint told me to
        'use strict';

        jQuery(document).on('click', "a[rel='tab']", function (evt) {
			
			// prevent normal navigation
            evt.preventDefault();
			//remove modal overlay when navigating from one pg to another
			if(jQuery("body").hasClass("modal_overlay")){			
				jQuery("body").removeClass("modal_overlay");				
				jQuery(".closepopup").trigger("click");
				jQuery("#close-icon").trigger("click"); //for mainuser popup tabs	
			}
			m(jQuery(this));
        });//onclick rel tab end		
			
		jQuery(document).on('click', "li[rel='tab']", function (evt) {
				// prevent normal navigation
				evt.preventDefault();				
				m(jQuery(this).find("a"));
        });//onclick rel tab end
		
		
		$(window)
        .on('focus', function(ev){
				window_focus = true;
				//gft();		
        })	
        .on('load', function(ev){			
				//gft();		
        });
		
        // handle back buttons of browser
        window.onpopstate = function(evt) {			
				if(jQuery("body").hasClass("modal_overlay")){
						jQuery("body").removeClass("modal_overlay");
						jQuery(".closepopup").trigger("click");
						jQuery("#close-icon").trigger("click"); //for mainuser popup tabs	
						jQuery("#closeprofilepicpopup").click(); //for mainuser pic popup	
						jQuery("#closeexamplepicpopup").click(); //for examplepic popup 
						
				}
				
				if(jQuery("#close_search").size() > 0)
						jQuery("#close_search").trigger("click");            
	
				if (evt.state) {               
						m(evt.state.url,true,true);              
				}
            
        };

        // create state on page init and replace the current history with it
        var state = createState('','Title','');
        history.replaceState(state, document.title, document.location.href);
		
		/*
		*	to flash the title of browser when notification recieved
		*
		*/				
		window.flashTitle = function (newMsg, howManyTimes) {
				function step() {
						document.title = (document.title == original) ? newMsg : original;
						if (--howManyTimes > 0) {
							timeout = setTimeout(step, 1000);					
						}
				}		
				howManyTimes = parseInt(howManyTimes);		
				if (isNaN(howManyTimes)) {
					howManyTimes = 15;
				}		
				cancelFlashTitle(timeout);
				step();
		};
		
		/*
		 *	to stop flashing title of browser when notification recieved
		 *
		 */	
		window.cancelFlashTitle = function () {
			clearTimeout(timeout);
			document.title = original;
		};
		
    }());

}else{
	m("browser-not-supported",true);
	//alert("your browser doesn't support Previous & Next function of browser history!!");
}

/*
 *	to display html, title of the page we called
 *  here we replace html into the div
 */	

function displayContent(state, container) {

            // change the page title
            document.title = state.title;
			// replace the current content
			
			if(typeof(container)!="undefined" && container!=null)
			{
				
				jQuery(container).html('');
				
				jQuery(container).hide().html(state.content).fadeIn(1500,function(){
				
					if(typeof(setHeightWidth) === 'function')
					{
						setHeightWidth();						
					}
						
				});				
			}
			else
			{
				
				jQuery('.page-content-wrapper').html('');				
            	jQuery('.page-content-wrapper').hide().html(state.content).fadeIn(1500,function(){
					
					if(typeof(setHeightWidth) === 'function')
					{
						setHeightWidth();						
					}
					
				});
			}
			
			//add_track_player(); 
			
}

// create a state object from html
function createState($content,title,container,url) {
			var state = {
				content : $content,
				container : container,
				url : url,
				title : title
			};
            return state;

        }
	//redirect function	
function m($ele,flag,replacestate){
	
			if(typeof(main_q)!="undefined" && typeof(saved_q)!="undefined")
			{
				if(main_q.length > Object.keys(saved_q).length)
				{							
					if(confirm('A track is being uploaded. If you leave or refresh the site, your upload will be lost. Continue?'))
					{
						Cancel_Upload("all_with_complete");						
					}
					else
					{	
						return false;						
					}
				}
			}
				
			if(typeof(flag)!="undefined" && flag==true)
			{
				
				var clickedHref =$ele;
				var container=".page-content-wrapper";
				var loading_text="Loading...";
				var extra='';
			}
			else
			{
				var clickedHref =$ele.attr("href");
				var container=$ele.attr('data-container');
				var loading_text=$ele.attr('data-loading_text');
				var extra=$ele.attr('data-extra');
			}		
			var lg=(jQuery("body").hasClass("lg")==true)?"y":"n";
			counter = 1; 
            // request new page through ajax
			try {
				
				//alert(this.href)
				if(typeof(req)!='undefined')
				{
					//console.log( "aborted from history.js" );
					req.abort();
				}

				req = $.ajax({
					url:clickedHref,
					cache: false,
					beforeSend:function(){

									jQuery(".loading_show").removeClass("loading_show");
		
									jQuery(".loading_container .loading_text").html('Loading Page...');
									if(typeof(loading_text)!="undefined" && loading_text!=null && loading_text!='')
									{
										jQuery(".loading_container .loading_text").html(loading_text);
									}
									jQuery(".loading_container").addClass("loading_show");												
					},
					method:'post',
					dataType:'json',
					async: true,
					data:{"rel":"true","extra":extra,"lg":lg},
					success:function(data){		
						//alert(JSON.stringify(data));
						jQuery(".loading_container").removeClass("loading_show");
						if(data.code==200)
						{
							// create state object							
							var state = createState(data.content,data.title,container,clickedHref);							
							$.when(displayContent(state,container)).done(function( x ) {});
							
							
							if(typeof(replacestate)=="undefined" || replacestate==false){
								history.pushState(state,state.title,clickedHref);
							}
							else
							{
								 history.replaceState(state,state.title,clickedHref);
							}
								
								//playset page set
								/*if(jQuery("#playset_page").length > 0)
								{
									/*if(mplayer.sound().playState==1)
									{
										var track = mplayer.track();
										var url="/"+track.user.permalink+"/"+track.permalink;
										
										var playset_temp_id = $(".tracks_parent[data-track='" +url+"']").attr("data-id");
										$(".controls").attr("data-id",playset_temp_id);
									//}
								}*/
								//
							if(data.msg != "undefined" && data.msg.trim()!=''){	
								displayNotification("success",data.msg);                                     
							}	
									
						}
						else if(data.code == 300)
						{
							after_login_url = data.url;
							$.when(gft()).then(go_home());
							if(data.msg != "undefined"){	
								showMessage("info",data.msg);                                       
							}
						}
						else
						{
							console.log("data.msg is coming");
							//alert(data.msg);							
						}
					}
				});			
			
			} catch(e) {
				console.log("Error in redirection - " + e);
			}
}

function enable_footer_controls(){
	
	var prev = jQuery("#footer_prev");
	var playpause = jQuery("#footer_play");
	var next = jQuery("#footer_next");
	
	if(jQuery(".track_list_item").size() > 0 || jQuery(".play_playlist_extra").size() > 0)
	{
			next.removeAttr("disabled");
			//playpause.removeAttr("disabled");
			prev.removeAttr("disabled");
			
			prev.children().removeClass("opa3");
			//playpause.css("opacity","1");
			next.children().removeClass("opa3");
	}
	else{
								
			next.attr("disabled","disabled");
			//playpause.attr("disabled","disabled");
			prev.attr("disabled","disabled");
			
			prev.children().addClass("opa3");
			//playpause.css("opacity","0.3");
			next.children().addClass("opa3");
	}
	
	
	if(jQuery("#carousel-example-captions").size() > 0)
	{
		//home page
		//mplayer.add_tracks();
		
	}
	else{
		 prev.children().addClass("prev");
		 next.children().addClass("next");
		 
	}
	
	jQuery(playpause).on("click",function(e){
				e.preventDefault();
				if(jQuery("#playset_page").size() > 0)
				{
					var temp_click = jQuery("#playset_page .playset-group-panel").first().find(".play_playlist_extra");
					temp_click.trigger("click");
				}
	});
	
}

function l()
{
	m(jQuery("a.ftr-logo[rel='tab']"));
	//jQuery("a.ftr-logo[rel='tab']").click();
}


function go_discover()
{
	jQuery("a#discover_link").attr("data-loading_text","Logging you in...");
	m(jQuery("a#discover_link[rel=\'tab\']"));

}
function refresh_comment_track()
{	
	if(typeof(scwaveform)!="undefined"){		
		scwaveform.refresh_comments();
	}
}
function go_current_page()
{
	var current_url = window.location.pathname;	
 	//jQuery("a#discover_link").attr("data-loading_text","Logging you in...");
	m(jQuery("a#"+current_url+"[rel=\'tab\']"));

}


function go_home()
{	
	if(typeof(jQuery("a.ftr-logo[rel='tab']"))!="undefined")
	{
		m(jQuery("a.ftr-logo[rel='tab']"));
	}
	else
	{
		m(jQuery("a#site_logo[rel='tab']"));
	}	
}


function succ_signup()
{	
						jQuery("#signup_notify_resend_activation").attr("data-mail", jQuery("#register").find("#txt_email").val());
						
	 					jQuery("body").addClass("modal_overlay");
		                 if(jQuery("#test_modal").css("opacity")!=0)
		                 {
		                    testAnim("fadeOutUpBig",jQuery("#test_modal"),"closemodal","openmodal");		                    
		                 }
		                 if(jQuery("#forgot_modal").css("opacity")!=0)
		                 {
		                     testAnim("fadeOutUpBig",jQuery("#forgot_modal"),"closemodal","openmodal");		                    
		                 }
		                 if(jQuery("#register_modal").css("opacity")!=0)
		                 {
		                    testAnim("fadeOutUpBig",jQuery("#register_modal"),"closemodal","openmodal");		                    
		                 }
		                
		                jQuery("label.error").remove();		                
		                testAnim("fadeInDownBig",jQuery("#notify_modal"),"openmodal","closemodal");
						
						jQuery("body").on(event_click,"#popup_close",function(){
							
				            jQuery("body").removeClass("modal_overlay");
				            var $elem=jQuery("#notify_modal");
				            testAnim("fadeOutUpBig",$elem,"closemodal","openmodal");
				            //jQuery("#notify_modal").remove();
				        });
				        jQuery("body").on(event_click,"#btn_notify_okay",function(){
							
				            jQuery("body").removeClass("modal_overlay");
				            var $elem=jQuery("#notify_modal");
				            testAnim("fadeOutUpBig",$elem,"closemodal","openmodal");
				            //jQuery("#notify_modal").remove();
				        });				
}//gdt end

	/*		
function gft(login_click,message_sent,mfeed_rec)//get footer tool
{
	$.ajax({
			url:site_url+"admin-nct/index.php",
			method:"post",
			data:{"rel":"true",extra:"f"},
			dataType:"json",			
			async: true,
			success: function(data)
			{
				if(data.code==200)
				{	
					//console.log(data);				
				
					var new_footer = data.content;
					var lc=false;
					loggedin=data.msg;					
					
					if(last_login!=loggedin){						
						//console.log("Inside last_login != loggedin");
						lc=true; //login changed
						last_login=loggedin;
						jQuery(".login-main-panel").remove();
						jQuery(".footer-section ul li.inner_ft").remove();
						jQuery(".footer-section ul li.ftr-link").remove();							
						jQuery(".footer-section ul.inner_ft_main #demo").remove();				
						jQuery(".footer-section ul.inner_ft_main script").remove();
						jQuery(".footer-section ul.inner_ft_main").hide().prepend(new_footer).show(function(){
							jQuery(".footer-section ul.inner_ft_main #demo").appendTo($("footer"));
							jQuery(".footer-section ul.inner_ft_main script").appendTo($("footer"));
							jQuery("#test_modal").appendTo($("footer"));
							jQuery("#forgot_modal").appendTo($("footer"));
							jQuery("#register_modal").appendTo($("footer"));
							jQuery("#notify_modal").appendTo($("footer"));
							jQuery("#share_musicpopup").appendTo("footer");
							jQuery("body").removeClass("modal_overlay");
							if(loggedin=='nl'){
								jQuery(".messagePopup").remove();								
							}
							else if(loggedin=='l'){								
								jQuery(".messagePopup").appendTo($("footer"));
							}							
						});
					}
					//alert(lc);
					//console.log( jQuery(".footer-section ul li.inner_ft").hasClass("nl") );	
					
					
					
					if(!jQuery(".footer-section ul li.inner_ft").hasClass("nl"))
					{	
						if(typeof(login_click)!='undefined' && login_click==true){
							 if(jQuery(location).attr("href") == site_url || jQuery(location).attr("href") == site_url+"home"|| jQuery(location).attr("href") == site_url+"Login"){
									if(mfeed_rec > 0)
									{
										m(site_url+"music-feed",true);
									}else{
										jQuery("a#discover_link").attr("data-loading_text","Loading Discover...");
										go_discover(); 
									}
									 
							 }else{							
									setTimeout(function(){
											var clickto = jQuery("do").attr("clickthis");										
											jQuery(clickto).click();
									},2500);
									m(jQuery(location).attr("href"),true);
							 }							 
								
						}
						$(".ftr-logo").attr("href",site_url+"discover");
						
					
					}
					else
					{	
							//console.log(" currently user is not logged in. ");								
							if(lc==true){						
								if(typeof(main_q)!="undefined" && typeof(saved_q)!="undefined")
								{
										Cancel_Upload("all_with_complete");
								}
								after_login_url=window.location.href;
								$(".ftr-logo").attr("href",site_url);
								l();								
								last_login=loggedin;		
							}						
					}					
					
					if(typeof(message_sent)!="undefined" && message_sent==true)
					{
						jQuery(".login-main-panel").remove();
						jQuery(".footer-section ul li.inner_ft").remove();
						jQuery(".footer-section ul li.ftr-link").remove();	
						
						jQuery(".footer-section ul.inner_ft_main #demo").remove();				
						jQuery(".footer-section ul.inner_ft_main script").remove();
						jQuery(".messagePopup").remove();
								
						jQuery(".footer-section ul.inner_ft_main").hide().prepend(new_footer).show(function(){
							jQuery(".footer-section ul.inner_ft_main #demo").appendTo($("footer"));
							jQuery(".footer-section ul.inner_ft_main script").appendTo($("footer"));
							jQuery("#test_modal").appendTo($("footer"));
							jQuery("#forgot_modal").appendTo($("footer"));
							jQuery("#register_modal").appendTo($("footer"));
							jQuery("#notify_modal").appendTo($("footer"));
							jQuery("#share_musicpopup").appendTo("footer");
							jQuery(".messagePopup").appendTo($("footer"));
							
							jQuery("body").removeClass("modal_overlay");
							
						});
					}
					
				}
				else
				{
					console.log("data.msg is: "+data.msg);
				}
			}
	});
}//gft end*/
var myMessages = ["info"]; // define the messages types		 
function hideAllMessages()
{
	/* var messagesHeights = new Array(); // this array will store height for each
	 if(myMessages.length>0){ 

			 for (i=0; i<myMessages.length; i++)
			 { 
			 		messagesHeights[i] = jQuery("." + myMessages[i]).outerHeight()+10;				
					// jQuery(".message_notification." + myMessages[i]).html("");	
					jQuery(".message_notification." + myMessages[i]).css("top", -messagesHeights[i]); //move element outside viewport	  				
			 }
	}*/
}
function showMessage(type,msg)
{
	$.when(
		hideAllMessages()
	).done(
		function(){
			if(msg != null){
				//jQuery(".message_notification").show();
				jQuery("body").append('<div class="info message_notification" style="top: -150px;"></div>');
				var interval_val = 4000;
				jQuery(".message_notification."+type).html(msg);			  
				jQuery(".message_notification."+type).animate({top:"0"}, 500);
				
				if(msg.indexOf("rel")>=0){
					interval_val = 9000;
				}
				if(msg.indexOf("Play track")>=0 || msg.indexOf("account is now active")>=0 || msg.indexOf("Login")>=0){
					//dont let msg go.
				}else{
					messageinterval=setTimeout(function(){
						jQuery(".message_notification.info").trigger("click");
						//jQuery(".message_notification").hide();
						jQuery(".message_notification").remove();
					},interval_val);	 
					
				}
			}
		//hideAllMessages(); 
		}
	);
}

function displayNotification(type,msg)
{
	toastr[type](msg);
}

function submitFormHandler(url, form_id, loading_text,start_function_name,end_function_name)
{
	//alert(jQuery("form#"+form_id).serialize());
	
	
	try {
		//alert("b");
				
				$.ajax({
					url:url ,
					
					beforeSend:function(){
						jQuery(".loading_show").removeClass("loading_show");
						
						jQuery(".loading_container .loading_text").html('Processing...');
						
									if(typeof(loading_text)!="undefined" && loading_text!=null && loading_text!='')
									{
										jQuery(".loading_container .loading_text").html(loading_text);
									}
									jQuery(".loading_container").addClass("loading_show");
									
									/*jQuery("#loginform input").attr("disabled", true);
									jQuery("#register input").attr("disabled", true);
									jQuery("#basicinfoform input").attr("disabled", true);*/
									
									jQuery("form#"+form_id).find("input").attr("disabled", true);																			
						},
					method:'post',
					dataType:'json',
					
					data:jQuery("form#"+form_id).serialize(),
					success:function(data){
					
						jQuery(".loading_container").removeClass("loading_show");
						/*$("#loginform input").attr("disabled", false);
						jQuery("#loginform").trigger("reset");
						
						$("#register input").attr("disabled", false);
						jQuery("#register").trigger("reset");
						
						jQuery("#basicinfoform input").attr("disabled", false);*/
						
						jQuery("form#"+form_id).find("input").attr("disabled", false);
						jQuery("form#"+form_id).trigger("reset");
						
						
						if(data.code==200)
						{	
							if(typeof(eval(start_function_name)) === 'function')
							{
								window[(start_function_name)](data);
							}
							if(data.msg!="undefined"){	
								showMessage("info",data.msg);                                       
							}
							if(typeof(eval(end_function_name)) ==='function')
							{
								if(form_id == "reset_pass_form"){
									setTimeout(function(){
										window[(end_function_name)](data);
									},5000);
								}else{
									window[(end_function_name)](data);	
								}
								
							}
						}
						else if(data.code==300)
						{
							after_login_url=data.url;
							$.when(gft()).then(go_home());
							
							if(data.msg!="undefined"){	
								showMessage("info",data.msg);                                       
							}
							
						}
						else
						{
							if(data.msg!="undefined"){	
								showMessage("info",data.msg);                                       
							}
						}
						
						
					}
				});
				
	
	} catch(e) {
		
				console.log("Error in redirection - " + e);
	}
}

function submitValueHandler(url, data_string, loading_text,start_function_name,end_function_name,async)
{
	//alert(data_string);
	if(typeof(async)==="undefined"){
		async=true;
	}
	try {
		//alert(this.href)
			
			if(typeof(req)!='undefined')
			{
				req.abort();
			}
			
	   		req_val = $.ajax({
					
					url:url ,
					beforeSend:function(){
						if(typeof(loading_text)=="undefined" || loading_text!=false){
							
						jQuery(".loading_show").removeClass("loading_show");
						
							jQuery(".loading_container .loading_text").html('Processing...');
							
										if(typeof(loading_text)!="undefined" && loading_text!=null && loading_text!='')
										{
											jQuery(".loading_container .loading_text").html(loading_text);
										}
										jQuery(".loading_container").addClass("loading_show");	
						}
										
						},
					method:'post',
					dataType:'json',
					data:data_string,
					async:async,
					success:function(data){
					
						jQuery(".loading_container").removeClass("loading_show");
					
						if(data.code==200)
						{	
							if(typeof(eval(start_function_name)) === 'function')
							{
								
								window[(start_function_name)](data);
							}
							if(data.msg!="undefined"){	
								showMessage("info",data.msg);                                       
							}
							if(typeof(eval(end_function_name)) ==='function')
							{
								window[(end_function_name)](data);
							}
						}
						else if(data.code==300)
						{
							after_login_url=data.url;
							$.when(gft()).then(go_home());
							if(data.msg!="undefined"){	
								showMessage("info",data.msg);                                       
							}
							
						}
						else
						{
							if(data.msg!="undefined"){	
								showMessage("info",data.msg);                                       
							}
						}
						
						
					}
				});
			return req;
		
	} catch(e) {
				console.log("Error in redirection - " + e);
	}
}

function submitFormHandlerWithUpload(url, form_id , loading_text,start_function_name,end_function_name)
{
	 var formElement = document.getElementById(form_id);
		var formObj = jQuery("#"+form_id);
		var formURL = formObj.attr("action");
		//var formData = new FormData();
		var formData = new FormData(formElement);
		//var formData = formObj.serialize();
		//formData.append(formObj.serialize());
		jQuery.ajax({
		url: url,
		type: 'post',
		dataType:'json',
		data:  formData,
		processData: false,  // tell jQuery not to process the data
	 	contentType: false ,  // tell jQuery not to set contentType
		enctype: 'multipart/form-data',
		mimeType    : 'multipart/form-data',
		cache: false,
		beforeSend:function(){
						jQuery(".loading_show").removeClass("loading_show");
						
						jQuery(".loading_container .loading_text").html('Processing...');
						
									if(typeof(loading_text)!="undefined" && loading_text!=null && loading_text!='')
									{
										jQuery(".loading_container .loading_text").html(loading_text);
									}
									jQuery(".loading_container").addClass("loading_show");	
										
						},
		success: function(data, textStatus, jqXHR)
		{
			jQuery(".loading_container").removeClass("loading_show");
		
			if(data.code==200)
			{
				
				if(typeof(eval(start_function_name)) === 'function')
				{
					window[(start_function_name)]();
				}
				if(data.msg!="undefined"){	
				showMessage("info",data.msg);                                       	
				}
				if(typeof(eval(end_function_name)) ==='function')
				{
					window[(end_function_name)]();
				}	
			}
			else if(data.code==300)
						{
							after_login_url=data.url;
							$.when(gft()).then(go_home());
							if(data.msg!="undefined"){	
								showMessage("info",data.msg);                                       
							}
							
			}
			else
			{
				if(data.msg!="undefined"){	
				showMessage("info",data.msg);                                       	
				}
			}
			 
		},
		 error: function(jqXHR, textStatus, errorThrown) 
		 {
			 //alert("error");
		 }          
		});
		return false;
		//Prevent Default action. 
		
	/*
	jQuery("#"+form_id).submit(); //Submit the form	*/
	
}

function apply_scrollbar(elem)
{
	var document_height=jQuery(window).height();
	var footer_height=jQuery("footer").outerHeight();
	var footer_height1=jQuery("footer").outerHeight(true);		
	
	var original_elm='';
	elem=elem+",";
	var elements=elem.split(",");
	elements.forEach(function(elm) {
		
		var $elem=jQuery(elm);
	
		if(elm!=''){
			
			var flag=$elem.attr("data-position");
			var scroll_flag=$elem.attr("data-scroll");
			//alert($elem.attr("data-scroll"))
			if(typeof($elem)!='undefined'){
				if(typeof(scroll_flag)=="undefined" || scroll_flag!="false")
				{
					
					original_elm+=elm+",";
					
					if(typeof(flag)!="undefined" && flag=="nt")
					{
						var h1=jQuery("#logo_cover").outerHeight(true);
						final_height1=document_height-footer_height1-h1;
					}
					else if(typeof(scroll_flag)!="undefined" && scroll_flag=="ot") //only top
					{
						if(typeof($elem.offset())!='undefined'){
							
							var scrollTop     = jQuery(window).scrollTop(),
								elementOffset = $elem.offset().top,
								h1      = (elementOffset - scrollTop);
							//var h1=$elem.position().top;
							
							final_height1=document_height-h1;
							$elem.height(final_height1);
						}
						
					}
					else if(typeof(scroll_flag)!="undefined" && scroll_flag=="ot-pt") //only top -with parent
					{
						
							var doc_height=$elem.parent().height();
							var h1=$elem.position().top;
							
							final_height1=doc_height-h1;
							$elem.height(final_height1);
					}
					else
					{
						if(typeof($elem.offset())!='undefined'){
							
							var scrollTop     = jQuery(window).scrollTop(),
								elementOffset = $elem.offset().top,
								h1      = (elementOffset - scrollTop);
							//var h1=$elem.position().top;
							
							
							final_height1=document_height-footer_height1-h1;	
							$elem.height(final_height1);
						}
						
					}
				
				}
				else
				{
					if(typeof($elem)!='undefined'){
						var final_h=document_height-footer_height;
						$elem.height(final_h);
					}
					
				}
			}
		
		}
	});
	
		
	original_elm=original_elm.substring(0, original_elm.length - 1);
	
	var all_elem=elem.substring(0, elem.length - 1);
	$scrollbar[all_elem]=jQuery(original_elm).mCustomScrollbar({
					scrollButtons:{
						enable:false
					},
					autoDraggerLength:false,
					theme:"light"					,
					mouseWheel:true,
					autoHideScrollbar:true,
					contentTouchScroll:true,
					advanced:{
						updateOnBrowserResize: true,
						updateOnContentResize:true,
						autoScrollOnFocus:true
					}
					
				});
			jQuery(original_elm).on({
				mouseenter: function () {
					jQuery(document).data({"keyboard-input":"enabled"});
						jQuery(this).addClass("keyboard-input");
				},
				mouseleave: function () {
					jQuery(document).data({"keyboard-input":"disabled"});
						jQuery(this).removeClass("keyboard-input");
				}
			});
	
		
}
function add_scrollbar($elem){
	
	
	if(typeof($scrollbar[$elem])=="undefined"){
				
            	apply_scrollbar($elem);
			}
			else
			{
				$scrollbar[$elem].mCustomScrollbar("destroy");
				//$scrollbar.mCustomScrollbar("update");
				apply_scrollbar($elem);
				
				$scrollbar[$elem].mCustomScrollbar("update");
				
			}
}
jQuery(document).keydown(function(e){
					if(jQuery(this).data("keyboard-input")==="enabled" && jQuery(".keyboard-input .mCSB_container") != "undefined"){
						var activeElem=jQuery(".keyboard-input");
							
						if(e.which===38){ //scroll up
							e.preventDefault();
							var activeElemPos=Math.abs(jQuery(".keyboard-input .mCSB_container").position().top);
							var pixelsToScroll=60;
							if(pixelsToScroll>activeElemPos){
								activeElem.mCustomScrollbar("scrollTo","top");
							}else{
								activeElem.mCustomScrollbar("scrollTo",(activeElemPos-pixelsToScroll),{scrollInertia:400,scrollEasing:"easeOutCirc"});
							}
						}else if(e.which===40){ //scroll down
							e.preventDefault();
							var activeElemPos=Math.abs(jQuery(".keyboard-input .mCSB_container").position().top);
							var pixelsToScroll=60;
							activeElem.mCustomScrollbar("scrollTo",(activeElemPos+pixelsToScroll),{scrollInertia:400,scrollEasing:"easeOutCirc"});
						}
					}
				});

function check_file(value,allowedExtensions)
{
				
				file = value.toLowerCase();
				extension = file.substring(file.lastIndexOf(".") + 1);
				allowedExtensions=allowedExtensions+",";			
				allowedExtensions_ar=allowedExtensions.split(",");
				allowedExtensions_ar.filter(function(e){return e;});				
                if ($.inArray(extension, allowedExtensions_ar) == -1) {
                    return false;
                } else {
                    return true;

                }

}
function imagePreview(input,container,extensions,h)
{
					
							if (input.files && input.files[0])
							{
								 t = input.files[0].name;
								
								
								if(check_file(t,extensions)==true)
								{
									var reader = new FileReader();
									reader.onload = function (e) {
									  //jQuery("#"+container).attr("src", e.target.result).height("87px");									  										
									  if(typeof(h)!="undefined")
									  {
										  jQuery("#"+container).attr("src", e.target.result).height(h+"px");
									  }
									  
									  else
									  {
									  	jQuery("#"+container).attr("src", e.target.result);									  
									  }
									  if(input.id=="userImg")
									  {
											jQuery("img[alt=msg-img]").attr("src", e.target.result);
											jQuery("#userImageUploaded").attr("check", "yes");
									  }
									  if(input.id=="bkImg")
									  {
											jQuery("#bkImageUploaded").attr("check", "yes");
									  }
									  
									};
									reader.readAsDataURL(input.files[0]);
								}
								else
								{
									showMessage("info","Please upload a valid JPG or PNG image format.");
									return false;
								}
							}
					}	
						

function load_r()
{
				
				var doc_height = jQuery(window).height()-jQuery(".scroll_inner_container").offset().top-$("footer").outerHeight(true);
				
				var doc_inner_height = jQuery(".scroll_inner_container").height();
				
				if(doc_inner_height < doc_height)
				{
										
					if(jQuery(".search_load_more").attr("data-page")>0){
						load_more_results(".search_load_more");
					}
					
				}	
}
		

function load_more_results_update(data)
{
	var cont=data.container;
	var ref=data.ref;
	
	if(data.last_page==0)
	{		
		if($(ref).length>0){
			jQuery(".cont_spinner").remove();
			$(cont).append(data.data);		
		}
		jQuery(ref).remove();		
	}
	else
	{
		jQuery(".cont_spinner").remove();
		$(cont).append(data.data);
		$(ref).attr("data-page",data.last_page);
	}
	load_r();
	append_track_player();
	$("img.lazy").lazyload({
					effect : "fadeIn",
					load : function()
					{
						$(this).removeClass("lazy"); // Callback here
					}
				});
		if(typeof(setHeightWidth)==="function"){
			setHeightWidth();
		}
}
		
function load_more_results(elem)
{
	
	var $elem=$(elem);
	var container=$elem.attr("data-cont");
	var $container=$(container);
	
	if(jQuery('.cont_spinner').length > 0) {
	}else{
			jQuery(".scroll_inner_container").append("<div class='clearfix'></div><div class='cont_spinner'><p>Loading...</p></div>");			
			jQuery(".cont_spinner").css("display","block");		
	}	
	
	var url = $elem.attr("data-url");
	var page=$elem.attr("data-page");
	
	var discover = $elem.attr("data-disc");	
	
	if(typeof(page)=="undefined")
	{
		page=1;
	}
	
	if(typeof(req_val)!='undefined')
	{
		console.log( "aborted from history.js" );
		console.log(req_val);
		req_val.abort();
		
	}
		
	var datastring="currentpage="+page+"&cont="+container+"&ref="+elem;
	
	if(discover == "discover")
	{
		var $loading_text = "Loading More Tracks";
	}else if(discover == "notification" || discover == "mymusic"){
		var $loading_text = "Loading more";
	}	
	else{
		var $loading_text = "Loading more";
	}
	
	submitValueHandler(url,datastring,$loading_text,"load_more_results_update",'');	
	 
}

function refresh_track_comments(data)
{
	$(".track-comment-box").children("ul").html(data.data);
	$(".comment_count[data-id='" + data.t+"']").html(data.datacount);
}
function refresh_track_plays(data)
{
	$(".play_count[data-id='" + data.t+"']").html(data.datacount);
	$(".total_plays").html(data.datatodaycount);
	$(".today_plays").html(data.datatodaycount);
	
	//to update plays in group stats
	var updated_play_today = parseInt($(".play_number:eq(0)").html())+1;	
	$(".play_number:eq(0)").html(updated_play_today);
	var updated_play_total = parseInt($(".total_number").html())+1; 
	$(".total_number").html(updated_play_total);
	
}
function refresh_playset_plays(data)
{
	$(".playset_count[data-id='" + data.t+"']").html(data.datacount);	
}

//setInterval("getList()", 10000); // Get users-online every 10 seconds 
function update_users(list)
{

	if(typeof(list)=="undefined" || list==null || list.length==0)
	{
		$(".user_status").removeClass("online_before").removeClass("online_after");
	}
	else if(list.length>0)
	{
		
		$(".user_status").each(function(){
			var $this=$(this);
			var attr=$this.attr("data-position");
			var id=$this.attr("data-id");
			if($.inArray(id,list)>=0)
			{
					if(attr=="before")
					{
						$this.addClass("online_before");
					}
					else if(attr=="after")
					{
						$this.addClass("online_after");
					}
			}
			else
			{
				$this.removeClass("online_before").removeClass("online_after");
			}
			
		});
		
	}
}
function removeAnimation($elm){
		setTimeout(function() {
			$elm.removeClass('animating_noti');
		}, 1000);			
}
function is_loggedin(){				
	var temp = jQuery(".ftr-btn").hasClass("nl");		
	
	if(temp == true)
	{
			jQuery("#btn_login").trigger( "click" );						
	}else{
			return true;	
	}
}

function stopsongs(){	
	mplayer.pause();
}
function stopsongsonclose(){	
	mplayer.pause();
	$("iframe").remove();  
}

function getList() 
{ 	
	if(last_login=="l"){
	$.getJSON(site_url+"onlineusers", function(data) { 
		if(data.u.result_users=="success")
		{
			if(data.u.data_users.length>0){
				var current_online_ids=[];
				var len=data.u.data_users.length;
				
				var j=0;
				$.each(data.u.data_users,function(i,id){
					
					current_online_ids.push(id);
					j++;
					if(j==len)
					{
						update_users(current_online_ids);
					}
				});
			}

		}
		else{
			update_users();
		}
		
		if(typeof(data.m)!=="undefined"){
			if(data.m.msg_result=="success")
			{
				var l=data.m.data_msg_ids.length;
				if(l>0){
					var j=0;
					$.each(data.m.data_msg_ids,function(i,k){
						j++;
						$(".footerMessageScroll ul li[data-pid='"+k+"']").remove();
					
						if(j==l){
							$(".empty_msg").remove();
							$(".footerMessageScroll ul").prepend(data.m.data_msg);
						}
					});
				}
				
			}
			var old_c=$("#msg_un").html();

			if(!isNaN(old_c) && old_c!=data.m.lm_un)
			{
				if(data.m.lm_un > 0)
				{
					flashTitle(data.m.lm_un+" New message received",15);
				}
				
				$("#msg_un").html(data.m.lm_un).addClass('animating_noti');
				if(old_c<data.m.lm_un){
					$('#chatAudio')[0].play();
				}
				removeAnimation($("#msg_un"));				
			}
			if(data.m.lm_un>0)
			{
				$("#msg_un").show().addClass('animating_noti');
				removeAnimation($("#msg_un"));
			}
			else
			{
				$("#msg_un").hide();
			}
			
		}
		else
		{
			$("#msg_un").hide();
		}
		
		//notification
		if(typeof(data.n)!=="undefined"){
			if(data.n.msg_n_result == "success")
			{
				var ln = data.n.data_not_ids.length;
				if(ln>0){
					var j=0;
					if(jQuery("#no_notify_msg").length > 0)
					{
						jQuery("#no_notify_msg").remove();	
					}					
					$.each(data.n.data_not_ids,function(i,k){
						j++;
						
						if(j > 0 && j == 1){							
							$(".empty_msg").remove();							
							$("#footer_notification_ul").prepend(data.n.data_not);
						}else{
							
						}
					});
				}
				else{
				}
				
			}
			var old_n_c=$("#not_un").html();

                                if (!isNaN(old_n_c) && old_n_c != data.n.ln_un)
                                {
                                        if (data.n.ln_un > 0)
                                        {
                                                flashTitle(data.n.ln_un + " New notification received", 15);
                                        }
                                        
										$("#not_un").html(data.n.ln_un).addClass('animating_noti');
										
                                        if (old_c < data.n.ln_un)
                                        {
                                                $('#chatAudio')[0].play();
                                        }
                                        removeAnimation($("#not_un"));
                                }

                                if (data.n.ln_un > 0)
                                {
                                        $("#not_un").show().addClass('animating_noti');
                                        removeAnimation($("#not_un"));
                                }
                                else
                                {
                                        $("#not_un").hide();
                                }

                        }
                        else
                        {
                                $("#not_un").hide();

                        }

                });
        }
}

function scroll_to_btm() {
	
	setTimeout(function(){
		window.scrollTo(0,document.body.scrollHeight);
	}, 2000);	
	
}

$(document).on('switch-change','.make-switch', function(event, state) {
	$(this).prop('checked', state.value);
	var val = state.value ? '1' : '0';
	var action = $(this).data('action');
	
	var getaction = $(this).data('getaction');
	
	if(typeof getaction !== 'undefined')
		getAction = getaction;
	else
		getAction = "updateStatus"
	$.getJSON(action,{action:getAction,value:val},function(r){
		toastr[r['type']](r[0]);			
	});
});