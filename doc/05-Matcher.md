Matcher to determine the current page
=====================================

Basics
------

A `Matcher` is capable of determining if a menu item is the menu entry for the
current page, or an ancestor thereof. The default matcher implementation uses
the *voter* pattern.

Here is an example how to use the matcher:

```php
use Knp\Menu\Matcher\Matcher;

$itemMatcher = new Matcher();
$itemMatcher->addVoter(new UriVoter(str_replace('/index.php', '', $_SERVER['REQUEST_URI'])));
```

To use the `Matcher` you have to pass it to your renderer. For the `ListRenderer`,
this looks like:

```php
use Knp\Menu\Renderer\ListRenderer;

new ListRenderer($itemMatcher);
```

Available voters
----------------

KnpMenu provides some voters for standard cases:

* `RegexVoter`: checks if the request matches a regular expression you pass to the voter
* `RouteVoter`: uses a Symfony request to check if the current route is same as the route of the menu item
* `UriVoter`: compare the URI of the menu item with the URI passed to the voter

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

Note: You can also write your own *matcher* that implements the `MatcherInterface`
if you need something different than the voter approach.

If you use the [KnpMenuBundle](http://symfony.com/doc/master/bundles/KnpMenuBundle/index.html), the RouteVoter is automatically loaded.
