function postcomment(to_id){
	var id = $('#comment_id').val();
	var content = '';
	if(to_id > 0){
		content = $('#reply_'+to_id).val();
	}else{
		content = editor.html();
	}
	
	$.ajax({
		   type: "POST",
		   url: "/index.php",
		   data: "m=blog&c=comment&a=post&id="+id+"&content="+content+"&to_id="+to_id,
		   dataType:'json',
		   success: function(rs){
		     if(rs.status == 1){
		    	 if(to_id > 0){
					 if($('#poster_'+to_id).find('.poster_data').size() > 0){
						 $('#poster_'+to_id).find('.poster_data').first().before(rs.data.html);
					 }else{
						 $('#poster_'+to_id).append(rs.data.html);
					 }
		    		 
		    		 $('#poster_'+to_id).find('.reply').remove();
		    	 }else{
		    		 $('#comment_list').prepend(rs.data.html);
		    	 }
		    	 
		    	 art.dialog({
		    		    time:1,
		    		    content: '发布成功！'
		    		});
		     }else{
		    	 art.dialog({
		    		    time:1,
		    		    content: '发布失败'
		    		});
		     }
		   }
		});
}

function delComment(id){
	$.ajax({
		   type: "POST",
		   url: "/index.php",
		   data: "m=blog&c=comment&a=delete&id="+id,
		   dataType:'json',
		   success: function(rs){
		     if(rs.status == 1){
		    	 $('#poster_'+id).remove();		    	 
		    	 art.dialog({
		    		    time:1,
		    		    content: '删除成功！'
		    		});
		     }else{
		    	 art.dialog({
		    		    time:1,
		    		    content: '删除失败'
		    		});
		     }
		   }
		});
}

function reply(id){
	var html = '<div class="reply"><textarea id="reply_'+id+'" style="width:100%;height:70px;"></textarea><div class="action"><input type="button" class="submit" value="提交" onclick="postcomment('+id+')"/></div></div>';
	$('#poster_'+id).find('.other').first().append(html);
}