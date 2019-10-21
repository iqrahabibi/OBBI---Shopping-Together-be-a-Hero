<?php

$respon = array();
$respon['code'] = 503;
$respon['message'] = 'Under maintenance';

$data['meta'] = $respon;
echo json_encode($data);