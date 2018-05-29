<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodule.kr)
 *
 * 탭 그룹 목록을 가져온다.
 * 
 * @file /modules/tab/process/@getGroups.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 21.
 */
if (defined('__IM__') == false) exit;

$lists = $this->db()->select($this->table->group,'idx, title, templet')->get();
for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$templet = $this->getTemplet($lists[$i]->templet);
	
	$lists[$i]->templet = $templet->getTitle().'('.$templet->getDir().')';
	$lists[$i]->contexts = $this->db()->select($this->table->context)->where('parent',$lists[$i]->idx)->count();
}
$results->success = true;
$results->lists = $lists;
$results->total = count($lists);
?>