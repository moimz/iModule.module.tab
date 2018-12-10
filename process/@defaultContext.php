<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 *
 * 탭 컨텍스트를 삭제한다.
 * 
 * @file /modules/tab/process/@defaultContext.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 12. 10.
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

$this->db()->update($this->table->context,array('is_default'=>'FALSE'))->where('parent',$parent)->execute();
$this->db()->update($this->table->context,array('is_default'=>'TRUE'))->where('parent',$parent)->where('tab',$tab)->execute();

$results->success = true;
?>