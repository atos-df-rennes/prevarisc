<?php

use PHPUnit\Framework\TestCase;

/**
 * @covers \Service_Utils_TempsRestant
 *
 * @internal
 */
final class Service_Utils_TempsRestantTest extends TestCase
{
    /**
     * @dataProvider calculateProvider
     */
    public function testCalculate(string $limitDate, string $expected): void
    {
        $this->assertSame($expected, Service_Utils_TempsRestant::calculate($limitDate));
    }

    public function calculateProvider(): array
    {
        return [
            'future limit date days' => [(new DateTime('now +1 day'))->format('Y-m-d'), '1 jour'],
            'future limit date months' => [(new DateTime('now +1 month'))->format('Y-m-d'), '1 mois'],
            'future limit date years' => [(new DateTime('now +1 year'))->format('Y-m-d'), '1 an'],
            'future limit date all parts' => [(new DateTime('now +1 year +1 month +1 day'))->format('Y-m-d'), '1 an et 1 mois et 1 jour'],
            'past limit date days' => [(new DateTime('now -1 day'))->format('Y-m-d'), '- 1 jour'],
            'past limit date months' => [(new DateTime('now -1 month'))->format('Y-m-d'), '- 1 mois'],
            'past limit date years' => [(new DateTime('now -1 year'))->format('Y-m-d'), '- 1 an'],
            'past limit date all parts' => [(new DateTime('now -1 year -1 month -1 day'))->format('Y-m-d'), '- 1 an et 1 mois et 1 jour'],
        ];
    }

    /**
     * @dataProvider getCouleurTempsRestantProvider
     */
    public function testGetCouleurTempsRestant(string $limitDate, string $expected): void
    {
        $this->assertSame($expected, Service_Utils_TempsRestant::getCouleurTempsRestant($limitDate));
    }

    public function getCouleurTempsRestantProvider(): array
    {
        return [
            'future limit date OK' => [(new DateTime('now +1 year'))->format('Y-m-d'), 'success'],
            'future limit date WARN' => [(new DateTime('now +6 months'))->format('Y-m-d'), 'warning'],
            'future limit date IMPORTANT' => [(new DateTime('now +3 months'))->format('Y-m-d'), 'important'],
            'past limit date' => [(new DateTime('now -1 day'))->format('Y-m-d'), 'inverse'],
        ];
    }
}
