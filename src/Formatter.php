<?php

/**
 * Преобразует значение в строку с поддержкой всех типов данных.
 *
 * @param mixed $value Любое значение для преобразования
 * @return string Строковое представление значения
 */

function strValue(mixed $value): string
{
    return match (true) {
        is_bool($value) => $value ? 'true' : 'false',
        is_null($value) => 'null',
        default => (string)$value,
    };
}
