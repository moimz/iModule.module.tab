<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 *
 * 다른 모듈속에서 호출이 가능한 컨텍스트 모듈을 가져온다.
 * 
 * @file /modules/tab/process/@getContextModules.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 21.
 */
if (defined('__IM__') == false) exit;

$modules = $this->IM->getModule()->getContextModules();
$lists = array();
for ($i=0, $loop=count($modules);$i<$loop;$i++) {
//	if ($modules[$i]->module == 'tab') continue;
	$mModule = $this->IM->getModule($modules[$i]->module);
	if (method_exists($mModule,'setUrl') == false) continue;
	$lists[] = $modules[$i];
}

$results->success = true;
$results->lists = $lists;
$results->count = count($lists);
?>