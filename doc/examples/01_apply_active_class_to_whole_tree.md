How to apply the active class to a item and all ancestors
=========================================================

By default the active menu item is applied with a `current` class and it's ancestors are applied with a `current_ancestor` 
class. To apply a class other than the default or apply the same class to both you can either pass it as option to the 
render method of your used renderer:

```php
<?php
$renderer->render($item, ['currentClass' => 'active', 'ancestorClass' => 'active']);
```

or pass it as argument to the constructor of the used renderer:

```php
<?php
$renderer = new Renderer($matcher, ['currentClass' => 'active', 'ancestorClass' => 'active']);
```
