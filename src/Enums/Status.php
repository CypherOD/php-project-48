<?php

namespace Differ\Enums;

/**
 * Перечисление возможных статусов различий между значениями.
 */
enum Status: string
{
    case ADDED = 'added';
    case REMOVED = 'removed';
    case UNCHANGED = 'unchanged';
    case NESTED = 'nested';
    case UPDATED = 'updated';
}
