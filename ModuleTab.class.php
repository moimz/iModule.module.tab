<?php
/**
 * 이 파일은 iModule 탭모듈의 일부입니다. (https://www.imodules.io)
 *
 * 탭메뉴(3차메뉴)를 생성/관리한다.
 * 
 * @file /modules/tab/ModuleTab.class.php
 * @author Arzz (arzz@arzz.com)
 * @license MIT License
 * @version 3.0.0
 * @modified 2018. 9. 12.
 */
class ModuleTab {
	/**
	 * iModule 및 Module 코어클래스
	 */
	private $IM;
	private $Module;
	
	/**
	 * DB 관련 변수정의
	 *
	 * @private object $DB DB접속객체
	 * @private string[] $table DB 테이블 별칭 및 원 테이블명을 정의하기 위한 변수
	 */
	private $DB;
	private $table;
	
	/**
	 * 언어셋을 정의한다.
	 * 
	 * @private object $lang 현재 사이트주소에서 설정된 언어셋
	 * @private object $oLang package.json 에 의해 정의된 기본 언어셋
	 */
	private $lang = null;
	private $oLang = null;
	
	/**
	 * DB접근을 줄이기 위해 DB에서 불러온 데이터를 저장할 변수를 정의한다.
	 *
	 * @private $groups 탭 그룹정보
	 * @private $tabs 탭 컨텍스트 정보
	 */
	private $groups = array();
	private $tabs = array();
	
	/**
	 * class 선언
	 *
	 * @param iModule $IM iModule 코어클래스
	 * @param Module $Module Module 코어클래스
	 * @see /classes/iModule.class.php
	 * @see /classes/Module.class.php
	 */
	function __construct($IM,$Module) {
		/**
		 * iModule 및 Module 코어 선언
		 */
		$this->IM = $IM;
		$this->Module = $Module;
		
		/**
		 * 모듈에서 사용하는 DB 테이블 별칭 정의
		 * @see 모듈폴더의 package.json 의 databases 참고
		 */
		$this->table = new stdClass();
		$this->table->group = 'tab_group_table';
		$this->table->context = 'tab_context_table';
	}
	
	/**
	 * 모듈 코어 클래스를 반환한다.
	 * 현재 모듈의 각종 설정값이나 모듈의 package.json 설정값을 모듈 코어 클래스를 통해 확인할 수 있다.
	 *
	 * @return Module $Module
	 */
	function getModule() {
		return $this->Module;
	}
	
	/**
	 * 모듈 설치시 정의된 DB코드를 사용하여 모듈에서 사용할 전용 DB클래스를 반환한다.
	 *
	 * @return DB $DB
	 */
	function db() {
		if ($this->DB == null || $this->DB->ping() === false) $this->DB = $this->IM->db($this->getModule()->getInstalled()->database);
		return $this->DB;
	}
	
	/**
	 * 모듈에서 사용중인 DB테이블 별칭을 이용하여 실제 DB테이블 명을 반환한다.
	 *
	 * @param string $table DB테이블 별칭
	 * @return string $table 실제 DB테이블 명
	 */
	function getTable($table) {
		return empty($this->table->$table) == true ? null : $this->table->$table;
	}
	
	/**
	 * URL 을 가져온다.
	 *
	 * @param string $view
	 * @param string $idx
	 * @return string $url
	 */
	function getUrl($view=null,$idx=null) {
		return $this->IM->getUrl(null,null,$view,$idx);
	}
	
	/**
	 * view 값을 가져온다.
	 *
	 * @return string $view
	 */
	function getView() {
		return $this->IM->getView();
	}
	
	/**
	 * view 값을 변경한다.
	 *
	 * @param string $view
	 */
	function setView($view) {
		return $this->IM->setView($view);
	}
	
	/**
	 * idx 값을 가져온다.
	 *
	 * @return string $idx
	 */
	function getIdx() {
		return $this->IM->getIdx();
	}
	
