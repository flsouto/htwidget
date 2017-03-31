<?php

namespace FlSouto;

abstract class HtWidget extends HtField{

	protected $label_text = '';
	protected $label_attrs = [];

	protected $readonly = false;

	protected $error_display = false;
	protected $error_attrs = [];

	protected $inline = false;
	protected $fallback = null;


	function __construct($name){
		parent::__construct($name);
		$this->label(['style'=>['display' => 'block']]);
		$this->error(['style'=>['color' => 'yellow','background'=>'red']]);
	}

	function readonly($bool=true){
		$this->readonly = $bool;
		return $this;
	}

	function inline($inline=true){
		$this->inline = (bool)$inline;
		return $this;
	}

	function label($label){
		if(is_string($label)){
			$this->label_text = $label;
		} else if(is_array($label)){
			foreach($label as $k=>$v){
				$this->setLabelAttr($k,$v);
			}
		}
		return $this;
	}

	function required($errmsg=''){
		$this->param->filters()->required($errmsg);
		return $this;
	}

	function error($error){
		if(is_array($error)){
			foreach($error as $k=>$v){
				if($k=='display'){
					$this->error_display = (bool)$v;
				} else {
					if($k=='style' && is_array($v)){
						foreach($v as $k2=>$v2){
							$this->error_attrs[$k][$k2] = $v2;
						}
					} else {
						$this->error_attrs[$k] = $v;
					}
				}
			}
		} else {
			$this->error_display = (bool)$error;
		}
		return $this;
	}

	function fallback($value, $when=[null]){
		$this->fallback = $value;
		$this->param->fallback($value, $when);
		return $this;
	}

	protected function setLabelAttr($k, $v){
		switch($k){
			case 'inline' : 
				if($v){
					$this->label_attrs['style']['display'] = 'inline-block';
					$this->label_attrs['style']['margin-right'] = '10px';							
				} else {
					$this->label_attrs['style']['display'] = 'block';							
				}
				break;
			case 'text' : 
				$this->label_text = $v;
				break;
			case 'style' :
				foreach($v as $k2 => $v2){
					$this->label_attrs['style'][$k2] = $v2;
				}
				break;
			default:
				$this->label_attrs[$k] = $v;
				break;
		}
	}

	abstract protected function renderWritable();
	abstract protected function renderReadonly();

	function render(){
		
		$attrs = new HtAttrs([
			'class' => "widget ".$this->id(),
			'style' => [
				'display' => $this->inline ? 'inline-block' : 'block'
			]
		]);

		if($this->inline){
			$attrs['style']['vertical-align'] = 'text-top';
		}

		echo "<div $attrs>\n";

		$this->renderInner();

		echo "\n</div>";

	}

	function renderInner(){
		
		$this->renderLabel();
		echo "\n";

		if($this->readonly){
			$this->renderReadonly();
		} else {
			$this->renderWritable();
		}
		echo "\n";
		$this->renderError();
	}

	protected function renderLabel(){
		
		if(empty($this->label_text)){
			return;
		}

		$attrs = new HtAttrs($this->label_attrs);
		$attrs['for'] = $this->id();

		echo "<label {$attrs}>{$this->label_text}</label>";
	}

	protected function renderError(){
		$attrs = new HtAttrs($this->error_attrs);
		if(!isset($attrs['class'])){
			$attrs['class'] = 'error';
		}
		echo "<div $attrs>\n";
		if($this->error_display){
			echo $this->validate();
		}
		echo "\n</div>";
	}

	protected function getSubmitFlag(){
		return str_replace(['[',']'],['_',''],$this->name()).'_submit';
	}

}