<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodule.kr)
 *
 * 탭 그룹을 저장한다.
 * 
 * @file /modules/tab/process/@saveGroup.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 21.
 */
if (defined('__IM__') == false) exit;

$errors = array();

$idx = Request('idx');
$title = Request('title');
$templet = Request('templet');
$templetConfigs = new stdClass();
foreach ($_POST as $key=>$value) {
	if (preg_match('/^templet_configs_/',$key) == true) {
		$templetConfigs->{str_replace('templet_configs_','',$key)} = $value;
	}
}
$templetConfigs = json_encode($templetConfigs,JSON_UNESCAPED_UNICODE);

$header = new stdClass();
$header->type = Request('header_type');
if ($header->type == 'TEXT') {
	$header = $this->IM->getModule('admin')->getWysiwygContent('header_text','tab','group.header',$header);
	$header->type = 'TEXT';
} elseif ($header->type == 'EXTERNAL') {
	$header->external = Request('header_external') ? Request('header_external') : $errors['header_external'] = $this->getErrorText('REQUIRED');
}
$header = json_encode($header,JSON_UNESCAPED_UNICODE);

$footer = new stdClass();
$footer->type = Request('footer_type');
if ($footer->type == 'TEXT') {
	$footer = $this->IM->getModule('admin')->getWysiwygContent('footer_text','tab','group.footer',$footer);
	$footer->type = 'TEXT';
} elseif ($footer->type == 'EXTERNAL') {
	$footer->external = Request('footer_external') ? Request('footer_external') : $errors['footer_external'] = $this->getErrorText('REQUIRED');
}
$footer = json_encode($footer,JSON_UNESCAPED_UNICODE);

if ($idx) {
	if ($this->db()->select($this->table->group)->where('title',$title)->where('idx',$idx,'!=')->has() == true) {
		$errors['title'] = $this->getErrorText('DUPLICATED');
	}
	
	if (count($errors) == 0) {
		$this->db()->update($this->table->group,array('title'=>$title,'templet'=>$templet,'templet_configs'=>$templetConfigs,'header'=>$header,'footer'=>$footer))->where('idx',$idx)->execute();
	}
} else {
	if ($this->db()->select($this->table->group)->where('title',$title)->has() == true) {
		$errors['title'] = $this->getErrorText('DUPLICATED');
	}
	
	if (count($errors) == 0) {
		$this->db()->insert($this->table->group,array('title'=>$title,'templet'=>$templet,'templet_configs'=>$templetConfigs,'header'=>$header,'footer'=>$footer))->execute();
	}
}

if (count($errors) == 0) {
	$results->success = true;
} else {
	$results->success = false;
	$results->errors = $errors;
}
?>