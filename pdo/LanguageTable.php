<?php

namespace pdo;

require_once "../entity/Language.php";
require_once "../pdo/BasicTable.php";

use entity\Language;
use pdo\BasicTable;

final class LanguageTable extends BasicTable
{
    protected function getTable(): string
    {
        return "langages";
    }

    public function getFields(): array
    {
        return [
            "name",
            "abbreviation",
            "creation_date",
            "update_date",
            "suppression_date"
        ];
    }

    protected function dataObjectify(array $data): array
    {
        return array_map(function ($data) {
            return new Language($data);
        }, $data);
    }
}