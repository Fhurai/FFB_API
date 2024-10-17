<?php

namespace pdo;

require_once "../entity/Author.php";
require_once "../pdo/BasicTable.php";

use entity\Author;
use pdo\BasicTable;

final class AuthorTable extends BasicTable
{
    protected function getTable(): string
    {
        return "auteurs";
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
            return new Author($data);
        }, $data);
    }
}