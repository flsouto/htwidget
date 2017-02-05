
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

```php
<?php

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

```



In the next example we instantiate the newly defined class and render it. 

```php
<?php
require_once('vendor/autoload.php');

$field = new TextField('email');
$field->context(['email'=>'someuser@domain.com']);

echo $field;
```

Notice that by default the widget is rendered in "writable mode":

```html

<div class="widget email" style="display:block">
 <input id="email" name="email" value="someuser@domain.com" />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```



### Switch to readonly mode

To render the readonly version of your widget, simply call the `readonly` setter with a positive argument:

```php
require_once('vendor/autoload.php');

$field = new TextField('email');
$field->context(['email'=>'someuser@domain.com'])
	->readonly(true);

echo $field;
```

Output:

```html

<div class="widget email" style="display:block">
  <span>someuser@domain.com</span>
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```

#### Understanding how it works

All those extra tags surrounding the main element are produced by default. When we `echo $field` the `HtWidget::render()` method gets called which produces a wrapper containing the output of `HtWidget::renderInner()`, which in turn decides which mode we are in and calls the respective `renderReadonly` or `renderWritable` method, along with error messages. So, if you wanted to display only the inner content, without the wrapper, you would have to call the `renderInner()` method:

```php
$field->renderInner()
```



### Show the widget inline

By default the widget is rendered in block mode, which means it occupies the entire line. If you want it to appear in the same line as whatever was printed before, use the `inline` setter:

```php

$field = new TextField('username');
$field->inline(true);

echo $field;
```

Output:

```html

<div class="widget username" style="display:inline-block;vertical-align:text-top">
 <input id="username" name="username" value="" />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```



### Labels

By default the widget is rendered without an associated label. You have to specify one if you want it to be displayed:

```php

$field = new TextField('product_name');
$field->label('Product Name');

echo $field;
```

Output:

```html

<div class="widget product_name" style="display:block">
 <label style="display:block" for="product_name">Product Name</label>
 <input id="product_name" name="product_name" value="" />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```


You can change the label's tag attributes by passing an associative array. In that case you have to use the special `text` attribute in order to set the label's text:

```php

$field = new TextField('product_name');
$field->label(['text'=>'Product Name', 'class'=>'some_class']);

echo $field;
```

Output:

```html

<div class="widget product_name" style="display:block">
 <label style="display:block" class="some_class" for="product_name">Product Name</label>
 <input name="product_name" value="" />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```


By default, the label is rendered above the widget. 
If you want it to be displayed in the same line you can use the special `inline` attribute:

```php

$field = new TextField('name');
$field->label(['text'=>'Name','inline'=>true]);

echo $field;
```

Output:

```html

<div class="widget name" style="display:block">
 <label style="display:inline-block;margin-right:10px" for="name">Name</label>
 <input id="name" name="name" value="" />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```



## Set the field as required

Call the `required` method passing the error message to be shown in case the field is left blank:

```php

$field = new TextField('name');
$field
	->required('Name is required!')
	->context(['name'=>'']);

echo $field->validate();
```

Output:

```
Name is required!
```



## Activate error messsages

By default, error messages are not displayed along the field if any validation error occurs internally. You can change that by calling the `error` method with a positive value:

```php

$field = new TextField('name');
$field
	->required('Name is required!')
	->context(['name'=>''])
	->error(true);

echo $field;
```

Output:

```html

<div class="widget name" style="display:block">
 <input name="name" value="" />
 <div style="color:yellow;background:red" class="error">
    Name is required!
 </div>
</div>

```



You can customize the error message tag by passing an array of attributes to the error function (in which case the error displaying is automatically enabled):

```php

$field = new TextField('name');
$field->required('Name is required!')
	->context(['name'=>''])
	->error(['class'=>'errmsg','style'=>['padding'=>'5px']]);

echo $field;
```

Output:

```html

<div class="widget name" style="display:block">
 <input name="name" value="" />
 <div style="padding:5px" class="errmsg">
    Name is required!
 </div>
</div>

```



Disable error displaying by passing a negative (i.e. false) argument:

```php

$field = new TextField('name');
$field->required('Name is required!')
	->context(['name'=>''])
	->error(['class'=>'errmsg','style'=>['padding'=>'5px']])
	->error(false);

echo $field;
```

Output:

```html

<div class="widget name" style="display:block">
 <input name="name" value="" />
 <div style="padding:5px" class="errmsg">
 </div>
</div>

```


## Specify a default value

You can setup a default value to be used in case the field is left blank and/or a validation error occurs:

```php

$field = new TextField('amount');
$field->fallback(1);

echo $field;
```

Output:

```html

<div class="widget amount" style="display:block">
 <input id="amount" name="amount" value="1" />
 <div style="color:yellow;background:red" class="error">
 </div>
</div>

```
