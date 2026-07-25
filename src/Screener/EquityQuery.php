<?php

declare(strict_types=1);

namespace Scheb\YahooFinanceApi\Screener;

/**
 * @final
 */
class EquityQuery implements \JsonSerializable
{
    public const FIELDS = [
        'region',
        'sector',
        'peer_group',
        'industry',
        'exchange',
        'eodprice',
        'intradaypricechange',
        'intradayprice',
        'lastclosemarketcap.lasttwelvemonths',
        'percentchange',
        'lastclose52weekhigh.lasttwelvemonths',
        'fiftytwowkpercentchange',
        'lastclose52weeklow.lasttwelvemonths',
        'intradaymarketcap',
        'beta',
        'avgdailyvol3m',
        'pctheldinsider',
        'pctheldinst',
        'dayvolume',
        'eodvolume',
        'short_percentage_of_shares_outstanding.value',
        'short_interest.value',
        'short_percentage_of_float.value',
        'days_to_cover_short.value',
        'short_interest_percentage_change.value',
        'bookvalueshare.lasttwelvemonths',
        'lastclosemarketcaptotalrevenue.lasttwelvemonths',
        'lastclosetevtotalrevenue.lasttwelvemonths',
        'pricebookratio.quarterly',
        'peratio.lasttwelvemonths',
        'lastclosepricetangiblebookvalue.lasttwelvemonths',
        'lastclosepriceearnings.lasttwelvemonths',
        'pegratio_5y',
        'consecutive_years_of_dividend_growth_count',
        'returnonassets.lasttwelvemonths',
        'returnonequity.lasttwelvemonths',
        'forward_dividend_per_share',
        'forward_dividend_yield',
        'returnontotalcapital.lasttwelvemonths',
        'lastclosetevebit.lasttwelvemonths',
        'netdebtebitda.lasttwelvemonths',
        'totaldebtequity.lasttwelvemonths',
        'ltdebtequity.lasttwelvemonths',
        'ebitinterestexpense.lasttwelvemonths',
        'ebitdainterestexpense.lasttwelvemonths',
        'lastclosetevebitda.lasttwelvemonths',
        'totaldebtebitda.lasttwelvemonths',
        'quickratio.lasttwelvemonths',
        'altmanzscoreusingtheaveragestockinformationforaperiod.lasttwelvemonths',
        'currentratio.lasttwelvemonths',
        'operatingcashflowtocurrentliabilities.lasttwelvemonths',
        'totalrevenues.lasttwelvemonths',
        'netincomemargin.lasttwelvemonths',
        'grossprofit.lasttwelvemonths',
        'ebitda1yrgrowth.lasttwelvemonths',
        'dilutedepscontinuingoperations.lasttwelvemonths',
        'quarterlyrevenuegrowth.quarterly',
        'epsgrowth.lasttwelvemonths',
        'netincomeis.lasttwelvemonths',
        'ebitda.lasttwelvemonths',
        'dilutedeps1yrgrowth.lasttwelvemonths',
        'totalrevenues1yrgrowth.lasttwelvemonths',
        'operatingincome.lasttwelvemonths',
        'netincome1yrgrowth.lasttwelvemonths',
        'grossprofitmargin.lasttwelvemonths',
        'ebitdamargin.lasttwelvemonths',
        'ebit.lasttwelvemonths',
        'basicepscontinuingoperations.lasttwelvemonths',
        'netepsbasic.lasttwelvemonths',
        'netepsdiluted.lasttwelvemonths',
        'totalassets.lasttwelvemonths',
        'totalcommonsharesoutstanding.lasttwelvemonths',
        'totaldebt.lasttwelvemonths',
        'totalequity.lasttwelvemonths',
        'totalcurrentassets.lasttwelvemonths',
        'totalcashandshortterminvestments.lasttwelvemonths',
        'totalcommonequity.lasttwelvemonths',
        'totalcurrentliabilities.lasttwelvemonths',
        'totalsharesoutstanding',
        'leveredfreecashflow.lasttwelvemonths',
        'capitalexpenditure.lasttwelvemonths',
        'cashfromoperations.lasttwelvemonths',
        'leveredfreecashflow1yrgrowth.lasttwelvemonths',
        'unleveredfreecashflow.lasttwelvemonths',
        'cashfromoperations1yrgrowth.lasttwelvemonths',
        'esg_score',
        'environmental_score',
        'governance_score',
        'social_score',
        'highest_controversy',
    ];

    public const REGIONS = [
        'ae', 'ar', 'at', 'au', 'be', 'br', 'ca', 'ch', 'cl', 'cn', 'co', 'cz', 'de', 'dk', 'ee', 'eg', 'es',
        'fi', 'fr', 'gb', 'gr', 'hk', 'hu', 'id', 'ie', 'il', 'in', 'is', 'it', 'jp', 'kr', 'kw', 'lk', 'lt',
        'lv', 'mx', 'my', 'nl', 'no', 'nz', 'pe', 'ph', 'pk', 'pl', 'pt', 'qa', 'ro', 'ru', 'sa', 'se', 'sg',
        'sr', 'th', 'tr', 'tw', 'us', 've', 'vn', 'za',
    ];

