<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NumberConversionController extends AbstractController
{
    #[Route('/index', name: 'app_index_conversion')]
    public function index()
    {
        return $this->render('number_conversion/index.html.twig');
    }

    #[Route('/number/conversor', name: 'app_conversor')]
    public function conversor()
    {
        return $this->render('number_conversion/conversor.html.twig');
    }

    #[Route('/number/roman', name: 'app_number_conversion', methods:'POST')]
    public function convertRoman(Request $request): JsonResponse
    {
        $number = $request->request->get('number');

        /**O maior número literal que pode ser convertido em de ou para romanos é 3.999.000 já que o maior algarismo romano representa 1.000.000.
        Mas como esses algarimos não exitem no nosso padrão de teclado (são os mesmos algarimos romanos mas com um traço horizontal em cima) eu decido por
        converter apenas números até 3.999 por limitações de digitação. Assim como o 0 também não pode ser usado por não existir um algarismo que o represente.**/
        
        //usei este if para validar os valores inseridos
        if(!is_numeric($number) || $number < 1 || $number > 3999) {
            return new JsonResponse(['error' => 'Valor inválido']);
        }

        $roman = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];

        $result = '';
        foreach ($roman as $romans => $value) {
            while ($number >= $value) {
                $result .= $romans;
                $number -= $value;
            }
        }

        return new JsonResponse(['result' => $result]);
    }

    #[Route('/number/arabic', name: 'app_number_conversion', methods:'POST')]
    public function convertArabic (Request $request): JsonResponse {
        $romans = strtoupper($request->request->get('number'));

        /**Mesma lógica do anterior, o maior número romano que pode ser convertido é 3.999 (MMMCMXCIX)
        Além de verificar se as letras inseridas correspondem a algarismos romanos**/
        //Restrição adicionada para evitar valores inválidos como XXXX, MMMM e CCCC.

        if (!preg_match('/^(?:M{0,3})(?:CM|CD|D?C{0,3})(?:XC|XL|L?X{0,3})(?:IX|IV|V?I{0,3})$/', $romans)){
            return new JsonResponse(['error' => 'Número romano inválido']);
        }

        $roman = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];

        $result = 0;
        $i = 0;
        while ($i <strlen($romans)) {
            if ($i +1 < strlen($romans) && isset($roman[$romans[$i] . $romans[$i +1]])) {
                $result += $roman[$romans[$i] . $romans[$i + 1]];
                $i += 2;
            } else {
                $result += $roman[$romans[$i]];
                $i++;
            }
        }

        return new JsonResponse(['result' => $result]);
    }
}


