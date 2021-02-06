<?php
require __DIR__ . '/vendor/autoload.php';

use Curl\Curl;

class byuHealthReport {
	private $curl;
	
	private $OpenWjdcSet;		//问卷信息
	private $Wj;				//问卷id信息
	private $Wjdc;				//问卷保存内容
	
	private $set_id;			//问卷id
	
	function __construct($AuthToken, $filter) {
		$this->curl = new Curl();
		
		$this->curl->setHeader('User-Agent', 'Mozilla/5.0 (Linux; Android 9; SM-G9650 Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/66.0.3359.126 MQQBrowser/6.2 TBS/044903 Mobile Safari/537.36 MMWEBID/7909 MicroMessenger/7.0.6.1460(0x27000636) Process/tools NetType/WIFI Language/zh_CN');
		
		$this->curl->setCookie('Auth-Token', $AuthToken);
		$this->getOpenWjdcSet($filter);
		$this->getWj();
		$this->getWjdc();
	}
	
	function getOpenWjdcSet($filter) {
		if(empty($this->OpenWjdcSet)) {
			$this->curl->get('https://byu.educationgroup.cn/wx/wxWjdc/getOpenWjdcSet');
			if ($this->curl->error) throw new Exception('getOpenWjdcSet error');
			
			$res = $this->curl->response;
			foreach($res as $obj) {
				if(stristr($obj->name, $filter)) {
					$this->set_id = $obj->id;
					$this->OpenWjdcSet = $res;
				}
			}
		}
		
		return($this->OpenWjdcSet);
	}
	
	function getWj() {
		if(empty($this->Wj)) {
			if(empty($this->set_id)) throw new Exception('set_id is empty');
			
			$this->curl->post('https://byu.educationgroup.cn/wx/wxWjdc/getWj', array(
				'set_id' => $this->set_id
			));
			if ($this->curl->error) throw new Exception('getWj error');
			
			$res = $this->curl->response;
			if(empty($res->data->dto)) throw new Exception('Wj->data->dto is empty');
			
			$this->Wj = $res;
		}
		
		end:
		return($this->Wj);
	}
	
	function getWjdc() {
		if(empty($this->Wjdc)) {
			if(empty($this->set_id)) throw new Exception('set_id is empty');
			
			$this->curl->post('https://byu.educationgroup.cn/wx/wxWjdc/getWjdc', array(
				'set_id' => $this->set_id
			));
			if ($this->curl->error) throw new Exception('getWjdc error');
			
			$res = $this->curl->response;
			if(empty($res->data)) throw new Exception('Wjdc->data is empty');
			
			$this->Wjdc = $res;
		}
		
		return($this->Wjdc);
	}
	
	function getData() {
		$data = array('items' => '');
		
		foreach($this->Wjdc->data->nr as $n) {
			$name = $n->step_id;
			
			$data[$name] = ($n->uitype == 'radio' ? $n->det_ids : $n->det_names);
			$data['items'] .= ($data['items'] == '' ? '' : ',') . $name;
		}
		
		return($data);
	}
	
	function save($data) {
		$this->curl->post('https://byu.educationgroup.cn/wx/wxWjdc/save', $data + array(
			'set_id' => $this->set_id
		));
		
		return($this->curl->response);
	}
}
?>