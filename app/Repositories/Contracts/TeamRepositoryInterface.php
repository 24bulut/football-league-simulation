<?php

namespace App\Repositories\Contracts;

use App\Models\Team;
use Illuminate\Support\Collection;

interface TeamRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?Team;

    public function count(): int;

    public function getIds(): array;
}

