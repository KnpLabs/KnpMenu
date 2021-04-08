<?php

namespace Knp\Menu\Tests\Matcher\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\RegexVoter;
use PHPUnit\Framework\TestCase;

final class RegexVoterTest extends TestCase
{
    /**
     * @dataProvider provideData
     */
    public function testMatching(?string $exp, ?string $itemUri, ?bool $expected): void
    {
        $item = $this->getMockBuilder(ItemInterface::class)->getMock();
        $item->expects($this->any())
            ->method('getUri')
            ->willReturn($itemUri);

        $voter = new RegexVoter($exp);

        $this->assertSame($expected, $voter->matchItem($item));
    }

    /**
     * @return array<string, array<int, string|bool|null>>
     */
    public function provideData(): array
    {
        return [
            'no regexp' => [null, 'foo', null],
            'no item uri' => ['foo', null, null],
            'matching uri' => ['/^foo/', 'foobar', true],
            'not matching uri' => ['/^foo/', 'barfoo', null],
        ];
    }
}
