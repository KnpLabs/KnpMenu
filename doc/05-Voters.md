Vote for the current page
=========================

Basics
------

To guess what's the current page inside your menu, KnpMenu use a Voter mechanism.

You have to define an `ItemMatcher` that use one or many voter to decide if the page is or not the current page.

Here is an example of voter implementation:

```php
$itemMatcher = new \Knp\Menu\Matcher\Matcher();
$itemMatcher->addVoter(new UriVoter(str_replace('/index.php', '', $_SERVER['REQUEST_URI'])));
```

To use the `ItemMatcher` you have to set it as argument of the constructor of your renderer, here is an example for the `ListRenderer`:

```php
new \Knp\Menu\Renderer\ListRenderer($itemMatcher);
```

Available voters
----------------

By default KnpMenu provides some voter to help you. Here are them:

* `RegexVoter`: uses the URI and vote with a regex you set in constructor
* `RouteVoter`: it uses a Symfony2 request to check if the current route is same as the route of the menu item
* `UriVoter`: it compare the URI of the menu item with the URI you give to him

Create your own voters
----------------------

You can create your own voters by implementing `VoterInterface`.

```php
use Knp\Menu\Matcher\Voter\VoterInterface;

class MyAwesomeVoter implements VoterInterface
{
    // Your code
}
```

> You also can create your own matcher if you want to add more logic into your menus.


*Note: Voters are natively implemented inside KnpMenuBundle.*
