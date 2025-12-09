<?php

namespace Knp\Menu\Tests\Matcher\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\CallbackVoter;
use PHPUnit\Framework\TestCase;

final class CallbackVoterTest extends TestCase
{
    public function testNoMatchCallbackSet(): void
    {
        $item = $this->createMock(ItemInterface::class);
        $item->expects($this->once())
            ->method('getExtra')
            ->with('match_callback')
            ->willReturn(null);

        $voter = new CallbackVoter();

        $this->assertNull($voter->matchItem($item));
    }

    public function testMatchCallbackIsNotCallable(): void
    {
        $item = $this->createMock(ItemInterface::class);
        $item->expects($this->once())
            ->method('getExtra')
            ->with('match_callback')
            ->willReturn('foo');

        $voter = new CallbackVoter();

        $this->expectException(\InvalidArgumentException::class);

        $voter->matchItem($item);
    }

    /**
     * @dataProvider provideData
     */
    public function testMatching(callable $callback, ?bool $expected): void
    {
        $item = $this->createMock(ItemInterface::class);
        $item->expects($this->once())
            ->method('getExtra')
            ->with('match_callback')
            ->willReturn($callback);

        $voter = new CallbackVoter();

        $this->assertSame($expected, $voter->matchItem($item));
    }

    /**
     * @return iterable<string, array{callable(): bool|null, bool|null}>
     */
    public static function provideData(): iterable
    {
        yield 'matching' => [[self::class, 'matchingCallable'], true];
        yield 'not matching' => [[self::class, 'notMatchingCallable'], false];
        yield 'skipping' => [[self::class, 'skippingCallable'], null];
    }

    private static function matchingCallable(): bool
    {
        return true;
    }

    private static function notMatchingCallable(): bool
    {
        return false;
    }

    private static function skippingCallable(): null
    {
        return null;
    }
}
