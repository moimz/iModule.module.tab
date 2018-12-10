<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 * 
 * 탭 컨텍스트를 저장한다.
 *
 * @file /modules/tab/process/@saveContext.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 7. 14.
 */
if (defined('__IM__') == false) exit;

$errors = array();
$parent = Request('parent');
$oTab = Request('oTab');
$title = Request('title') ? Request('title') : $errors['title'] = $this->getErrorText('REQUIRED');
$description = Request('description');
$type = Request('type') ? Request('type') : $errors['type'] = $this->getErrorText('REQUIRED');
$tab = preg_match('/^[a-zA-Z0-9_]+$/',Request('tab')) == true ? Request('tab') : $errors['tab'] = $this->getErrorText('ALPHABET_NUMBER_UNDERBAR_ONLY');

if ($oTab != $tab && $this->db()->select($this->table->context)->where('parent',$parent)->where('tab',$tab)->has() == true) {
	$errors['tab'] = $this->getErrorText('DUPLICATED');
}

$header = new stdClass();
$footer = new stdClass();
if ($type == 'LINK') {
	$header->type = 'NONE';
	$footer->type = 'NONE';
} else {
	$header->type = Request('header_type');
	if ($header->type == 'TEXT') {
		$header = $this->IM->getModule('admin')->getWysiwygContent('header_text','tab','context.header',$header);
		$header->type = 'TEXT';
	} elseif ($header->type == 'EXTERNAL') {
		$header->external = Request('header_external') ? Request('header_external') : $errors['header_external'] = $this->getErrorText('REQUIRED');
	}
	
	$footer = new stdClass();
	$footer->type = Request('footer_type');
	if ($footer->type == 'TEXT') {
		$footer = $this->IM->getModule('admin')->getWysiwygContent('footer_text','tab','context.footer',$footer);
		$footer->type = 'TEXT';
	} elseif ($footer->type == 'EXTERNAL') {
		$footer->external = Request('footer_external') ? Request('footer_external') : $errors['footer_external'] = $this->getErrorText('REQUIRED');
	}
}
$header = json_encode($header,JSON_UNESCAPED_UNICODE);
$footer = json_encode($footer,JSON_UNESCAPED_UNICODE);

$context = new stdClass();

if ($type == 'MODULE') {
	$context->module = Request('target') ? Request('target') : $errors['target'] = $this->getErrorText('REQUIRED');
	$context->context = Request('context') ? Request('context') : $errors['context'] = $this->getErrorText('REQUIRED');
	
	if ($context->module == 'tab' && $context->context == $parent) {
		$results->success = false;
		$results->errors = array('context'=>'현재탭이 속한 탭 그룹을 선택할 수 없습니다.');
		return;
	}
	
	$configs = array();
	foreach ($_POST as $key=>$value) {
		if (preg_match('/^@(.*?)_configs_(.*?)$/',$key,$match) == true && array_key_exists('@'.$match[1],$_POST) == true) {
			if (isset($configs[$match[1].'_configs']) == false) $configs[$match[1].'_configs'] = array();
			$configs[$match[1].'_configs'][$match[2]] = $value;
		} elseif (preg_match('/^@/',$key) == true) {
			$configs[preg_replace('/^@/','',$key)] = $value;
		}
	}
	$context->configs = $configs;
} elseif ($type == 'EXTERNAL') {
	$context->external = Request('external') ? Request('external') : $errors['external'] = $this->getErrorText('REQUIRED');
} elseif ($type == 'WIDGET') {
	$context->widget = Request('widget') && json_decode(Request('widget')) != null ? json_decode(Request('widget')) : array();
} elseif ($type == 'LINK') {
	$context->link = Request('link_url') ? Request('link_url') : $errors['link_url'] = $this->getErrorText('REQUIRED');
	$context->target = Request('link_target') ? Request('link_target') : $errors['link_target'] = $this->getErrorText('REQUIRED');
}

if (count($errors) == 0) {
	$insert = array();
	$insert['parent'] = $parent;
	$insert['tab'] = $tab;
	$insert['title'] = $title;
	$insert['description'] = $description;
	$insert['type'] = $type;
	$insert['header'] = $header;
	$insert['footer'] = $footer;
	$insert['context'] = json_encode($context,JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
	
	if ($oTab) {
		$this->db()->update($this->table->context,$insert)->where('parent',$parent)->where('tab',$oTab)->execute();
	} else {
		$sort = $this->db()->select($this->table->context)->where('parent',$parent)->orderBy('sort','desc')->getOne();
		$insert['sort'] = $sort == null ? 0 : $sort->sort + 1;
		$this->db()->insert($this->table->context,$insert)->execute();
	}
	
	$results->success = true;
} else {
	$results->success = false;
	$results->errors = $errors;
}
?>