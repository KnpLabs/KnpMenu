<?php

namespace Knp\Menu\Tests\Matcher;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\Voter\VoterInterface;
use PHPUnit\Framework\TestCase;

final class MatcherTest extends TestCase
{
    /**
     * @dataProvider provideItemFlag
     */
    public function testItemFlag(?bool $flag, bool $expected): void
    {
        $item = $this->getMockBuilder(ItemInterface::class)->getMock();
        $item->expects($this->any())
            ->method('isCurrent')
            ->willReturn($flag);

        $matcher = new Matcher();

        $this->assertSame($expected, $matcher->isCurrent($item));
    }

    /**
     * @return array<int, array<int, bool|null>>
     */
    public function provideItemFlag(): array
    {
        return [
            [true, true],
            [false, false],
            [null, false],
        ];
    }

    public function testFlagOverwritesCache(): void
    {
        $item = $this->getMockBuilder(ItemInterface::class)->getMock();
        $item
            ->method('isCurrent')
            ->will($this->onConsecutiveCalls($this->returnValue(true), $this->returnValue(false)));

        $matcher = new Matcher();

        $this->assertTrue($matcher->isCurrent($item));
        $this->assertFalse($matcher->isCurrent($item));
    }

    /**
     * @dataProvider provideBoolean
     */
    public function testFlagWinsOverVoter(bool $value): void
    {
        $item = $this->getMockBuilder(ItemInterface::class)->getMock();
        $item->expects($this->any())
            ->method('isCurrent')
            ->willReturn($value);

        $voter = $this->getMockBuilder(VoterInterface::class)->getMock();
        $voter->expects($this->never())
            ->method('matchItem');

        $matcher = new Matcher([$voter]);

        $this->assertSame($value, $matcher->isCurrent($item));
    }

    /**
     * @dataProvider provideBoolean
     */
    public function testFirstVoterWins(bool $value): void
    {
        $item = $this->getMockBuilder(ItemInterface::class)->getMock();
        $item->expects($this->any())
            ->method('isCurrent')
            ->willReturn(null);

        $voter1 = $this->getMockBuilder(VoterInterface::class)->getMock();
        $voter1->expects($this->once())
            ->method('matchItem')
            ->with($this->equalTo($item))
            ->willReturn($value);

        $voter2 = $this->getMockBuilder(VoterInterface::class)->getMock();
        $voter2->expects($this->never())
            ->method('matchItem');

        $matcher = new Matcher([$voter1, $voter2]);

        $this->assertSame($value, $matcher->isCurrent($item));
    }

    /**
     * @dataProvider provideBoolean
     */
    public function testVoterIterator(bool $value): void
    {
        $item = $this->getMockBuilder(ItemInterface::class)->getMock();
        $item->expects($this->any())
            ->method('isCurrent')
            ->willReturn(null);

        $voter1 = $this->getMockBuilder(VoterInterface::class)->getMock();
        $voter1->expects($this->once())
            ->method('matchItem')
            ->with($this->equalTo($item))
            ->willReturn($value);

        $voter2 = $this->getMockBuilder(VoterInterface::class)->getMock();
        $voter2->expects($this->never())
            ->method('matchItem');

        $matcher = new Matcher(new \ArrayIterator([$voter1, $voter2]));

        $this->assertSame($value, $matcher->isCurrent($item));
    }

    /**
     * @return array<int, array<int, bool>>
     */
    public function provideBoolean(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
