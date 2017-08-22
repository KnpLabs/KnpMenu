<?php

namespace Knp\Menu\Tests\Matcher\Voter;

use Knp\Menu\Matcher\Voter\RegexVoter;
use PHPUnit\Framework\TestCase;

class RegexVoterTest extends TestCase
{
    /**
     * @param string  $exp
     * @param string  $itemUri
     * @param boolean $expected
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
        return array(
            'no regexp' => array(null, 'foo', null),
            'no item uri' => array('foo', null, null),
            'matching uri' => array('/^foo/', 'foobar', true),
            'not matching uri' => array('/^foo/', 'barfoo', null),
        );
    }
}
