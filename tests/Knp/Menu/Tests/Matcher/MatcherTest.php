<?php

namespace Knp\Menu\Tests\Matcher;

use Knp\Menu\Matcher\Matcher;
use PHPUnit\Framework\TestCase;

final class MatcherTest extends TestCase
{
    /**
     * @param bool|null $flag
     * @param bool      $expected
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
        return [
            [true, true],
            [false, false],
            [null, false],
        ];
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
     * @param bool $value
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

        $matcher = new Matcher([$voter]);

        $this->assertSame($value, $matcher->isCurrent($item));
    }

    /**
     * @param bool $value
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

        $matcher = new Matcher([$voter1, $voter2]);

        $this->assertSame($value, $matcher->isCurrent($item));
    }

    /**
     * @param bool $value
     *
     * @dataProvider provideBoolean
     */
    public function testVoterIterator($value)
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

        $matcher = new Matcher(new \ArrayIterator([$voter1, $voter2]));

        $this->assertSame($value, $matcher->isCurrent($item));
    }

    /**
     * @param boolean $value
     *
     * @group legacy
     *
     * @dataProvider provideBoolean
     */
    public function testVoterSetter($value)
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

    /**
     * @param bool $value
     *
     * @group legacy
     *
     * @dataProvider provideBoolean
     */
    public function testVoterIteratorInConstructorAndExtraVoter($value)
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

        $matcher = new Matcher(new \ArrayIterator([$voter1]));
        $matcher->addVoter($voter2); // Added through the getter to ensure it works when using an iterator.

        $this->assertSame($value, $matcher->isCurrent($item));
    }

    public function provideBoolean()
    {
        return [
            [true],
            [false],
        ];
    }
}
