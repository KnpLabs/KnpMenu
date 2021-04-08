<?php

namespace Knp\Menu\Tests\Matcher\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\UriVoter;
use PHPUnit\Framework\TestCase;

final class UriVoterTest extends TestCase
{
    /**
     * @dataProvider provideData
     */
    public function testMatching(?string $uri, ?string $itemUri, ?bool $expected): void
    {
        $item = $this->getMockBuilder(ItemInterface::class)->getMock();
        $item->expects($this->any())
            ->method('getUri')
            ->willReturn($itemUri);

        $voter = new UriVoter($uri);

        $this->assertSame($expected, $voter->matchItem($item));
    }

    /**
     * @return array<string, array<int, string|bool|null>>
     */
    public function provideData(): array
    {
        return [
            'no uri' => [null, 'foo', null],
            'no item uri' => ['foo', null, null],
            'same uri' => ['foo', 'foo', true],
            'different uri' => ['foo', 'bar', null],
        ];
    }
}
