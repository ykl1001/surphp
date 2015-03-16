<?php
function formatComment($list){
	$html = '';
	if (!empty($list)) {
		foreach ($list as $data){
			$html .='<div class="poster_data" id="poster_'.$data['id'].'"><div class="comm_topbar"><span>'.$data['username'].'</span>于 '.friendlyDate($data['create_at'],'mohu').'</div>';
			$html .='<div class="comm_content">'.$data['content'].'</div>';
			$html .='<div class="other"><div class="ip">ip:'.$data['ip'].'</div><a href="javascript:;" onclick="delComment('.$data['id'].')">删除</a><a href="javascript:;" onclick="reply('.$data['id'].')">回复</a></div>';
			$html .='<div class="clear"></div>';
			if (isset($data['reply_data']) && $data['reply_data']) {
				$html .= formatComment($data['reply_data']);
			}
			$html .='</div>';
		}
		
	}
	
	return $html;
}