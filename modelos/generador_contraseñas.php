<?php
function generar_contraseña($tamaño)
{
    if ($tamaño < 8) {
        $tamaño = 8;
    }

    $cadena = "";

    $simbolos = "@#$%&()!{}[]>+-*,.+;:";          //22 
    $mayusculas = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";    //26
    $minusculas = "abcdefghijklmnopqrstuvwxyz";     //26
    $numeros = "1234567890";
    $todos = $simbolos . $mayusculas . $minusculas . $numeros;

    $cadena .= $simbolos[rand(0, strlen($simbolos) - 1)];
    $cadena .= $mayusculas[rand(0, strlen($mayusculas) - 1)];
    $cadena .= $minusculas[rand(0, strlen($minusculas) - 1)];
    $cadena .= $numeros[rand(0, strlen($numeros) - 1)];

    for ($i=0; $i < $tamaño - 4; $i++) { 
        $cadena .= $todos[rand(0, strlen($todos) - 1)];    
    }
    
    return $cadena;
}
