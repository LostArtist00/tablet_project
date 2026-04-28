<?php

declare(strict_types=1);

function dd(mixed $data): void
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    exit;
}

function dump(mixed $data): void
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}