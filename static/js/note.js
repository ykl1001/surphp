$(document).ready(function(){
	$('#addnote').click(function(){
		art.dialog.load('/index.php?m=note&c=index&a=add',{title:'添加笔记',id:'addnote',button:[{
			name:'提交',
			callback: function(){
				if(document.getElementById('note_content').value == ''){
					document.getElementById('note_content').focus();
					art.dialog.tips('内容不能为空', 1);
					return false;
				}
				document.getElementById('note_form').submit();
			},focus: true
		}],init:function(){
			
		    $('#tag').tagit({
		        availableTags: sampleTags,
		        tagLimit:3,
		        onTagLimitExceeded:function(){
		        	art.dialog.tips('标签数量超过限制', 1);
		        }
		    });
		}},false);
		return false;
	});
	
	$('.toview').click(function(){
		var show_dialog = art.dialog.load(this.getAttribute('href'),{title:'查看笔记',id:'addnote',button:[{
			name:'关闭',
			callback: function(){
				show_dialog.close();
			},focus: true
		}]},false);
		return false;
	});
	
	$('.tomodify').click(function(){
		var modifyurl = this.getAttribute('href');
		var is_saved = false;
		var show_dialog = art.dialog.load(modifyurl,{title:'修改笔记',id:'modifynote',button:[{
			name:'保存修改',
			callback: function(){
				if(document.getElementById('note_content').value == ''){
					document.getElementById('note_content').focus();
					art.dialog.tips('内容不能为空', 1);
					return false;
				}
				$.ajax({
					   type: "POST",
					   url: modifyurl,
					   data:  $("#note_form").serialize(),
					   dataType: 'json',
					   success: function(data){
					     if(data.status == 1){
					    	 art.dialog.tips('保存成功', 1);
					    	 is_saved = true;
					     }else{
					    	 art.dialog.tips('未保存成功', 1);
					     }
					   }
					});
				return false;
			},focus: true
		},{
			name:'关闭',
			callback: function(){
				show_dialog.close();
				if(is_saved){
					window.location.href = window.location.href;
				}				
			}
		}],init:function(){
			
		    $('#tag').tagit({
		        tagLimit:3,
		        onTagLimitExceeded:function(){
		        	art.dialog.tips('标签数量超过限制', 1);
		        }
		    });
		}},false);
		return false;
	});
});

function selectbg(color){
	var node = document.getElementById('note_content');
	if(node != null){
		node.style.backgroundColor = '#'+color;
		document.getElementById('background').value = color;
	}
}