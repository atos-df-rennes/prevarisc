<?php

class Service_Utils
{
    public function getFullFusionName(string $capsuleRubrique, array $options): string
    {
        $params = [];
        array_push($params, $capsuleRubrique);

        $options = array_map(function (string $option): string {
            return $this->formatFusionName($option);
        }, $options);

        $params = array_merge($params, $options);

        return implode('-', $params);
    }

    private function formatFusionName(string $name): string
    {
        $loweredName = strtolower($name);

        return preg_replace(['/\s+/', '/\'+/', '/\"+/'], '_', $loweredName);
    }

    public function getFusionNameMagicalCase(string $name): string
    {
        $fusionName = '';
        $strings =  preg_split("/[\s,']+/", $name);

        foreach ($strings as $string) {
            $fusionName .= ucfirst(strtolower($string));
        }

        return $fusionName;
    }
}
