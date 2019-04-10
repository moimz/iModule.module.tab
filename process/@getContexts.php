<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 * 
 * 탭 컨텍스트 목록을 가져온다.
 *
 * @file /modules/tab/process/@getContexts.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2019. 4. 9.
 */
if (defined('__IM__') == false) exit;

$parent = Request('parent');

$is_default = false;
$lists = $this->db()->select($this->table->context)->where('parent',$parent)->orderBy('sort','asc')->get();
for ($i=0, $loop=count($lists);$i<$loop;$i++) {
	$context = json_decode($lists[$i]->context);
	if ($lists[$i]->type == 'EXTERNAL') {
		$lists[$i]->context = $context->external;
	} elseif ($lists[$i]->type == 'MODULE') {
		$lists[$i]->context = $this->Module->getTitle($context->module).' - '.$this->Module->getContextTitle($context->context,$context->module);
	} elseif ($lists[$i]->type == 'PAGE') {
		$lists[$i]->context = $this->IM->getPages($lists[$i]->menu,$context->page,$lists[$i]->domain,$lists[$i]->language)->title.'('.$context->page.')';
	} elseif ($lists[$i]->type == 'LINK') {
		$lists[$i]->context = $context->link;
	} elseif ($lists[$i]->type == 'HTML') {
		$lists[$i]->context = $context != null && isset($context->html) == true && isset($context->css) == true ? '본문 : '.GetFileSize(strlen($context->html)).' / 스타일시트 : '.GetFileSize(strlen($context->css)) : '내용없음';
	}
	
	if ($lists[$i]->sort != $i) {
		$this->db()->update($this->table->context,array('sort'=>$i))->where('parent',$parent)->where('tab',$lists[$i]->tab)->execute();
		$lists[$i]->sort = $i;
	}
	
	$lists[$i]->is_default = $lists[$i]->is_default == 'TRUE';
	$is_default = $is_default || $lists[$i]->is_default;
}

if ($is_default == false && count($lists) > 0) {
	$lists[0]->is_default = true;
	$this->db()->update($this->table->context,array('is_default'=>'TRUE'))->where('parent',$lists[0]->parent)->where('tab',$lists[0]->tab)->execute();
}

$results->success = true;
$results->lists = $lists;
$results->total = count($lists);
?>