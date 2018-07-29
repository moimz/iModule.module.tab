<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodule.kr)
 *
 * afterGetUrl 이벤트를 처리한다.
 * 
 * @file /modules/tab/events/afterGetUrl.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 7. 29.
 */
if (defined('__IM__') == false) exit;

if ($target == 'core') {
	if ($get == 'context') {
		if ($url == null) $url = $me->getTabUrl($values->module,$values->context,$values->exacts,$values->options,$values->isSameDomain);
	}
}
?>