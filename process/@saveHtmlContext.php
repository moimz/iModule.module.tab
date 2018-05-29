<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodule.kr)
 *
 * HTML 컨텍스트 내용을 저장한다.
 * 
 * @file /modules/tab/process/@saveHtmlContext.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 29.
 */
if (defined('__IM__') == false) exit;

$parent = Request('parent');
$tab = Request('tab');

$tab = $this->db()->select($this->table->context)->where('parent',$parent)->where('tab',$tab)->getOne();
if ($tab == null || $tab->type != 'HTML') {
	$results->success = false;
	$results->message = $this->getErrorText('NOT_FOUND_PAGE');
	return;
}

$files = array();
$attachments = Request('attachments');
for ($i=0, $loop=count($attachments);$i<$loop;$i++) {
	$fileIdx = Decoder($attachments[$i]);
	if ($fileIdx !== false) {
		$files[] = $fileIdx;
		$this->IM->getModule('attachment')->filePublish($fileIdx);
	}
}

$html = $this->IM->getModule('wysiwyg')->encodeContent(Request('html'),$files);
$css = Request('css');

$context = new stdClass();
$context->html = $html;
$context->css = $css;
$context->files = $files;

$this->db()->update($this->table->context,array('context'=>json_encode($context,JSON_UNESCAPED_UNICODE)))->where('parent',$tab->parent)->where('tab',$tab->tab)->execute();

$results->success = true;
?>