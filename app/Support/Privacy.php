<?php

namespace App\Support;

class Privacy
{
    public static function maskContact(?string $contact): ?string
    {
        $c = trim((string) $contact);
        if ($c === '') {
            return null;
        }
        if (mb_strlen($c) <= 7) {
            return mb_substr($c, 0, 2).'•••';
        }

        return mb_substr($c, 0, 5).'•••'.mb_substr($c, -2);
    }
}
