<?php

use entity\Author;
use pdo\AuthorTable;
use utils\Functions;

require_once "../entity/Author.php";
require_once "../pdo/AuthorTable.php";
require_once "../utils/Functions.php";

// Initialization of table.
$table = new AuthorTable("test_fanbk");

/**
 * * * * * * * * * * * * * *
 * * * * * Getters * * * * *
 * * * * * * * * * * * * * *
 * A01 - get() with numeric identifier                                                  - yes
 * A02 - get() with string identifier and selected field                                - yes
 * A03 - getAll() with all fields and all entities                                      - yes
 * A04 - getAll() with selected fields in array                                         - yes
 * A05 - getAll() with selected fields in string                                        - yes
 * A06 - getAll() with limits (2,2 begins to 3 & go to 4)                               - yes
 * A07 - getSearch() with name                                                          - yes
 * A08 - getSearch() with date only update_date & limits (0,5 begins to 1 &  go to 4)   - yes
 * A09 - getSearch() with time only update_date                                         - yes
 * A10 - getSearch() with complete update_date with selected fields in array            - yes
 * A11 - getSearch() with null search                                                   - yes
 */
$A01 = $table->get(1);
$A02 = $table->get("2", ["fields" => ["name"]]);
$A03 = $table->getAll();
$A04 = $table->getAll(["fields" => ["id", "name"]]);
$A05 = $table->getAll(["fields" => "id, name, update_date"]);
$A06 = $table->getAll(["start" => 2, "count" => 2]);
$A07 = $table->getSearch(["search" => ["name" => "Jayf"]]);
$A08 = $table->getSearch(["search" => ["update_date" => "2024-10-17"], "start" => 0, "count" => 5]);
$A09 = $table->getSearch(["search" => ["update_date" => "21:43:17"]]);
$A10 = $table->getSearch(["search" => ["update_date" => "2024-10-17 21:42:06"], "fields" => ["id", "name"]]);
$A11 = $table->getSearch(["search" => null]);

/**
 * * * * * * * * * * * * * *
 * * * * Empty Author* * * * 
 * * * * * * * * * * * * * *
 * B1 - Create  - no
 * B2 - Update  - no
 * B3 - Delete  - no
 * B4 - Restore - no
 * B5 - Remove  - no
 */
$authorB = new Author([]);
$B1 = $table->create($authorB);
$B2 = $table->update($authorB);
$B3 = $table->delete($authorB);
$B4 = $table->restore($authorB);
$B5 = $table->remove($authorB);

/**
 * * * * * * * * * * * * * * *
 * * * * Active Author * * * *
 * * * * * * * * * * * * * * *
 * C1 - Create  - yes
 * C2 - Update  - yes
 * C3 - Delete  - yes
 * C4 - Restore - yes
 * C5 - Remove  - no
 * C6 - Delete  - yes
 * C7 - Remove  - yes
 */
$authorC = new Author([
    "name" => "Somebody's Nightmare",
    "creation_date" => "2024-10-16 11:30:00",
    "update_date" => "2024-10-16 11:30:00",
]);
$C1 = $table->create($authorC);
$authorC->setId($C1->getId());
$authorC->setName("Somebodys Nightmare");
$C2 = $table->update($authorC, ["dirty" => ["name"]]);
$C3 = $table->delete($authorC);
$C4 = $table->restore($authorC);
$C5 = $table->remove($authorC);
$C6 = $table->delete($authorC);
$C7 = $table->remove($authorC);

/**
 * * * * * * * * * * * * * * *
 * * * * Inactive Author * * * 
 * * * * * * * * * * * * * * *
 * D1 - Create  - yes
 * D2 - Update  - no
 * D3 - Delete  - no
 * D4 - Restore - yes
 * D5 - Delete  - yes
 * D6 - Remove  - yes 
 */
$authorD = new Author([
    "name" => "Me413",
    "creation_date" => "2024-10-17 11:00:00",
    "update_date" => "2024-10-17 11:15:00",
    "suppression_date" => "2024-10-17 11:15:00",
]);
$D1 = $table->create($authorD);
$authorD->setId($D1->getId());
$authorD->setName("Me 413");
$D2 = $table->update($authorD, ["dirty" => ["name"]]);
$D3 = $table->delete($authorD);
$D4 = $table->restore($authorD);
$D5 = $table->delete($authorD);
$D6 = $table->remove($authorD);

Functions::sendJson([
    // Get from DB
    "A01" => $A01->getName() === "Senigata",
    "A02" => $A02["name"] === "Jayf",
    "A03" => count($A03) === 5 && is_object($A03[0]),
    "A04" => count($A04) === 5 && is_array($A04[0]) && count($A04[0]) == 2,
    "A05" => count($A05) === 5 && is_array($A05[0]) && count($A05[0]) == 3,
    "A06" => count($A06) === 2 && $A06[0]->getId() === 3 && $A06[1]->getId() === 4,
    "A07" => count($A07) === 1 && is_object($A07[0]),
    "A08" => count($A08) === 4 && is_object($A08[0]),
    "A09" => count($A09) === 1 && is_object($A09[0]) && $A09[0]->getName() === "Cj Spencer",
    "A10" => count($A10) === 3 && is_array($A10[0]) && count($A10[0]) == 2 && $A10[0]["name"] === "Jayf",
    "A11" => count($A11) === 5 && is_object($A11[0]),
    // Empty author
    "B1" => $B1 === false,
    "B2" => $B2 === false,
    "B3" => $B3 === false,
    "B4" => $B4 === false,
    "B5" => $B5 === false,
    // Active author
    "C1" => $C1->getId() !== 0 && $C1->getName() === "Somebody's Nightmare" && $C1->getUpdateDate()->format("Y-m-d") === "2024-10-16",
    "C2" => $C2->getId() === $C1->getId() && $C2->getName() === "Somebodys Nightmare" && $C1->getUpdateDate() !== $C2->getUpdateDate(),
    "C3" => $C3 === true,
    "C4" => $C4 === true,
    "C5" => $C5 === false,
    "C6" => $C6 === true,
    "C7" => $C7 === true,
    // Inactive author
    "D1" => $D1->getId() !== 0 && $D1->getName() === "Me413" && $D1->getCreationDate()->format("Y-m-d") === "2024-10-17" && !is_null($D1->getSuppressionDate()),
    "D2" => $D2 === false,
    "D3" => $D3 === false,
    "D4" => $D4 === true,
    "D5" => $D5 === true,
    "D6" => $D6 === true,
]);