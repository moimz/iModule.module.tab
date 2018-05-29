<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodule.kr)
 *
 * 탭모듈 기본템플릿 - 탭 컨텍스트
 * 
 * @file /modules/tab/templets/default/context.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 5. 21.
 */
if (defined('__IM__') == false) exit;
?>
<div data-role="tabbar">
	<div>
		<ul>
			<?php for ($i=0, $loop=count($contexts);$i<$loop;$i++) { ?>
			<li<?php echo $contexts[$i]->tab == $tab ? ' class="selected"' : ''; ?>><a href="<?php echo $me->getUrl($contexts[$i]->tab,false); ?>"><?php echo $contexts[$i]->title; ?></a></li>
			<?php } ?>
		</ul>
	</div>
</div>

<?php echo $context; ?>