# How to apply the active class to an item and all ancestors

By default, the active menu item is applied with a `current` class and its ancestors are applied with a `current_ancestor` 
class. To apply a class other than the default or apply the same class to both, you can either pass it as an option to the 
render method of your used renderer:

```php
<?php
$renderer->render($item, ['currentClass' => 'active', 'ancestor_class' => 'active']);
```

or pass it as argument to the constructor of the used renderer:

```php
<?php
$renderer = new Renderer($matcher, ['currentClass' => 'active', 'ancestor_class' => 'active']);
```
