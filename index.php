<?php
 function getRedCashResponse($token='', $keyword = array()) {
	    $cash = M('redcash_setting')->where(array('token' => $token, 'id' => $keyword['pid'] ))->find();
		if (!$cash) {
			return null;
		}
		$cur_time = time();
		if ($cur_time < strtotime($cash['start_time']) || !$cash['status']) {
	    	list($content, $type) = array('活动尚未开始，敬请期待', 'text');
	       	$this->response($content, $type);
		}
		if ($cur_time > strtotime($cash['end_time']) || $cash['status'] == '2' ||  $cash['status'] == '3'){
			list($content, $type) = array('活动已经结束，谢谢您的关注', 'text');
	       	$this->response($content, $type);
		}
		
		$cash = M('redcash_list')->where(array('token' => $token, 'openid' => $this->fromWxId, 'cashsetting_id' => $keyword['pid'], 'err_code' => 'SUCCESS'))->find();
		if (!$cash) {
			require_once('./RedCashAPI.php');
			$cash = new RedCashAPI(array('token' => $this->token, 'redcash_id' => $keyword['pid'], 'openid' => $this->fromWxId));
	    	$msg = $cash->sendRedCash();
	    	
	    	$strMsg = '';
	    	if ($msg['err_code'] == 'SUCCESS') {
	    		$strMsg = '红包已发送，请领取！';
	    	}else{
	    		$strMsg = '亲， 你来晚了，红包都被抢完了！';
	    	}
	    	list($content, $type) = array($strMsg, 'text');
	       	$this->response($content, $type);
		}else{
			list($content, $type) = array('亲，你已经领取红包了，不能太贪心哦！', 'text');
	       	$this->response($content, $type);
		}
    }
    