<?php

namespace Knp\Menu\Tests\Matcher;

use Knp\Menu\Matcher\Matcher;

class MatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param boolean|null $flag
     * @param boolean      $expected
     *
     * @dataProvider provideItemFlag
     */
    public function testItemFlag($flag, $expected)
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $item->expects($this->any())
            ->method('isCurrent')
            ->will($this->returnValue($flag));

        $matcher = new Matcher();

        $this->assertSame($expected, $matcher->isCurrent($item));
    }

    public function provideItemFlag()
    {
        return array(
            array(true, true),
            array(false, false),
            array(null, false),
        );
    }

    public function testFlagOverwritesCache()
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $item->expects($this->any())
            ->method('isCurrent')
            ->will($this->onConsecutiveCalls($this->returnValue(true), $this->returnValue(false)));

        $matcher = new Matcher();

        $this->assertTrue($matcher->isCurrent($item));
        $this->assertFalse($matcher->isCurrent($item));
    }

    /**
     * @param boolean $value
     *
     * @dataProvider provideBoolean
     */
    public function testFlagWinsOverVoter($value)
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $item->expects($this->any())
            ->method('isCurrent')
            ->will($this->returnValue($value));

        $voter = $this->getMockBuilder('Knp\Menu\Matcher\Voter\VoterInterface')->getMock();
        $voter->expects($this->never())
            ->method('matchItem');

        $matcher = new Matcher();
        $matcher->addVoter($voter);

        $this->assertSame($value, $matcher->isCurrent($item));
    }

    /**
     * @param boolean $value
     *
     * @dataProvider provideBoolean
     */
    public function testFirstVoterWins($value)
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $item->expects($this->any())
            ->method('isCurrent')
            ->will($this->returnValue(null));

        $voter1 = $this->getMockBuilder('Knp\Menu\Matcher\Voter\VoterInterface')->getMock();
        $voter1->expects($this->once())
            ->method('matchItem')
            ->with($this->equalTo($item))
            ->will($this->returnValue($value));

        $voter2 = $this->getMockBuilder('Knp\Menu\Matcher\Voter\VoterInterface')->getMock();
        $voter2->expects($this->never())
            ->method('matchItem');

        $matcher = new Matcher();
        $matcher->addVoter($voter1);
        $matcher->addVoter($voter2);

        $this->assertSame($value, $matcher->isCurrent($item));
    }

    public function provideBoolean()
    {
        return array(
            array(true),
            array(false),
        );
    }
}
