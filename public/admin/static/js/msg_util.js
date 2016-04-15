var MsgUtil = {
		
	show:function(notice,callback,time){
		if(callback)
		{
			this.callback = callback;
		}else
		{
			this.callback = null;
		}

		if(typeof time == 'undefined')
		{
			time=3000;
		}
		this.time = time;

		if(this.time==0)
		{
			buttonhtml = '<input type="button" onclick="MsgUtil.cancel();" value="关闭" class="button" >';
		}else
		{
			buttonhtml = '';
		}

		if(!$('#mpopup')[0])
		{
			var stylehtml = '<style>';	
			stylehtml += '#mpopup_wrap{ background:rgba(0, 0, 0, 0.45); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#55000000, endColorstr=#55000000);z-index:99999';
			stylehtml += ' -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#55000000, endColorstr=#55000000)";}';
			stylehtml += '#mpopup h2, #mpopup h2 .close, .mpop_box .button, .mpop_box .button_fd, .loginlist li span, .pop_login li.cwts em{ } ';
			stylehtml += '#mpopup { background:rgba(0, 0, 0, 0.20); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#44000000, endColorstr=#44000000);';
			stylehtml += ' -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#44000000, endColorstr=#44000000)"; clear:both; left:50%; margin-left:0px;    padding:4px; position:absolute; top:300px; width:auto; z-index:900002;}';
			stylehtml += '#mpopup h2{background-color:#ff4201; background-position:left top; background-repeat:repeat-x; height:33px; line-height:33px;    padding:0; padding-left:12px; color:#fff; font-size:12px; font-weight:normal; overflow:hidden; margin:0px;}';
			stylehtml += '#mpopup h2 .close { background-position:-90px -35px; cursor:pointer; float:right; width:12px; height:12px; margin:10px 10px 0 0; _display:inline; z-index:900002; overflow:hidden;}';
			stylehtml += '#mpopup .mpop_line{ background:url(http://passport.guixue.com/static/img/layer_btbg.jpg) no-repeat bottom center; clear:both; height:12px; margin-bottom:30px;}';
			stylehtml += '#mpopup .mpop_box{ width:560px; background:#fff; padding:30px 0; border-top:0;text-align:center; }';
			stylehtml += '#mpopup .mpop_box a.green{ text-decoration:underline; font-size:12px; color:#557917;}';
			stylehtml += '#mpopup .mpop_box a.red{ text-decoration:underline; font-size:14px; color:#FF4800;}';
			stylehtml += '#mpopup .mpop_box a:hover{ text-decoration:underline; color:#557917;}';
			stylehtml += '#mpopup .mpop_box .button{ background-position:0 -35px; width:85px; height:32px; cursor:pointer; border:0; font-size:14px; color:#fff; font-weight:bold; line-height:32px;}';
			 
			stylehtml += '.mPopupBox{position:absolute;left:450px;top:70px;z-index:900002;border:5px solid #D5DCD0;height:39px;_display:inline;_clear:both;}';
			stylehtml += '.mPopupBox .mPopupBox_c{background-color:#fff;border:1px solid #B7C2B1;color:#666;line-height:37px;height:37px;padding-right:40px;_display:inline;}';
			stylehtml += '.mPopupBox .mPopupBox_c .success{float:left;width:24px;height:20px;background-position:0 -1558px;margin:8px 10px 0 15px;display:inline;}';
			stylehtml += '.mPopupBox .mPopupBox_c .failure{float:left;width:24px;height:24px;background-position:0 -1582px;margin:6px 10px 0 15px;display:inline;}';
			stylehtml += '.mPopupBox .mPopupBox_c .loading{display:inline;float:left;margin:10px 10px 0 20px;}';
			stylehtml += '</style>';
			
			var bghtml= '<div style="position: absolute; left: 0px; top: 0px; width: 0px; height: 0px; display: block;z-index:99999" id="mpopup_wrap"></div>';
			var msghtml = stylehtml+bghtml;

			msghtml +='<div id="mpopup">';
			msghtml +='<h2><a href="javascript:void(0);" onclick="MsgUtil.cancel();return false;" class="close">x</a>提示</h2>';
			msghtml +='<div class="mpop_box"  id="mpop_box">';

			msghtml += '<div  id="mpop_box_notice" style="text-align:left;padding-left:40px;padding-right:40px">'+notice+'</div>';
			msghtml += buttonhtml;
			msghtml +='</div>';

			
	
			$('body').append(msghtml);
			$(document).keydown(function(event){
				if(event.keyCode == 27){
					
					MsgUtil.cancel();
				} 
			});
		}else
		{
			$('#mpop_box_notice').html(notice);
		}
		
		var top= $(document).scrollTop()+($(window).height()-$('#mpopup').height())/2;
		var left = $(document).scrollLeft()+($(window).width()-$('#mpopup').width())/2;
		
		$('#mpopup_wrap').show().css({width:$(document).width(),height:$(document).height()});
		$('#mpopup').show().css({top:top,left:left});
		

		if(this.time>0)
		{
			MsgUtil.handle = setTimeout(function(){
			
			MsgUtil.cancel()
			},this.time);
		}

	
	},


	cancel:function(){
		$('#mpopup_wrap').hide();
		$('#mpopup').hide();
		clearTimeout(MsgUtil.handle);
		if(this.callback)
		{
			this.callback();
		}
		//window.clearInterval(MsgUtil.handle);
		return false;
	},

	codehtml:{
		'audit':'<div class="PopupBox_c"><span class="success"></span>',
		'succ':'<div class="PopupBox_c"><span class="success"></span>',
		'fail':'<div class="PopupBox_c"><span class="failure"></span>',
		'same':'<div class="PopupBox_c"><span class="success"></span>',
		'wait':'<div class="PopupBox_c"><img src="/static/css/mili/images/loading.gif" class="loading" />'
	},

	tip:function(code, msg, obj )
	{
		if(typeof code == 'undefined')return;
		if(typeof msg == 'undefined')
		{
			msg='';
		}
		if(code=='wait')
		{
			if(msg=='')msg='加载中...';
			
		}
		$('#PopupBox').html(this.codehtml[code]+msg+'</div>');

		if(obj)
		{
			var pos = $(obj).offset();
		
			$('#PopupBox').show().css({'left':(pos.left-20),'top':(pos.top-5-$('#PopupBox').outerHeight())});
			
		}else
		{
			var top = $(document).scrollTop()+parseInt(($(window).height()-$('#PopupBox').height())/2);
			var left = $(document).scrollLeft()+parseInt(($(window).width()-$('#PopupBox').width())/2);
			$('#PopupBox').show().css({'left':left,'top':top});
		}
		if(code=='succ'|| code=='fail' || code=='same' || code == 'audit')
		{

			if(typeof MsgHandle != 'undefined')
			{
				clearTimeout(MsgHandle);
			}
			MsgHandle = setTimeout(function(){$('#PopupBox').fadeOut(500);},1000);
		}

	}

};


/*
window.alert=function(notice)
{
	MsgUtil.show(notice);
}
*/