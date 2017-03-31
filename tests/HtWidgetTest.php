<?php

use PHPUnit\Framework\TestCase;

#mdx:h al
require_once('vendor/autoload.php');

#mdx:h textfield hidden
require_once('tests/TextField.php');

/* 

# HtWidget

## Overview

This class can be used to define different types of widgets. But what is a widget? A widget is an interactive field/element which has some sort of state and allows users to communicate with a server or a backend. Notice that not all form fields are interactive. A [hidden field](https://github.com/flsouto/hthidden), for instance, even though it has a state, it does not provide any form of direct interaction. A [button](https://github.com/flsouto/htbutton) is also not a widget, in my opinion, because it doesn't allow any complex interaction other than just clicking on it. An input text field or a checkbox, on the other hand, can be considered to be a widget because they do have a state and require some kind of interaction. In this documentation we are going to see how to implement a simple TextField class which inherits from HtWidget.

**Notice:** A lot of functionality is inherited from a more basic abstract class called HtField. If you find difficulties in  understanding some of the features being reused here, please refer to [this documentation](https://github.com/flsouto/hthidden).

## Installation

Run composer:

```
composer require flsouto/htwidget
```

## Usage

Below we are going to implement a simple `TextField` class. Pay attention to it because we are going to use it through out this document in order to learn about all the functionality inherited from `HtWidget`:

#mdx:TextField -h:al

*/

class HtWidgetTest extends TestCase{

/*

In the next example we instantiate the newly defined class and render it. 

#mdx:1

Notice that by default the widget is rendered in "writable mode":

#mdx:1 -o httidy

*/

	function testBasic(){
		#mdx:1
		$field = new TextField('email');
		$field->context(['email'=>'someuser@domain.com']);
		#/mdx echo $field
		$this->expectOutputRegex('/<input.*someuser.*/');
		echo $field;
	}

/* 

### Switch to readonly mode

To render the readonly version of your widget, simply call the `readonly` setter with a positive argument:

#mdx:2 -php

Output:

#mdx:2 -o httidy

#### Understanding how it works

All those extra tags surrounding the main element are produced by default. When we `echo $field` the `HtWidget::render()` method gets called which produces a wrapper containing the output of `HtWidget::renderInner()`, which in turn decides which mode we are in and calls the respective `renderReadonly` or `renderWritable` method, along with error messages. So, if you wanted to display only the inner content, without the wrapper, you would have to call the `renderInner()` method:

```php
$field->renderInner()
```

*/

	function testReadonly(){
		#mdx:2
		$field = new TextField('email');
		$field->context(['email'=>'someuser@domain.com'])
			->readonly(true);
		#/mdx echo $field			
		$this->expectOutputRegex('/<span.*someuser.*/');
		echo $field;		
	}
/* 

### Show the widget inline

By default the widget is rendered in block mode, which means it occupies the entire line. If you want it to appear in the same line as whatever was printed before, use the `inline` setter:

#mdx:3 -h:al -php

Output:

#mdx:3 -o httidy

*/

	function testInline(){
		#mdx:3
		$field = new TextField('username');
		$field->inline(true);
		#/mdx echo $field
		$output = $field->__toString();
		$this->assertContains('inline-block', $output);

	}
/* 

### Labels

By default the widget is rendered without an associated label. You have to specify one if you want it to be displayed:

#mdx:4 idem

Output:

#mdx:4 -o httidy

*/

	function testLabel(){
		#mdx:4
		$field = new TextField('product_name');
		$field->label('Product Name');
		#/mdx echo $field
		$this->expectOutputRegex('/label.*Product Name.*label/');
		echo $field;
	}

/* 
You can change the label's tag attributes by passing an associative array. In that case you have to use the special `text` attribute in order to set the label's text:

#mdx:5 idem

Output:

#mdx:5 -o httidy

*/

	function testLabelAttrs(){
		#mdx:5
		$field = new TextField('product_name');
		$field->label(['text'=>'Product Name', 'class'=>'some_class']);
		#/mdx echo $field
		$this->expectOutputRegex('/label.*class.*some_class.*Product.*label/');
		echo $field;
	}

/* 
By default, the label is rendered above the widget. 
If you want it to be displayed in the same line you can use the special `inline` attribute:

#mdx:6 idem

Output:

#mdx:6 -o httidy

*/

