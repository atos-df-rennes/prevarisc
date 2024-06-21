<?php

use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class Service_Utils_DateTest extends TestCase
{
    /**
     * @dataProvider convertToMySQLProvider
     */
    public function testConvertToMySQL(string $date, ?string $expected): void
    {
        $this->assertSame($expected, Service_Utils_Date::convertToMySQL($date));
    }

    public function convertToMySQLProvider(): array
    {
        return [
            'valid date' => ['25/12/2020', '2020-12-25'],
            'empty string' => ['', null],
            'zero string' => ['0', null],
        ];
    }

    /**
     * @dataProvider convertFromMySQLProvider
     */
    public function testConvertFromMySQL(string $date, ?string $expected): void
    {
        $this->assertSame($expected, Service_Utils_Date::convertFromMySQL($date));
    }

    public function convertFromMySQLProvider(): array
    {
        return [
            'valid date' => ['2020-12-25', '25/12/2020'],
            'empty string' => ['', null],
            'zero string' => ['0', null],
        ];
    }

    /**
     * @dataProvider formatDateWithDayNameProvider
     */
    public function testFormatDateWithDayName(string $date, ?string $expected): void
    {
        $actual = Service_Utils_Date::formatDateWithDayName($date);
        $this->assertSame(
            $this->normalizeString($expected),
            $this->normalizeString($actual)
        );
    }

    public function formatDateWithDayNameProvider(): array
    {
        return [
            'valid date' => ['25/12/2020', 'Vendredi 25 Décembre 2020'],
            'empty string' => ['', null],
            'zero string' => ['0', null],
        ];
    }

    public function formatDateWithDayNameProvidersecond(): array
    {
        return [
            'valid date' => ['28/08/2020', 'Vendredi 28 août 2020'],
            'empty string' => ['', null],
            'zero string' => ['0', null],
        ];
    }

    private function normalizeString(?string $string): ?string
    {
        if (null === $string) {
            return null;
        }

        return mb_strtolower(str_replace(['é', 'è', 'ê', 'ë'], 'e', $string));
    }
}
