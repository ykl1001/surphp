
$(document).ready(function(){
	$('.sugg').click(function(){
		var url = this.getAttribute('href');
		var show_dialog = art.dialog.load(url,{title:'意见和建议',id:'suggestion',button:[{
			name:'提交',
			callback: function(){
				if(document.getElementById('suggestion_content').value == ''){
					document.getElementById('suggestion_content').focus();
					art.dialog.tips('内容不能为空', 1);
					return false;
				}
				$.ajax({
					   type: "POST",
					   url: url,
					   data:  $("#suggestionform").serialize(),
					   dataType: 'json',
					   success: function(data){
					     if(data.status == 1){
					    	 art.dialog.tips('提交成功', 1);
					     }else{
					    	 art.dialog.tips(data.info, 1);
					     }
					   }
					});
				return false;
			},focus: true
		},{
			name:'取消',
			callback: function(){
				show_dialog.close();
			}
		}]},false);
		return false;
	});
	
	$('.addcategory').click(function(){
		var addurl = this.getAttribute('href');
		var is_saved = false;
		var category_dialog = art.dialog({title:'添加分类',id:'addcategory',button:[{
			name:'添加',
			callback: function(){
				if(document.getElementById('add_cat').value == ''){
					document.getElementById('add_cat').focus();
					art.dialog.tips('请输入分类名', 1);
					return false;
				}
				$.ajax({
					   type: "POST",
					   url: addurl,
					   data:  'cname='+$('#add_cat').val(),
					   dataType: 'json',
					   success: function(data){
					     if(data.status == 1){
					    	 art.dialog.tips('添加成功', 1);
					    	 is_saved = true;
					     }else{
					    	 art.dialog.tips(data.info, 1);
					     }
					   }
					});
				return false;
			},focus: true
		},{
			name:'关闭',
			callback: function(){
				category_dialog.close();
				if(is_saved){
					window.location.href = window.location.href;
				}				
			}
		}],content:'分类名：<input type="text" id="add_cat" name="cat" value="">'});
		return false;
	});
	
});