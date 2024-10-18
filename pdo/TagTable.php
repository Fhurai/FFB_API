<?php

namespace pdo;

require_once "../entity/Tag.php";
require_once "../pdo/BasicTable.php";

use entity\Tag;
use pdo\BasicTable;

final class TagTable extends BasicTable
{
    protected function getTable(): string
    {
        return "tags";
    }

    public function getFields(): array
    {
        return [
            "name",
            "description",
            "creation_date",
            "update_date",
            "suppression_date"
        ];
    }

    protected function dataObjectify(array $data): array
    {
        return array_map(function ($data) {
            return new Tag($data);
        }, $data);
    }
}