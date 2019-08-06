<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 *
 * 탭모듈 스몰템플릿 - 헤더
 *
 * @file /modules/tab/templets/intab/header.php
 * @author wioz
 * @license MIT License
 * @version 3.0.0
 * @modified 2019. 8. 6.
 */
if (defined('__IM__') == false) exit;
?>
<div data-role="intabbar">
	<ul>
		<?php for ($i=0, $loop=count($contexts);$i<$loop;$i++) { ?>
		<li<?php echo $contexts[$i]->tab == $tab->tab ? ' class="selected"' : ''; ?>><a href="<?php echo $contexts[$i]->link; ?>"><?php echo $contexts[$i]->title; ?></a></li>
		<?php } ?>
	</ul>
</div>
