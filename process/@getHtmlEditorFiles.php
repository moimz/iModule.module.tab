<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 *
 * HTML 편집도구에 첨부된 파일목록을 가져온다.
 * 
 * @file /modules/tab/process/@getHtmlEditorFiles.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 29.
 */
if (defined('__IM__') == false) exit;

$context = Decoder(Request('context'));
if ($context === false) {
	$results->success = false;
	$results->message = $this->getErrorText('NOT_FOUND_PAGE');
	return;
}

$context = json_decode($context);
$tab = $this->db()->select($this->table->context)->where('parent',$context->parent)->where('tab',$context->tab)->getOne();
if ($tab == null || $tab->type != 'HTML') {
	$results->success = false;
	$results->message = $this->getErrorText('NOT_FOUND_PAGE');
	return;
}

$context = json_decode($tab->context);
$files = $context != null && isset($context->files) == true && is_array($context->files) == true ? $context->files : array();
for ($i=0, $loop=count($files);$i<$loop;$i++) {
	$files[$i] = $this->IM->getModule('attachment')->getFileInfo($files[$i]);
}

$results->success = true;
$results->files = $files;
?>