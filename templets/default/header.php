<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 *
 * 탭모듈 기본템플릿 - 헤더
 * 
 * @file /modules/tab/templets/default/header.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2019. 3. 29.
 */
if (defined('__IM__') == false) exit;
?>
<div data-role="tabbar">
	<div>
		<ul>
			<?php for ($i=0, $loop=count($contexts);$i<$loop;$i++) { ?>
			<li<?php echo $contexts[$i]->tab == $tab->tab ? ' class="selected"' : ''; ?>><a href="<?php echo $contexts[$i]->link; ?>"><?php echo $contexts[$i]->title; ?></a></li>
			<?php } ?>
		</ul>
	</div>
</div>