    public const EXCHANGES = [
        'DFM', 'BUE', 'VIE', 'ASX', 'CXA', 'BRU', 'SAO', 'CNQ', 'NEO', 'TOR', 'VAN', 'EBS', 'SGO', 'SHH',
        'SHZ', 'BVC', 'PRA', 'BER', 'DUS', 'EUX', 'FRA', 'HAM', 'HAN', 'GER', 'MUN', 'STU', 'CPH', 'TAL',
        'CAI', 'MAD', 'MCE', 'HEL', 'ENX', 'PAR', 'AQS', 'CXE', 'IOB', 'LSE', 'ATH', 'HKG', 'BUD', 'JKT',
        'ISE', 'TLV', 'BSE', 'NSI', 'ICE', 'MDD', 'MIL', 'TLO', 'FKA', 'JPX', 'OSA', 'SAP', 'KOE', 'KSC',
        'KUW', 'CSE', 'LIT', 'RIS', 'MEX', 'KLS', 'AMS', 'DXE', 'OSL', 'NZE', 'PHP', 'PHS', 'KAR', 'WSE',
        'LIS', 'DOH', 'BVB', 'MCX', 'SAU', 'STO', 'SES', 'SET', 'IST', 'TAI', 'TWO', 'ASE', 'BTS', 'CXI',
        'NAE', 'NCM', 'NGM', 'NMS', 'NYQ', 'OEM', 'OQB', 'OQX', 'PCX', 'PNK', 'YHD', 'CCS', 'VSE', 'JNB',
    ];

    private const OPERATORS = ['EQ', 'IS-IN', 'BTWN', 'GT', 'LT', 'GTE', 'LTE', 'AND', 'OR'];

    private readonly string $operator;

    /** @var array<int, mixed> */
    private readonly array $operands;

    /**
     * @param array<int, mixed> $operands
     */
    public function __construct(string $operator, array $operands)
    {
        $this->operator = strtoupper($operator);
        $this->operands = $operands;

        $this->validate();
    }

    public static function isValidField(string $field): bool
    {
        return \in_array($field, self::FIELDS, true);
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    /** @return array<int, mixed> */
    public function getOperands(): array
    {
        return $this->operands;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toArray(): array
    {
        if ('IS-IN' === $this->operator) {
            $queries = [];
            foreach (\array_slice($this->operands, 1) as $value) {
                $queries[] = new self('EQ', [$this->operands[0], $value]);
            }

            return (new self('OR', $queries))->toArray();
        }

        return [
            'operator' => $this->operator,
            'operands' => array_map(
                static fn (mixed $operand): mixed => $operand instanceof self ? $operand->toArray() : $operand,
                $this->operands
            ),
        ];
    }

    private function validate(): void
    {
        if (!\in_array($this->operator, self::OPERATORS, true)) {
            throw new \InvalidArgumentException(\sprintf('Invalid equity query operator "%s".', $this->operator));
        }

        if ('AND' === $this->operator || 'OR' === $this->operator) {
            $this->validateLogicalOperands();

            return;
        }

        if ('IS-IN' === $this->operator) {
            if (\count($this->operands) < 2) {
                throw new \InvalidArgumentException('IS-IN requires a field and at least one value.');
            }

            $this->validateField($this->operands[0] ?? null);
            foreach (\array_slice($this->operands, 1) as $value) {
                $this->validateRestrictedValue($this->operands[0], $value);
            }

            return;
        }

        $expectedCount = 'BTWN' === $this->operator ? 3 : 2;
        if (\count($this->operands) !== $expectedCount) {
            throw new \InvalidArgumentException(\sprintf('%s requires exactly %d operands.', $this->operator, $expectedCount));
        }

        $this->validateField($this->operands[0] ?? null);

        if ('EQ' === $this->operator) {
            $this->validateRestrictedValue($this->operands[0], $this->operands[1]);
            if (!\is_string($this->operands[1]) && !\is_int($this->operands[1]) && !\is_float($this->operands[1])) {
                throw new \InvalidArgumentException('EQ comparison values must be strings or numeric.');
            }

            return;
        }

        foreach (\array_slice($this->operands, 1) as $value) {
            if (!\is_int($value) && !\is_float($value)) {
                throw new \InvalidArgumentException(\sprintf('%s comparison values must be numeric.', $this->operator));
            }
        }

        if ('BTWN' === $this->operator && $this->operands[1] > $this->operands[2]) {
            throw new \InvalidArgumentException('BTWN lower bound must not exceed its upper bound.');
        }
    }

    private function validateLogicalOperands(): void
    {
        if (\count($this->operands) < 2) {
            throw new \InvalidArgumentException(\sprintf('%s requires at least two queries.', $this->operator));
        }

        foreach ($this->operands as $operand) {
            if (!$operand instanceof self) {
                throw new \InvalidArgumentException(\sprintf('%s operands must all be EquityQuery instances.', $this->operator));
            }

            $operand->validate();
        }
    }

    private function validateField(mixed $field): void
    {
        if (!\is_string($field) || !self::isValidField($field)) {
            throw new \InvalidArgumentException(\sprintf('Invalid equity screener field "%s".', \is_scalar($field) ? (string) $field : get_debug_type($field)));
        }
    }

    private function validateRestrictedValue(string $field, mixed $value): void
    {
        $allowedValues = match ($field) {
            'region' => self::REGIONS,
            'exchange' => self::EXCHANGES,
            default => null,
        };

        if (null !== $allowedValues && !\in_array($value, $allowedValues, true)) {
            throw new \InvalidArgumentException(\sprintf('Invalid %s value "%s".', $field, \is_scalar($value) ? (string) $value : get_debug_type($value)));
        }
    }
}
