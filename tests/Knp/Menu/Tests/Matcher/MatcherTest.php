<?php

namespace Knp\Menu\Tests\Matcher;

use Knp\Menu\Matcher\Matcher;
use PHPUnit\Framework\TestCase;

class MatcherTest extends TestCase
{
    /**
     * @param bool|null $flag
     * @param bool      $expected
     *
     * @dataProvider provideItemFlag
     */
    public function testItemFlag($flag, $expected): void
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

    public function testFlagOverwritesCache(): void
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
    public function testFlagWinsOverVoter($value): void
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
    public function testFirstVoterWins($value): void
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
    public function testVoterIterator(bool $value): void
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

    public function provideBoolean(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
