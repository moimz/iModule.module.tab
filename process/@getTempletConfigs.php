<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 *
 * 템플릿 설정을 가져온다.
 * 
 * @file /modules/tab/process/@getTempletConfigs.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 23.
 */
if (defined('__IM__') == false) exit;

$parent = Request('parent');
$tab = Request('tab');
$name = Request('name');
$type = Request('type');
$target = Request('target');
$templet = Request('templet');
$position = Request('position');
$module = Request('module');

if ($type == 'module') {
	$Templet = $this->IM->getModule($target,true)->getTemplet($templet);
	
	$context = $this->db()->select($this->table->context)->where('parent',$parent)->where('tab',$tab)->getOne();
	if ($context != null && $context->type == 'MODULE') {
		$name = preg_replace('/^@/','',$name);
		$context = json_decode($context->context);
		
		if ($context->module == $module && $context->configs->{$name} == $templet && isset($context->configs->{$name.'_configs'}) == true) {
			$Templet->setConfigs($context->configs->{$name.'_configs'});
		}
	}
	
	$configs = $Templet->getConfigs();
}

$results->success = true;
$results->configs = $configs;
?>