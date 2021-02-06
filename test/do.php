<?php
require '../byuLogin.class.php';
require '../byuHealthReport.class.php';

if(!empty($_REQUEST['loginCode'])) {
	$l = new byuLogin();
	$l->setloginCode($_REQUEST['loginCode']);
	$AuthToken = $l->login(@$_REQUEST['username'], @$_REQUEST['password'], @$_REQUEST['code']);
	
	$hr = new byuHealthReport($AuthToken, '疫情信息上报');
} else if(!empty($_REQUEST['AuthToken'])) {
	$hr = new byuHealthReport($_REQUEST['AuthToken'], '疫情信息上报');
}

switch(@$_REQUEST['func']) {
	case 'getCookie':
		sres(array('Auth-Token' => $AuthToken));
		break;
	
	case 'getOpenWjdcSet':
		sres($hr->getOpenWjdcSet('疫情信息上报'));
		break;
	
	case 'getWj':
		sres($hr->getWj());
		break;
	
	case 'getWjdc':
		sres($hr->getWjdc());
		break;
	
	case 'save':
		$data = $hr->getData();
		
		sres($hr->save($data));
		break;
	
	default:
	case 'test':
		$Wj = $hr->getWj();
		$Wjdc = $hr->getWjdc();
		
		//创建转换键值对
		$i2n = array();
		foreach($Wj->data->ptrees as $p) {
			$i2n[$p->id] = $p->tm;
			if($p->uitype == 'radio') {
				$p_id = $p->id;
				foreach($Wj->data->childrens->$p_id as $c) {
					$i2n[$c->id] = $c->name;
				}
			}
		}
		
		$data = array('items' => '');
		$text = array();
		
		foreach($Wjdc->data->nr as $n) {
			$key = $n->step_id;
			if($n->uitype == 'radio') {
				$val = $n->det_ids;
			} else {
				$val = $n->det_names;
			}
			
			$data[$key] = $val;
			$data['items'] .= ($data['items'] == '' ? '' : ',') . $key;
			
			$text[sval($i2n, $key)] = sval($i2n, $val);
		}
		
		$res = array(
			'info' => array(
				'name' => $Wj->data->dto->name,
				'date' => array(
					'start' => $Wj->data->dto->start_date,
					'end' => $Wj->data->dto->end_date
				)
			),
			'user' => array(
				'xm' => $Wj->data->user->xm,
				'username' => $Wj->data->user->username
			),
			'data' => $data,
			'text' => $text
		);
		
		if(!empty($Wj->data->tbInfo)) {
			$res['info']['date']['scrq'] = $Wj->data->tbInfo->scrq;
		}
		
		sres($res, true);
		break;
	
}

function sres($arr, $debug = false) {
	if($debug) {
		printf('<pre>');
		printf(htmlspecialchars(print_r($arr, true)));
		printf('</pre>');
	} else {
		printf(json_encode($arr));
	}
}

function sval($arr, $key) {
	if(array_key_exists($key, $arr)) {
		return($arr[$key]);
	}
	return($key);
}
?>