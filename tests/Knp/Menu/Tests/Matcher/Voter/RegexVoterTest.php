<?php

namespace Knp\Menu\Tests\Matcher\Voter;

use Knp\Menu\Matcher\Voter\RegexVoter;

class RegexVoterTest extends \PHPUnit_Framework_TestCase
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
        $item = $this->getMock('Knp\Menu\ItemInterface');
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