	/**
	 * [코어] 사이트 외부에서 현재 모듈의 API를 호출하였을 경우, API 요청을 처리하기 위한 함수로 API 실행결과를 반환한다.
	 * 소스코드 관리를 편하게 하기 위해 각 요쳥별로 별도의 PHP 파일로 관리한다.
	 *
	 * @param string $protocol API 호출 프로토콜 (get, post, put, delete)
	 * @param string $api API명
	 * @param any $idx API 호출대상 고유값
	 * @param object $params API 호출시 전달된 파라메터
	 * @return object $datas API처리후 반환 데이터 (해당 데이터는 /api/index.php 를 통해 API호출자에게 전달된다.)
	 * @see /api/index.php
	 */
	function getApi($protocol,$api,$idx=null,$params=null) {
		$data = new stdClass();
		
		$values = (object)get_defined_vars();
		$this->IM->fireEvent('beforeGetApi',$this->getModule()->getName(),$api,$values);
		
		/**
		 * 모듈의 api 폴더에 $api 에 해당하는 파일이 있을 경우 불러온다.
		 */
		if (is_file($this->getModule()->getPath().'/api/'.$api.'.'.$protocol.'.php') == true) {
			INCLUDE $this->getModule()->getPath().'/api/'.$api.'.'.$protocol.'.php';
		}
		
		unset($values);
		$values = (object)get_defined_vars();
		$this->IM->fireEvent('afterGetApi',$this->getModule()->getName(),$api,$values,$data);
		
		return $data;
	}
	
	/**
	 * [사이트관리자] 모듈 관리자패널 구성한다.
	 *
	 * @return string $panel 관리자패널 HTML
	 */
	function getAdminPanel() {
		/**
		 * 설정패널 PHP에서 iModule 코어클래스와 모듈코어클래스에 접근하기 위한 변수 선언
		 */
		$IM = $this->IM;
		$Module = $this;
		
		ob_start();
		INCLUDE $this->getModule()->getPath().'/admin/index.php';
		$panel = ob_get_contents();
		ob_end_clean();
		
		return $panel;
	}
	
	/**
	 * [사이트관리자] 모듈의 전체 컨텍스트 목록을 반환한다.
	 *
	 * @return object $contexts 전체 컨텍스트 목록
	 */
	function getContexts() {
		$groups = $this->db()->select($this->table->group)->orderBy('title','asc')->get();
		$contexts = array();
		foreach ($groups as $group) {
			$contexts[] = array('context'=>$group->idx,'title'=>$group->title);
		}
		
		return $contexts;
	}
	
	/**
	 * 특정 컨텍스트에 대한 제목을 반환한다.
	 *
	 * @param string $context 컨텍스트명
	 * @return string $title 컨텍스트 제목
	 */
	function getContextTitle($context) {
		$group = $this->getGroup($context);
		return $group == null ? '' : $group->title;
	}
	
	/**
	 * [사이트관리자] 모듈의 컨텍스트 환경설정을 구성한다.
	 *
	 * @param object $site 설정대상 사이트
	 * @param object $values 설정값
	 * @param string $context 설정대상 컨텍스트명
	 * @return object[] $configs 환경설정
	 */
	function getContextConfigs($site,$values,$context) {
		$configs = array();
		
		$templet = new stdClass();
		$templet->title = $this->getText('admin/configs/form/templet');
		$templet->name = 'templet';
		$templet->type = 'templet';
		$templet->target = 'tab';
		$templet->use_default = true;
		$templet->value = $values != null && isset($values->templet) == true ? $values->templet : '#';
		$configs[] = $templet;
		
		return $configs;
	}
	
