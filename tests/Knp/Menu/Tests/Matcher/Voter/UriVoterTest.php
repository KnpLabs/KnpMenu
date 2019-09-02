<?php

namespace Knp\Menu\Tests\Matcher\Voter;

use Knp\Menu\Matcher\Voter\UriVoter;
use PHPUnit\Framework\TestCase;

final class UriVoterTest extends TestCase
{
    /**
     * @param string $uri
     * @param string $itemUri
     * @param bool   $expected
     *
     * @dataProvider provideData
     */
    public function testMatching($uri, $itemUri, $expected)
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $item->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue($itemUri));

        $voter = new UriVoter($uri);

        $this->assertSame($expected, $voter->matchItem($item));
    }

    public function provideData()
    {
        return [
            'no uri' => [null, 'foo', null],
            'no item uri' => ['foo', null, null],
            'same uri' => ['foo', 'foo', true],
            'different uri' => ['foo', 'bar', null],
        ];
    }
}
