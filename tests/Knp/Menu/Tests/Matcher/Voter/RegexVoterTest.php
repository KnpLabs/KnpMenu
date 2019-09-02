<?php

namespace Knp\Menu\Tests\Matcher\Voter;

use Knp\Menu\Matcher\Voter\RegexVoter;
use PHPUnit\Framework\TestCase;

final class RegexVoterTest extends TestCase
{
    /**
     * @param string $exp
     * @param string $itemUri
     * @param bool   $expected
     *
     * @dataProvider provideData
     */
    public function testMatching($exp, $itemUri, $expected)
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $item->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue($itemUri));

        $voter = new RegexVoter($exp);

        $this->assertSame($expected, $voter->matchItem($item));
    }

    public function provideData()
    {
        return [
            'no regexp' => [null, 'foo', null],
            'no item uri' => ['foo', null, null],
            'matching uri' => ['/^foo/', 'foobar', true],
            'not matching uri' => ['/^foo/', 'barfoo', null],
        ];
    }
}
