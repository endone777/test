<?php

namespace App\Interface;

interface ModelInterface
{
    public function create(array $data): bool;

    public function update(array $data, int $id): bool;

    public function delete(int $id): bool;

}