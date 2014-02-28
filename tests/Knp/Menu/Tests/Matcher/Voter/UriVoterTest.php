<?php

namespace Knp\Menu\Tests\Matcher\Voter;

use Knp\Menu\Matcher\Voter\UriVoter;

class UriVoterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string  $uri
     * @param string  $itemUri
     * @param boolean $expected
     *
     * @dataProvider provideData
     */
    public function testMatching($uri, $itemUri, $expected)
    {
        $item = $this->getItemMock($itemUri);

        $voter = new UriVoter();
        $voter->setUri($uri);

        $this->assertSame($expected, $voter->matchItem($item));
    }

    /**
     * @param string $uri
     * @param string $itemUri
     * @param boolean $expected
     *
     * @dataProvider provideDataPrefixSupported
     */
    public function testMatchingWithPrefix($uri, $itemUri, $expected)
    {
        $item = $this->getItemMock($itemUri);

        $voter = new UriVoter();
        $voter->setUri($uri);
        $voter->setMatchPrefix(true);

        $this->assertSame($expected, $voter->matchItem($item));
    }

    public function provideData()
    {
        return array(
            'no uri' => array(null, 'foo', null),
            'no item uri' => array('foo', null, null),
            'same uri' => array('foo', 'foo', true),
            'different uri' => array('foo', 'bar', null),
            'prefixed by foo' => array('/foo/bar', '/foo', null),
            'prefixed by home' => array('/foo', '/', null)
        );
    }

    public function provideDataPrefixSupported()
    {
        $data = $this->provideData();
        $data['prefixed by foo'][2] = true;

        return $data;
    }

    private function getItemMock($uri)
    {
        $item = $this->getMock('Knp\Menu\ItemInterface');
        $item->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue($uri));

        return $item;
    }
}
