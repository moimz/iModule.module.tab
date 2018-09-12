<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 *
 * 탭 컨텍스트 정보를 가져온다.
 * 
 * @file /modules/tab/process/@getContext.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 7. 14.
 */
if (defined('__IM__') == false) exit;

$parent = Request('parent');
$tab = Request('tab');

$data = $this->db()->select($this->table->context)->where('parent',$parent)->where('tab',$tab)->getOne();
if ($data == null) {
	$results->success = false;
	$results->message = $this->getErrorText('NOT_FOUND');
	return;
}

$header = json_decode($data->header);
$data->header_type = $header->type;
if ($data->header_type == 'EXTERNAL') {
	$data->header_external = $header->external;
} elseif ($data->header_type == 'TEXT') {
	$data->header_text = $this->IM->getModule('wysiwyg')->decodeContent($header->text,false);
	$data->header_text_files = $header->files;
}

$footer = json_decode($data->footer);
$data->footer_type = $footer->type;
if ($data->footer_type == 'EXTERNAL') {
	$data->footer_external = $footer->external;
} elseif ($data->footer_type == 'TEXT') {
	$data->footer_text = $this->IM->getModule('wysiwyg')->decodeContent($footer->text,false);
	$data->footer_text_files = $footer->files;
}

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
?>