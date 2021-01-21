<?php
require_once('curl.class.php');

class byuHealthReport {
    private $curl;
    
    private $OpenWjdcSet;		//问卷信息
    private $Wj;				//问卷id信息
    private $Wjdc;				//问卷保存内容
    
    private $set_id;			//问卷id
    private $a_i2n = array();
    private $a_n2i = array();
    
    function __construct($cookie) {
        $this->curl = new Curl();
        $this->curl->addHeader('Cookie: ' . $cookie);
        $this->curl->addHeader('User-Agent: Mozilla/5.0 (Linux; Android 9; SM-G9650 Build/PPR1.180610.011; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/66.0.3359.126 MQQBrowser/6.2 TBS/044903 Mobile Safari/537.36 MMWEBID/7909 MicroMessenger/7.0.6.1460(0x27000636) Process/tools NetType/WIFI Language/zh_CN');
        
        $this->OpenWjdcSet = $this->getOpenWjdcSet();
        $this->Wj = $this->getWj();
        $this->Wjdc = $this->getWjdc();
    }
    
    function save($data) {
        return($this->curl->post('https://byu.educationgroup.cn/wx/wxWjdc/save', $data));
    }
    
    function id2name($id) {
        return($this->a_i2n[$id]);
    }
    
    function name2id($name) {
        return($this->a_n2i[$name]);
    }
    
    function getStatus() {
        $content = array();
        
        foreach($this->Wjdc->data->nr as $n) {
            $name = $this->id2name($n->step_id) . '[' . $n->step_id . ']';
            if($n->det_ids != null) {
                $key = $this->id2name($n->det_ids) . '[' . $n->det_ids . ']';
            } else {
                $key = $n->det_names;
            }
            $content[$name] = $key;
        }
        
        $status = array(
            'name' => $this->Wj->data->dto->name . '[' . $this->set_id . ']',
            'content' => $content,
            'user' => $this->Wj->data->user->username,
            'scrq' => $this->Wjdc->data->tbInfo->scrq
        );
        
        return($status);
    }
    
    function getData() {
        $data = 'set_id=' . $this->set_id;
        $items = '';
        
        foreach($this->Wjdc->data->nr as $n) {
            $name = $n->step_id;
            if($n->det_ids != null) {
                $content = $n->det_ids;
            } else {
                $content = urlencode($n->det_names);
            }
            $data .= '&' . $name . '=' . $content;
            $items .= ($items == '' ? '' : '%2c') . $name;
        }
        $data .= '&items=' . $items;
        
        return($data);
    }
    
    function getOpenWjdcSet() {
        if($this->OpenWjdcSet == null) {
            $res = json_decode($this->curl->get('https://byu.educationgroup.cn/wx/wxWjdc/getOpenWjdcSet'));
            
            foreach($res as $obj) {
                if(stristr($obj->name, '疫情防控健康上报')) {
                    $this->set_id = $obj->id;
                    return($res);
                }
            }
        }
        
        return($this->OpenWjdcSet);
    }
    
    function getWj() {
        if($this->Wj == null) {
            $res = json_decode($this->curl->post('https://byu.educationgroup.cn/wx/wxWjdc/getWj', 'set_id=' . $this->set_id));
            if($res->data->dto == null) goto end;
            
            //创建转换键值对
            foreach($res->data->ptrees as $p) {
                $this->a_i2n[$p->id] = $p->name;
                $this->a_n2i[$p->name] = $p->id;
                if($p->uitype == 'radio') {
                    $p_id = $p->id;
                    foreach($res->data->childrens->$p_id as $c) {
                        $this->a_i2n[$c->id] = $c->name;
                        $this->a_n2i[$c->name] = $c->id;
                    }
                }
            }
            
            return($res);
        }
        
        end:
        return($this->Wj);
    }
    
    function getWjdc() {
        if($this->Wjdc == null) {
            $res = json_decode($this->curl->post('https://byu.educationgroup.cn/wx/wxWjdc/getWjdc', 'set_id=' . $this->set_id));
            if($res->data == null) goto end;
            
            return($res);
        }
        
        end:
        return($this->Wjdc);
    }
}
?>