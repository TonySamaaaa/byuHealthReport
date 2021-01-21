<?php
require_once('class/byuHealthReport.class.php');

if(@_POST['cookie'] == null) exit;
$by = new byuHealthReport(@$_POST['cookie']);

switch(@$_GET['action']) {
    case 'save':
        $res = $by->save($by->getData());
        break;
    case 'status':
        $res = json_encode($by->getStatus(), JSON_UNESCAPED_UNICODE);
        break;
    case 'getOpenWjdcSet':
        $res = json_encode($by->getOpenWjdcSet(), JSON_UNESCAPED_UNICODE);
        break;
    case 'getWj':
        $res = json_encode($by->getWj(), JSON_UNESCAPED_UNICODE);
        break;
    case 'getWjdc':
        $res = json_encode($by->getWjdc(), JSON_UNESCAPED_UNICODE);
        break;
}

print($res);
?>
