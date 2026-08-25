<?php

function calcularIntervaloSemana(string $semana): array
{
    $ano = (int) substr($semana, 0, 4);
    $numSemana = (int) substr($semana, 6, 2);

    $inicio = new DateTime();
    $inicio->setISODate($ano, $numSemana, 1); // segunda-feira

    $fim = new DateTime();
    $fim->setISODate($ano, $numSemana, 5); // sexta-feira

    return [
        'inicio' => $inicio->format('Y-m-d'),
        'fim' => $fim->format('Y-m-d'),
    ];
}