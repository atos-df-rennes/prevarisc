<?php

use App\services\Utils;
use PHPUnit\Framework\TestCase;

final class UtilsTest extends TestCase
{
    private $utils;

    public function setUp()
    {
        $this->utils = new Utils();
    }

    /**
     * @dataProvider fusionNameProvider
     */
    public function testGetDescriptifFieldFusionName(string $baseObject, array $options, string $expected): void
    {
        $this->assertSame($expected, $this->utils->getFullFusionName($baseObject, $options));
    }

    /**
     * @dataProvider magicalNameProvider
     */
    public function testGetDescriptifFieldMagicalName(string $initialName, string $expected): void
    {
        $this->assertSame($expected, $this->utils->getFusionNameMagicalCase($initialName));
    }

    public function fusionNameProvider(): array
    {
        return [
            'simple field with rubrique' => ['descriptifEtablissement', ['Ma Rubrique', 'Mon Champ'], 'descriptifEtablissement-ma_rubrique-mon_champ'],
            'formatted field without rubrique' => ['descriptifEtablissement', ['Mon Champ d\'enfer "Personnalisé"'], 'descriptifEtablissement-mon_champ_d_enfer__personnalisé_'],
        ];
    }

    public function magicalNameProvider(): array
    {
        return [
            'simple field' => ['descriptif Rubrique Un champ', 'DescriptifRubriqueUnChamp'],
            'formatted field' => ['descriptif rUbrique un Champ d\'enfer "Personnalisé"', 'DescriptifRubriqueUnChampDEnferPersonnalisé'],
        ];
    }
}