<?php

namespace pdo;

require_once "../entity/Fandom.php";
require_once "../pdo/BasicTable.php";

use entity\Fandom;
use pdo\BasicTable;

final class FandomTable extends BasicTable
{
    protected function getTable(): string
    {
        return "fandoms";
    }

    public function getFields(): array
    {
        return [
            "name",
            "creation_date",
            "update_date",
            "suppression_date"
        ];
    }

    protected function dataObjectify(array $data): array
    {
        return array_map(function ($data) {
            return new Fandom($data);
        }, $data);
    }
}