	function testLabelInline(){
		#mdx:6
		$field = new TextField('name');
		$field->label(['text'=>'Name','inline'=>true]);
		#/mdx echo $field
		$this->expectOutputRegex("/label.*style.*inline-block/");
		echo $field;
	}

	function testLabelInlineFalse(){
		#mdx:7
		$field = new TextField('name');
		$field->label(['inline'=>false]);
		$field->label(['inline'=>true]);
		#/mdx echo $field
		$this->assertNotContains("inline-block",$field->__toString());
		echo $field;
	}

/* 

## Set the field as required

Call the `required` method passing the error message to be shown in case the field is left blank:

#mdx:7b idem

Output:

#mdx:7b -o

*/

	function testRequired(){
		#mdx:7b
		$field = new TextField('name');
		$field
			->required('Name is required!')
			->context(['name'=>'']);
		#/mdx echo $field->validate()
		$this->assertContains('required', $field->validate());

	}

/* 

## Activate error messsages

By default, error messages are not displayed along the field if any validation error occurs internally. You can change that by calling the `error` method with a positive value:

#mdx:8 idem

Output:

#mdx:8 -o httidy

*/

	function testError(){
		#mdx:8
		$field = new TextField('name');
		$field
			->required('Name is required!')
			->context(['name'=>''])
			->error(true);
		#/mdx echo $field
		$this->expectOutputRegex("/error.*Name is required.*/s");

		echo $field;

	}

/* 

You can customize the error message tag by passing an array of attributes to the error function. 

#mdx:9 idem

Output:

#mdx:9 -o httidy

Notice that in this case we enable the error display by setting the 'display' attribute.

*/

	function testErrorAttrs(){
		#mdx:9
		$field = new TextField('name');
		$field->required('Name is required!')
			->context(['name'=>''])
			->error(['display'=>true,'class'=>'errmsg','style'=>['padding'=>'5px']]);
		#/mdx echo $field
		$output = $field->__toString();

		$this->assertContains('padding:5px',$output);
		$this->assertContains('errmsg',$output);
		$this->assertContains('Name is required',$output);

		echo $field;

	}

	function testErrorAttrStyleIsNotOverritten(){
		$field = new TextField('name');
		$field->required('Name is required!')
			->context(['name'=>''])
			->error(['display'=>true,'class'=>'errmsg','style'=>['padding'=>'5px']])
			->error(['moreStyle'=>'OK']);
		$output = $field->__toString();

		$this->assertContains('padding:5px',$output);
		$this->assertContains('moreStyle',$output);

		echo $field;

	}

/* 

Disable error displaying by passing a negative (i.e. false) argument:

#mdx:10 idem

Output:

#mdx:10 -o httidy

Notice: you could instead pass the 'display' attribute set to false

*/

	function testErrorDisabling(){
		#mdx:10
		$field = new TextField('name');
		$field->required('Name is required!')
			->context(['name'=>''])
			->error(['class'=>'errmsg','style'=>['padding'=>'5px']])
			->error(false);
		#/mdx echo $field
		$output = $field->__toString();

		$this->assertContains('padding:5px',$output);
		$this->assertContains('errmsg',$output);
		$this->assertNotContains('Name is required',$output);

		echo $field;

	}

/* 
Make error tag display in the same line using `inline` option:

#mdx:ErrorInline idem

Output:

#mdx:ErrorInline -o httidy

*/

	function testErrorInlineOption(){
		#mdx:ErrorInline
		$field = new TextField('name');
		$field->required('Name is required!')
			->context(['name'=>''])
			->error(['display'=>true,'inline'=>true]);
		#/mdx echo $field

		$output = $field->__toString();

		$this->assertContains('inline-block',$output);

		echo $field;

	}

/* 
## Specify a default value

You can setup a default value to be used in case the field is left blank and/or a validation error occurs:

#mdx:11 idem

Output:

#mdx:11 -o httidy

*/
	function testFallback(){
		#mdx:11
		$field = new TextField('amount');
		$field->fallback(1);
		#/mdx echo $field
		$output = $field->__toString();
		$this->assertContains('value="1"',$output);
		echo $field;
	}

}