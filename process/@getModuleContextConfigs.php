<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodule.kr)
 *
 * 다른 모듈속에서 호출이 가능한 컨텍스트 모듈을 가져온다.
 * 
 * @file /modules/tab/process/@getModuleContextConfigs.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 21.
 */
if (defined('__IM__') == false) exit;

$parent = Request('parent');
$tab = Request('tab');
$module = Request('target');
$context = Request('context');

$results->success = true;

$page = $this->db()->select($this->table->context)->where('parent',$parent)->where('tab',$tab)->getOne();
if ($page != null && $page->type == 'MODULE') {
	$page->context = json_decode($page->context);
	if ($page->context->module == $module && $page->context->context == $context) {
		$values = $page->context->configs;
	} else {
		$values = null;
	}
} else {
	$values = null;
}

$mModule = $this->IM->getModule($module);
if (method_exists($mModule,'getContextConfigs') == true) $configs = $mModule->getContextConfigs(null,$values,$context);
else $configs = array();

$results->configs = $configs;
?>