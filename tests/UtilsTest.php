<?php

use App\services\Utils;
use PHPUnit\Framework\TestCase;

final class UtilsTest extends TestCase
{
    /**
     * @dataProvider fusionNameProvider
     */
    public function testGetDescriptifFieldFusionName(string $baseObject, array $options, string $expected): void
    {
        $utilsService = new Utils();

        $this->assertSame($expected, $utilsService->getFullFusionName($baseObject, $options));
    }

    public function fusionNameProvider(): array
    {
        return [
            'simple field' => ['descriptifEtablissement', ['Ma Rubrique', 'Mon Champ'], 'descriptifEtablissement-ma_rubrique-mon_champ'],
        ];
    }
}