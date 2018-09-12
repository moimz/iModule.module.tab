<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 *
 * 컨텍스트 순서를 저장한다.
 * 
 * @file /modules/tab/process/@saveContextSort.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 29.
 */
if (defined('__IM__') == false) exit;

$updated = json_decode(Request('updated'));

for ($i=0, $loop=count($updated);$i<$loop;$i++) {
	$this->db()->update($this->table->context,array('sort'=>$updated[$i]->sort))->where('parent',$updated[$i]->parent)->where('tab',$updated[$i]->tab)->execute();
}

$results->success = true;