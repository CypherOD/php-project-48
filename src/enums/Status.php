<?php

namespace Differ\enums;

/**
 * Перечисление возможных статусов различий между значениями.
 */
enum Status: string
{
    case ADDED = 'added';
    case REMOVE = 'remove';
    case UNCHANGED = 'unchanged';
    case NESTED = 'nested';
    case UPDATED = 'updated';
}