	/**
	 * 사이트맵에 나타날 뱃지데이터를 생성한다.
	 *
	 * @param string $context 컨텍스트종류
	 * @param object $configs 사이트맵 관리를 통해 설정된 페이지 컨텍스트 설정
	 * @return object $badge 뱃지데이터 ($badge->count : 뱃지숫자, $badge->latest : 뱃지업데이트 시각(UNIXTIME), $badge->text : 뱃지텍스트)
	 * @todo check count information
	 */
	function getContextBadge($context,$config) {
		/**
		 * null 일 경우 뱃지를 표시하지 않는다.
		 */
		return null;
	}
	
	/**
	 * 언어셋파일에 정의된 코드를 이용하여 사이트에 설정된 언어별로 텍스트를 반환한다.
	 * 코드에 해당하는 문자열이 없을 경우 1차적으로 package.json 에 정의된 기본언어셋의 텍스트를 반환하고, 기본언어셋 텍스트도 없을 경우에는 코드를 그대로 반환한다.
	 *
	 * @param string $code 언어코드
	 * @param string $replacement 일치하는 언어코드가 없을 경우 반환될 메세지 (기본값 : null, $code 반환)
	 * @return string $language 실제 언어셋 텍스트
	 */
	function getText($code,$replacement=null) {
		if ($this->lang == null) {
			if (is_file($this->getModule()->getPath().'/languages/'.$this->IM->language.'.json') == true) {
				$this->lang = json_decode(file_get_contents($this->getModule()->getPath().'/languages/'.$this->IM->language.'.json'));
				if ($this->IM->language != $this->getModule()->getPackage()->language && is_file($this->getModule()->getPath().'/languages/'.$this->getModule()->getPackage()->language.'.json') == true) {
					$this->oLang = json_decode(file_get_contents($this->getModule()->getPath().'/languages/'.$this->getModule()->getPackage()->language.'.json'));
				}
			} elseif (is_file($this->getModule()->getPath().'/languages/'.$this->getModule()->getPackage()->language.'.json') == true) {
				$this->lang = json_decode(file_get_contents($this->getModule()->getPath().'/languages/'.$this->getModule()->getPackage()->language.'.json'));
				$this->oLang = null;
			}
		}
		
		$returnString = null;
		$temp = explode('/',$code);
		
		$string = $this->lang;
		for ($i=0, $loop=count($temp);$i<$loop;$i++) {
			if (isset($string->{$temp[$i]}) == true) {
				$string = $string->{$temp[$i]};
			} else {
				$string = null;
				break;
			}
		}
		
		if ($string != null) {
			$returnString = $string;
		} elseif ($this->oLang != null) {
			if ($string == null && $this->oLang != null) {
				$string = $this->oLang;
				for ($i=0, $loop=count($temp);$i<$loop;$i++) {
					if (isset($string->{$temp[$i]}) == true) {
						$string = $string->{$temp[$i]};
					} else {
						$string = null;
						break;
					}
				}
			}
			
			if ($string != null) $returnString = $string;
		}
		
		$this->IM->fireEvent('afterGetText',$this->getModule()->getName(),$code,$returnString);
		
		/**
		 * 언어셋 텍스트가 없는경우 iModule 코어에서 불러온다.
		 */
		if ($returnString != null) return $returnString;
		elseif (in_array(reset($temp),array('text','button','action')) == true) return $this->IM->getText($code,$replacement);
		else return $replacement == null ? $code : $replacement;
	}
	
	/**
	 * 상황에 맞게 에러코드를 반환한다.
	 *
	 * @param string $code 에러코드
	 * @param object $value(옵션) 에러와 관련된 데이터
	 * @param boolean $isRawData(옵션) RAW 데이터 반환여부
	 * @return string $message 에러 메세지
	 */
	function getErrorText($code,$value=null,$isRawData=false) {
		$message = $this->getText('error/'.$code,$code);
		if ($message == $code) return $this->IM->getErrorText($code,$value,null,$isRawData);
		
		$description = null;
		switch ($code) {
			default :
				if (is_object($value) == false && $value) $description = $value;
		}
		
		$error = new stdClass();
		$error->message = $message;
		$error->description = $description;
		$error->type = 'BACK';
		
		if ($isRawData === true) return $error;
		else return $this->IM->getErrorText($error);
	}
	
