<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 *
 * afterGetContextPage 이벤트를 처리한다.
 * 
 * @file /modules/tab/events/afterGetContextPage.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 9. 12.
 */
if (defined('__IM__') == false) exit;

if ($target == 'core') {
	$matches = array_merge($matches,$me->getContextPage($values->module,$values->context));
}
?>