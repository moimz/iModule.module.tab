<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 *
 * 탭 컨텍스트를 삭제한다.
 * 
 * @file /modules/ctl/process/@deleteContext.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 29.
 */
if (defined('__IM__') == false) exit;

$parent = Request('parent');
$tab = Request('tab');
$context = $this->db()->select($this->table->context)->where('parent',$parent)->where('tab',$tab)->getOne();
if ($context == null) {
	$results->success = false;
	$results->message = $this->getErrorText('NOT_FOUND');
	return;
}

if ($context->type == 'HTML') {
	$context = json_decode($context->context);
	if ($context != null && is_array($context->files) == true && count($context->files) > 0) {
		$this->IM->getModule('attachment')->fileDelete($context->files);
	}
}

$this->db()->delete($this->table->context)->where('parent',$parent)->where('tab',$tab)->execute();

$contexts = $this->db()->select($this->table->context)->where('parent',$parent)->orderBy('sort','asc')->get();
for ($i=0, $loop=count($contexts);$i<$loop;$i++) {
	if ($i != $contexts[$i]->sort) {
		$this->db()->update($this->table->context,array('sort'=>$i))->where('parent',$parent)->where('tab',$contexts[$i]->tab)->execute();
	}
}

$results->success = true;
?>