	/**
	 * 템플릿 정보를 가져온다.
	 *
	 * @param string $this->getTemplet($configs) 템플릿명
	 * @return string $package 템플릿 정보
	 */
	function getTemplet($templet) {
		if (is_object($templet) == true) {
			$templet_configs = isset($templet->templet_configs) == true ? $templet->templet_configs : null;
			$templet = $templet->templet;
		} else {
			$templet = $templet;
			$templet_configs = null;
		}
		
		return $this->getModule()->getTemplet($templet,$templet_configs);
	}
	
	/**
	 * 페이지 컨텍스트를 가져온다.
	 *
	 * @param string $context 컨텍스트명
	 * @param object $configs 사이트맵 관리를 통해 설정된 페이지 컨텍스트 설정
	 * @return string $html 컨텍스트 HTML
	 */
	function getContext($context,$configs=null) {
		/**
		 * 모듈 기본 스타일 및 자바스크립트
		 */
		$this->IM->addHeadResource('style',$this->getModule()->getDir().'/styles/style.css');
		$this->IM->addHeadResource('script',$this->getModule()->getDir().'/scripts/script.js');
		
		$group = $this->getGroup($context);
		if ($group == null) return $this->getError('NOT_FOUND_PAGE');
		if ($configs == null) $configs = new stdClass();
		if (isset($configs->templet) == false) $configs->templet = '#';
		if ($configs->templet == '#') {
			$configs->templet = $group->templet;
			$configs->templet_configs = $group->templet_configs;
		} else {
			$configs->templet_configs = isset($configs->templet_configs) == true ? $configs->templet_configs : null;
		}
		
		$html = PHP_EOL.'<!-- TAB MODULE -->'.PHP_EOL.'<div data-role="context" data-type="module" data-module="tab" data-context="'.$context.'">'.PHP_EOL;
		$html.= $this->getHeader($context,$configs);
		$html.= $this->getTabContainerContext($context,$configs);
		$html.= $this->getFooter($context,$configs);
		
		/**
		 * 컨텍스트 컨테이너를 설정한다.
		 */
		$html.= PHP_EOL.'</div>'.PHP_EOL.'<!--// EXAMPLE #1 MODULE -->'.PHP_EOL;
		
		return $html;
	}
	
	/**
	 * 컨텍스트 헤더를 가져온다.
	 *
	 * @param object $configs 사이트맵 관리를 통해 설정된 페이지 컨텍스트 설정
	 * @return string $html 컨텍스트 HTML
	 */
	function getHeader($context,$configs=null) {
		$group = $this->getGroup($context);
		
		$contexts = $this->db()->select($this->table->context)->where('parent',$context)->orderBy('sort','asc')->get();
		if (count($contexts) == 0) return $this->getError('NO_CONTEXTS');
		
		$tab = $this->getView() ? $this->getView() : $contexts[0]->tab;
		$tab = $this->getTab($context,$tab);
		
		/**
		 * 템플릿파일을 호출한다.
		 */
		return $this->getTemplet($configs)->getHeader(get_defined_vars());
	}
	
	/**
	 * 컨텍스트 푸터를 가져온다.
	 *
	 * @param object $configs 사이트맵 관리를 통해 설정된 페이지 컨텍스트 설정
	 * @return string $html 컨텍스트 HTML
	 */
	function getFooter($context,$configs=null) {
		$group = $this->getGroup($context);
		
		/**
		 * 템플릿파일을 호출한다.
		 */
		return $this->getTemplet($configs)->getFooter(get_defined_vars());
	}
	
