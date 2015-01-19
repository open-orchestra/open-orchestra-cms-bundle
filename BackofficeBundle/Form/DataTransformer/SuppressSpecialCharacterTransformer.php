<?php

namespace PHPOrchestra\BackofficeBundle\Form\DataTransformer;

/**
 * Class SuppressSpecialCharacterTransformer
 */
class SuppressSpecialCharacterTransformer
{
    /**
     * @param string $mixed
     *
     * @return string
     */
    public function transform($mixed)
    {
//        $chaine = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $mixed);
        $caracteres = array(
            'À' => 'a', 'Á' => 'a', 'Â' => 'a', 'Ä' => 'a', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ä' => 'a', 'È' => 'e',
            'É' => 'e', 'Ê' => 'e', 'Ë' => 'e', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'Ì' => 'i', 'Í' => 'i',
            'Î' => 'i', 'Ï' => 'i', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'Ò' => 'o', 'Ó' => 'o', 'Ô' => 'o',
            'Ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'ö' => 'o', 'Ù' => 'u', 'Ú' => 'u', 'Û' => 'u', 'Ü' => 'u',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'Œ' => 'oe', 'œ' => 'oe'
        );

        $chaine = strtr($mixed, $caracteres);
        $chaine = preg_replace('#[^0-9a-z]+#i', '', $chaine);

        return $chaine;
    }
}
