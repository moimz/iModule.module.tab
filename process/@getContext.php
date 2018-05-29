<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodule.kr)
 *
 * 탭 컨텍스트 정보를 가져온다.
 * 
 * @file /modules/tab/process/@getContext.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 21.
 */
if (defined('__IM__') == false) exit;

$parent = Request('parent');
$tab = Request('tab');

$data = $this->db()->select($this->table->context)->where('parent',$parent)->where('tab',$tab)->getOne();

if ($data != null) {
	$context = json_decode($data->context);
	
	if ($data->type == 'MODULE') {
		$data->target = $context->module;
		$data->_context = $context->context;
		$data->_configs = isset($context->configs) == true ? $context->configs : new stdClass();
	} elseif ($data->type == 'EXTERNAL') {
		$data->external = $context->external;
	} elseif ($data->type == 'PAGE') {
		$data->subpage = $context->page;
	} elseif ($data->type == 'WIDGET') {
		$data->widget = json_encode($context->widget,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES);
	} elseif ($data->type == 'LINK') {
		$data->link_url = $context->link;
		$data->link_target = $context->target;
	}
	
	unset($data->context);
	
	$results->success = true;
	$results->data = $data;
} else {
	$results->success = false;
	$results->message = $this->getErrorText('NOT_FOUND');
}
?>