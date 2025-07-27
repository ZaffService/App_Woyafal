<?php
namespace App\Core\Abstract;

abstract class AbstractEntity
{
    abstract public static function toObject(array $data): static;
    abstract public function toArray(): array;
}
