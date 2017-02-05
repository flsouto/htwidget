<?php

#mdx:h useHtWidget hidden
use FlSouto\HtWidget;

#mdx:TextField
class TextField extends HtWidget{

	function __construct($name){
		parent::__construct($name);
		// By default all HtWidget instances have a default, random id.
		// We want to change that so the id is always the name of the field itself.
		$this->attrs['id'] = $name;
	}

	// All concrete HtWidget implementations must define a renderWritable method
	function renderWritable(){
		$attrs = $this->attrs;
		$attrs['value'] = $this->value();
		echo '<input '.$attrs.' />';
	}

	// All concrete HtWidget implementations must define a renderReadonly method
	function renderReadonly(){
		$attrs = $this->attrs;
		echo "<span ".$attrs.">".$this->value()."</span>";
	}

}
#/mdx