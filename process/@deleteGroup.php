<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodule.kr)
 *
 * 탭 그룹을 삭제한다.
 * 
 * @file /modules/ctl/process/@deleteGroup.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 29.
 */
if (defined('__IM__') == false) exit;

$idx = Request('idx');
$group = $this->db()->select($this->table->group)->where('idx',$idx)->getOne();
if ($group == null) {
	$results->success = false;
	$results->message = $this->getErrorText('NOT_FOUND');
	return;
}

$contexts = $this->db()->select($this->table->context)->where('parent',$idx)->get();
for ($i=0, $loop=count($contexts);$i<$loop;$i++) {
	if ($contexts[$i]->type == 'HTML') {
		$context = json_decode($contexts[$i]->context);
		if ($context != null && is_array($context->files) == true && count($context->files) > 0) {
			$this->IM->getModule('attachment')->fileDelete($context->files);
		}
	}
}

$this->db()->delete($this->table->context)->where('parent',$idx)->execute();
$this->db()->delete($this->table->group)->where('idx',$idx)->execute();

$results->success = true;
?>