!function(o){o.fn.oauthpopup=function(o){this.click(function(){o.windowName=o.windowName||"ConnectWithOAuth",o.windowOptions=o.windowOptions||"location=0,status=0,width="+o.width+",height="+o.height+",scrollbars=1",o.callback=o.callback||function(){window.location.reload()};var i=this;i._oauthWindow=window.open(o.path,o.windowName,o.windowOptions),i._oauthInterval=window.setInterval(function(){i._oauthWindow.closed&&(window.clearInterval(i._oauthInterval),o.callback())},10)})},o.fn.googlelogout=function(i){i.google_logout=i.google_logout||"true",i.iframe=i.iframe||"ggle_logout",this.length&&"true"==i.google_logout&&this.after('<iframe name="'+i.iframe+'" id="'+i.iframe+'" style="display:none"></iframe>'),i.iframe?i.iframe="iframe#"+i.iframe:i.iframe="iframe#ggle_logout",this.click(function(){if("true"==i.google_logout){o(i.iframe).attr("src","https://mail.google.com/mail/u/0/?logout");var t=window.setInterval(function(){o(i.iframe).load(function(){window.clearInterval(t),window.location=i.redirect_url})})}else window.location=i.redirect_url})}}(jQuery);