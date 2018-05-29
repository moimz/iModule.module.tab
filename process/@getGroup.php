<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodule.kr)
 *
 * 탭 그룹 정보를 가져온다.
 * 
 * @file /modules/tab/process/@getGroup.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 29.
 */
if (defined('__IM__') == false) exit;

$idx = Request('idx');
$data = $this->db()->select($this->table->group)->where('idx',$idx)->getOne();
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

$results->success = true;
$results->data = $data;
?>