	/**
	 * 에러메세지를 반환한다.
	 *
	 * @param string $code 에러코드 (에러코드는 iModule 코어에 의해 해석된다.)
	 * @param object $value 에러코드에 따른 에러값
	 * @return $html 에러메세지 HTML
	 */
	function getError($code,$value=null) {
		/**
		 * iModule 코어를 통해 에러메세지를 구성한다.
		 */
		$error = $this->getErrorText($code,$value,true);
		return $this->IM->getError($error);
	}
	
	/**
	 * 탭 컨텍스트를 가져온다.
	 *
	 * @param int $parent 탭 그룹 고유값
	 * @param object $configs 사이트맵 관리를 통해 설정된 페이지 컨텍스트 설정
	 * @return string $html 컨텍스트 HTML
	 */
	function getTabContainerContext($parent,$configs=null) {
		$contexts = $this->db()->select($this->table->context)->where('parent',$parent)->orderBy('sort','asc')->get();
		if (count($contexts) == 0) return $this->getError('NO_CONTEXTS');
		
		$tab = $this->getView() ? $this->getView() : $contexts[0]->tab;
		$tab = $this->getTab($parent,$tab);
		if ($tab == null) return $this->getError('NOT_FOUND_PAGE');
		
		if ($tab->header->type == 'TEXT') {
			$header = '<div class="header">'.$this->IM->getModule('wysiwyg')->decodeContent($tab->header->text).'</div>';
		} elseif ($tab->header->type == 'EXTERNAL') {
			$header = '<div class="header">'.$this->getTemplet($configs)->getExternal($tab->header->external).'</div>';
		} else {
			$header = '';
		}
		
		if ($tab->footer->type == 'TEXT') {
			$footer = '<div class="header">'.$this->IM->getModule('wysiwyg')->decodeContent($tab->footer->text).'</div>';
		} elseif ($tab->footer->type == 'EXTERNAL') {
			$footer = '<div class="header">'.$this->getTemplet($configs)->getExternal($tab->footer->external).'</div>';
		} else {
			$footer = '';
		}
		
		$context = $this->getTabContext($parent,$tab->tab);
		
		/**
		 * 템플릿파일을 호출한다.
		 */
		return $this->getTemplet($configs)->getContext('context',get_defined_vars(),$header,$footer);
	}
	
	/**
	 * 탭 내부 컨텍스트를 가져온다.
	 *
	 * @param int $parent 탭 그룹 고유값
	 * @param string $tab 탭 컨텍스트 ID
	 * @return string $html 컨텍스트 HTML
	 */
	function getTabContext($parent,$tab) {
		$context = $this->getTab($parent,$tab);
		
		/**
		 * 컨텍스트 종류가 EXTERNAL 일 경우
		 * 서버내 특정 디렉토리에 존재하는 PHP 파일 내용을 가지고 온다.
		 * $config->context->external : 불러올 외부 PHP 파일명
		 */
		if ($context->type == 'EXTERNAL') {
			return $this->IM->getExternalContext($context->context->external);
		}
		
		/**
		 * 컨텍스트 종류가 WIDGET 일 경우
		 * 위젯마법사를 이용하여 위젯만으로 이루어진 페이지에 대한 컨텍스트를 가지고 온다.
		 * $page->context->widget : 위젯마법사를 이용해 만들어진 위젯레이아웃 코드
		 */
		if ($context->type == 'WIDGET') {
			return $this->IM->getWidgetContext($context->context->widget);
		}
		
		/**
		 * 컨텍스트 종류가 HTML 일 경우
		 */
		if ($context->type == 'HTML') {
			return $this->getHtmlContext($context);
		}
		
		/**
		 * 컨텍스트 종류가 MODULE 일 경우
		 * 설정된 모듈 클래스를 선언하고 모듈클래스내의 getContext 함수를 호출하여 컨텍스트를 가져온다.
		 * $page->context->module : 불러올 모듈명
		 * $page->context->context : 해당 모듈에서 불러올 컨텍스트 종류
		 * $page->context->widget : 해당 모듈에 전달할 환경설정값 (예 : 템플릿명 등)
		 */
		if ($context->type == 'MODULE') {
			return $this->IM->getModule($context->context->module)->setUrl($context->tab)->getContext($context->context->context,$context->context->configs);
		}
	}
	
