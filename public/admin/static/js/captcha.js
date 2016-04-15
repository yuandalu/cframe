var captchaUtil = {
	create:function(id)
	{
		var c_currentTime = new Date();
		var c_miliseconds = c_currentTime.getTime();
		var url = '/include/showHtmlImage?x='+ c_miliseconds;
		$.ajax({
			url: url,
			success: function(data){

				var str= "<div onclick='captchaUtil.create(\""+id+"\");' style='width:80px;height:30px;border:0;padding:0;margin:0;' id='"+id+"child'><style>#"+id+"child div{float:left;height:1px;width:1px;border:0;padding:0;margin:0}</style>";
				var captcodearr=data.split('#');
				for(var i=0;i<captcodearr.length;i++){
					str+="<div style='background:#"+captcodearr[i]+"'/>";
				};
				str+="</div>";
				$('#'+id).html(str);
			},

			dataType: 'text'
		});

	}
}