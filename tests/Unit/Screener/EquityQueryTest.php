<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Tests\Unit\Screener;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Scheb\YahooFinanceApi\Screener\EquityQuery;
use Scheb\YahooFinanceApi\Tests\TestCase;

class EquityQueryTest extends TestCase
{
    public static function provideSimpleOperators(): iterable
    {
        yield 'eq' => ['eq', ['region', 'dk'], 'EQ'];
        yield 'gt' => ['gt', ['intradayprice', 10], 'GT'];
        yield 'lt' => ['lt', ['intradayprice', 10], 'LT'];
        yield 'gte' => ['gte', ['intradayprice', 10], 'GTE'];
        yield 'lte' => ['lte', ['intradayprice', 10], 'LTE'];
    }

    #[Test]
    #[DataProvider('provideSimpleOperators')]
    public function jsonSerialize_simpleOperator_usesUppercaseWireOperator(string $operator, array $operands, string $wireOperator): void
    {
        $query = new EquityQuery($operator, $operands);

        $this->assertSame(['operator' => $wireOperator, 'operands' => $operands], $query->jsonSerialize());
    }

    #[Test]
    public function jsonSerialize_nestedQuery_usesUppercaseWireOperators(): void
    {
        $query = new EquityQuery('and', [
            new EquityQuery('is-in', ['exchange', 'CPH', 'STO']),
            new EquityQuery('btwn', ['intradaymarketcap', 1_000_000, 10_000_000]),
            new EquityQuery('or', [
                new EquityQuery('gte', ['intradayprice', 10]),
                new EquityQuery('lt', ['percentchange', -2.5]),
            ]),
        ]);

        $this->assertSame([
            'operator' => 'AND',
            'operands' => [
                [
                    'operator' => 'OR',
                    'operands' => [
                        ['operator' => 'EQ', 'operands' => ['exchange', 'CPH']],
                        ['operator' => 'EQ', 'operands' => ['exchange', 'STO']],
                    ],
                ],
                ['operator' => 'BTWN', 'operands' => ['intradaymarketcap', 1_000_000, 10_000_000]],
                [
                    'operator' => 'OR',
                    'operands' => [
                        ['operator' => 'GTE', 'operands' => ['intradayprice', 10]],
                        ['operator' => 'LT', 'operands' => ['percentchange', -2.5]],
                    ],
                ],
            ],
        ], $query->jsonSerialize());
    }

    public static function provideInvalidQueries(): iterable
    {
        yield 'operator' => ['contains', ['region', 'dk'], 'Invalid equity query operator'];
        yield 'field' => ['eq', ['not_a_field', 1], 'Invalid equity screener field'];
        yield 'region' => ['eq', ['region', 'DK'], 'Invalid region value'];
        yield 'exchange' => ['is-in', ['exchange', 'CPH', 'INVALID'], 'Invalid exchange value'];
        yield 'logical length' => ['and', [new EquityQuery('eq', ['region', 'dk'])], 'requires at least two queries'];
        yield 'logical type' => ['or', [new EquityQuery('eq', ['region', 'dk']), 'invalid'], 'must all be EquityQuery'];
        yield 'comparison type' => ['gt', ['intradayprice', '10'], 'comparison values must be numeric'];
        yield 'equality type' => ['eq', ['intradayprice', ['invalid']], 'must be strings or numeric'];
        yield 'between arity' => ['btwn', ['intradayprice', 10], 'requires exactly 3 operands'];
        yield 'between range' => ['btwn', ['intradayprice', 20, 10], 'lower bound must not exceed'];
    }

    #[Test]
    #[DataProvider('provideInvalidQueries')]
    public function construct_invalidQuery_throwsException(string $operator, array $operands, string $message): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($message);

        new EquityQuery($operator, $operands);
    }
}