	/**
	 * 본문편집 컨텍스트를 가져온다.
	 *
	 * @param object $context 컨텍스트 설정
	 * @return string $html 컨텍스트 HTML
	 */
	function getHtmlContext($context) {
		$view = $this->getIdx();
		
		/**
		 * 편집모드 일 경우, 편집페이지를 불러온다.
		 */
		if ($view == 'edit') {
			if ($this->IM->getModule('member')->isLogged() == false) return $this->getError('REQUIRED_LOGIN');
			if ($this->IM->getModule('member')->isAdmin() == false) return $this->getError('FORBIDDEN');
			
			$parent = $context->parent;
			$tab = $context->tab;
			$html = $context->context != null && isset($context->context->html) == true ? $context->context->html : '';
			$css = $context->context != null && isset($context->context->css) == true ? $context->context->css : '';
			
			$mAdmin = $this->IM->getModule('admin');
			$this->IM->addHeadResource('style',$mAdmin->getModule()->getDir().'/styles/html.css');
			$this->IM->addHeadResource('script',$mAdmin->getModule()->getDir().'/scripts/html.js');
			$this->IM->getModule('wysiwyg')->addCodeMirrorMode('css')->preload();
			$this->IM->addHeadResource('script',$this->IM->getModule('wysiwyg')->getModule()->getDir().'/scripts/codemirror/addon/edit/closetag.js');
			
			$uploader = $this->IM->getModule('attachment')->setTemplet('default')->setModule('tab')->setWysiwyg('wysiwyg')->setLoader($this->IM->getProcessUrl('tab','@getHtmlEditorFiles',array('context'=>Encoder(json_encode(array('parent'=>$parent,'tab'=>$tab))))))->get();
			$wysiwyg = $this->IM->getModule('wysiwyg')->setId('ModuleAdminHtmlEditor')->setModule('tab')->setName('wysiwyg')->setContent($html)->get(true);
			
			ob_start();
			
			echo PHP_EOL.'<form id="ModuleAdminHtmlEditorForm" data-submit="'.$this->IM->getProcessUrl('tab','@saveHtmlContext').'">'.PHP_EOL;
			echo '<input type="hidden" name="parent" value="'.$parent.'">'.PHP_EOL;
			echo '<input type="hidden" name="tab" value="'.$tab.'">'.PHP_EOL;
			
			echo '<style data-role="style">'.$css.'</style>';
			
			$IM = $this->IM;
			INCLUDE $mAdmin->getModule()->getPath().'/includes/html.php';
			
			echo PHP_EOL.'</form>'.PHP_EOL.'<script>$(document).ready(function() { HtmlEditor.init(); });</script>'.PHP_EOL;
			
			$context = ob_get_clean();
			
			return $context;
		} else {
			$parent = $context->parent;
			$tab = $context->tab;
			$html = $context->context != null && isset($context->context->html) == true ? $context->context->html : '';
			$css = $context->context != null && isset($context->context->css) == true ? $context->context->css : '';
			
			if ($html) $html = $this->IM->getModule('wysiwyg')->decodeContent($html,false);
			
			$context = PHP_EOL.'<!-- HTML CONTEXT START -->'.PHP_EOL;
			$context = '<style>'.$css.'</style>'.PHP_EOL;
			$context.= '<div data-role="context" data-type="html" data-parent="'.$parent.'" data-tab="'.$tab.'">'.PHP_EOL;
			if ($this->IM->getModule('member')->isAdmin() == true) $context.= '<a href="'.$this->getUrl(null,'edit').'" class="edit"><i class="mi mi-pen"></i><span>페이지 편집</span></a>';
			$context.= '<div data-role="wysiwyg-content">'.$html.'</div>';
			$context.= PHP_EOL.'</div>'.PHP_EOL.'<!-- HTML CONTEXT END -->'.PHP_EOL;
			
			$values = (object)get_defined_vars();
			$this->IM->fireEvent('afterGetContext','core','html',$values,$context);
		}
		
		return $context;
	}
	
	/**
	 * 탭 그룹 정보를 가져온다.
	 *
	 * @param int $idx 탭 그룹 고유값
	 * @return object $group
	 */
	function getGroup($idx) {
		if (isset($this->groups[$idx]) == true) return $this->groups[$idx];
		$group = $this->db()->select($this->table->group)->where('idx',$idx)->getOne();
		if ($group == null) return null;
		
		$group->templet_configs = json_decode($group->templet_configs);
		
		$this->groups[$idx] = $group;
		return $this->groups[$idx];
	}
	
	/**
	 * 탭 컨텍스트 정보를 가져온다.
	 *
	 * @param int $parent 탭 그룹 고유값
	 * @param string $tab 탭 컨텍스트 ID
	 * @return object $context
	 */
	function getTab($parent,$tab) {
		if (isset($this->tabs[$parent.'-'.$tab]) == true) return $this->tabs[$parent.'-'.$tab];
		$context = $this->db()->select($this->table->context)->where('parent',$parent)->where('tab',$tab)->getOne();
		if ($context == null) return null;
		
		$context->context = json_decode($context->context);
		$context->header = json_decode($context->header);
		$context->footer = json_decode($context->footer);
		
		$this->tabs[$parent.'-'.$tab] = $context;
		return $this->tabs[$parent.'-'.$tab];
	}
	
	/**
	 * 특정 모듈의 특정 컨텍스트를 사용하도록 설정된 탭을 반환한다.
	 *
	 * @param string $module 모듈명
	 * @param string $context 컨텍스트명
	 * @return object[] $matches 조건과 일치하는 탭 객체
	 */
	function getContextPage($module,$context) {
		$matches = array();
		$tabs = $this->db()->select($this->table->context)->where('type','MODULE')->where('context','{"module":"'.$module.'"%','LIKE')->get();
		foreach ($tabs as $tab) {
			$tab->context = json_decode($tab->context);
			if ($context != $tab->context->context) continue;
			
			/**
			 * 사이트맵의 도메인정보를 검색결과에 추가한다.
			 */
			$pages = $this->IM->getContextPage('tab',$tab->parent);
			foreach ($pages as $page) {
				$item = new stdClass();
				$item->domain = $page->domain;
				$item->language = $page->language;
				$item->context = $tab->context;
				$item->url = $page->url.'/'.$tab->tab;
				$matches[] = $item;
			}
		}
		
		return $matches;
	}
	
	/**
	 * 현재 모듈에서 처리해야하는 요청이 들어왔을 경우 처리하여 결과를 반환한다.
	 * 소스코드 관리를 편하게 하기 위해 각 요쳥별로 별도의 PHP 파일로 관리한다.
	 * 작업코드가 '@' 로 시작할 경우 사이트관리자를 위한 작업으로 최고관리자 권한이 필요하다.
	 *
	 * @param string $action 작업코드
	 * @return object $results 수행결과
	 * @see /process/index.php
	 */
	function doProcess($action) {
		$results = new stdClass();
		
		$values = (object)get_defined_vars();
		$this->IM->fireEvent('beforeDoProcess',$this->getModule()->getName(),$action,$values);
		
		/**
		 * 모듈의 process 폴더에 $action 에 해당하는 파일이 있을 경우 불러온다.
		 */
		if (is_file($this->getModule()->getPath().'/process/'.$action.'.php') == true) {
			INCLUDE $this->getModule()->getPath().'/process/'.$action.'.php';
		}
		
		unset($values);
		$values = (object)get_defined_vars();
		$this->IM->fireEvent('afterDoProcess',$this->getModule()->getName(),$action,$values,$results);
		
		return $results;
	}
}
?>