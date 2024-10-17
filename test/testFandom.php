<?php

use entity\Fandom;
use pdo\FandomTable;
use utils\Functions;

require_once "../entity/Fandom.php";
require_once "../pdo/FandomTable.php";
require_once "../utils/Functions.php";

// Initialization of table.
$table = new FandomTable("test_fanbk");

/**
 * * * * * * * * * * * * * *
 * * * * * Getters * * * * *
 * * * * * * * * * * * * * *
 * A01 - get() with numeric identifier                                                  - yes
 * A02 - get() with string identifier and selected field                                - yes
 * A03 - getAll() with all fields and all entities                                      - yes
 * A04 - getAll() with selected fields in array                                         - yes
 * A05 - getAll() with selected fields in string                                        - yes
 * A06 - getAll() with limits (5,5 begins to 6 & go to 10)                              - yes
 * A07 - getSearch() with name                                                          - yes
 * A08 - getSearch() with date only update_date & limits (0,15 begins to 1 &  go to 14) - yes
 * A09 - getSearch() with time only update_date                                         - yes
 * A10 - getSearch() with complete update_date with selected fields in array            - yes
 * A11 - getSearch() with null search                                                   - yes
 */
$A01 = $table->get(8);
$A02 = $table->get("7", ["fields" => "name"]);
$A03 = $table->getAll();
$A04 = $table->getAll(["fields" => ["id", "name"]]);
$A05 = $table->getAll(["fields" => "id, name, update_date"]);
$A06 = $table->getAll(["start" => 5, "count" => 5]);
$A07 = $table->getSearch(["search" => ["name" => "Naruto"]]);
$A08 = $table->getSearch(["search" => ["update_date" => "2024-01-26"], "start" => 0, "count" => 15]);
$A09 = $table->getSearch(["search" => ["update_date" => "17:43:18"]]);
$A10 = $table->getSearch(["search" => ["update_date" => "2024-01-26 17:43:10"], "fields" => ["id", "name"]]);
$A11 = $table->getSearch(["search" => null]);

/**
 * * * * * * * * * * * * * *
 * * * * Empty Fandom* * * * 
 * * * * * * * * * * * * * *
 * B1 - Create  - no
 * B2 - Update  - no
 * B3 - Delete  - no
 * B4 - Restore - no
 * B5 - Remove  - no
 */
$fandomB = new Fandom([]);
$B1 = $table->create($fandomB);
$B2 = $table->update($fandomB);
$B3 = $table->delete($fandomB);
$B4 = $table->restore($fandomB);
$B5 = $table->remove($fandomB);
/**
 * * * * * * * * * * * * * * *
 * * * * Active Fandom * * * *
 * * * * * * * * * * * * * * *
 * C1 - Create  - yes
 * C2 - Update  - yes
 * C3 - Delete  - yes
 * C4 - Restore - yes
 * C5 - Remove  - no
 * C6 - Delete  - yes
 * C7 - Remove  - yes
 */
$fandomC = new Fandom([
    "name" => "One Piece",
    "creation_date" => "2024-10-16 11:30:00",
    "update_date" => "2024-10-16 11:30:00",
]);
$C1 = $table->create($fandomC);
$fandomC->setId($C1->getId());
$fandomC->setName("One Piece & Co");
$C2 = $table->update($fandomC, ["dirty" => ["name"]]);
$C3 = $table->delete($fandomC);
$C4 = $table->restore($fandomC);
$C5 = $table->remove($fandomC);
$C6 = $table->delete($fandomC);
$C7 = $table->remove($fandomC);
/**
 * * * * * * * * * * * * * * *
 * * * * Inactive Fandom * * * 
 * * * * * * * * * * * * * * *
 * D1 - Create  - yes
 * D2 - Update  - no
 * D3 - Delete  - no
 * D4 - Restore - yes
 * D5 - Delete  - yes
 * D6 - Remove  - yes 
 */
$fandomD = new Fandom([
    "name" => "Dragon Ball",
    "creation_date" => "2024-10-17 11:00:00",
    "update_date" => "2024-10-17 11:15:00",
    "suppression_date" => "2024-10-17 11:15:00",
]);
$D1 = $table->create($fandomD);
$fandomD->setId($D1->getId());
$fandomD->setName("Dragon Ball / Z / GT / Super");
$D2 = $table->update($fandomD, ["dirty" => ["name"]]);
$D3 = $table->delete($fandomD);
$D4 = $table->restore($fandomD);
$D5 = $table->delete($fandomD);
$D6 = $table->remove($fandomD);

$results = [
    // // Get from DB
    "A01" => $A01->getName() === "Naruto",
    "A02" => $A02["name"] === "Nana to Kaoru | ナナとカオル",
    "A03" => count($A03) === 15 && is_object($A03[0]),
    "A04" => count($A04) === 15 && is_array($A04[0]) && count($A04[0]) == 2,
    "A05" => count($A05) === 15 && is_array($A05[0]) && count($A05[0]) == 3,
    "A06" => count($A06) === 5 && $A06[0]->getId() === 6 && $A06[4]->getId() === 10,
    "A07" => count($A07) === 1 && is_object($A07[0]),
    "A08" => count($A08) === 14 && is_object($A08[0]),
    "A09" => count($A09) === 1 && is_object($A09[0]) && $A09[0]->getName() === "Xenoblade Chronicles 3",
    "A10" => count($A10) === 1 && is_array($A10[0]) && count($A10[0]) == 2 && $A10[0]["name"] === "Xenoblade Chronicles 2",
    "A11" => count($A11) === 15 && is_object($A11[0]),
    // // Empty fandom
    "B1" => $B1 === false,
    "B2" => $B2 === false,
    "B3" => $B3 === false,
    "B4" => $B4 === false,
    "B5" => $B5 === false,
    // Active fandom
    "C1" => $C1->getId() !== 0 && $C1->getName() === "One Piece" && $C1->getUpdateDate()->format("Y-m-d") === "2024-10-16",
    "C2" => $C2->getId() === $C1->getId() && $C2->getName() === "One Piece & Co" && $C1->getUpdateDate() !== $C2->getUpdateDate(),
    "C3" => $C3 === true,
    "C4" => $C4 === true,
    "C5" => $C5 === false,
    "C6" => $C6 === true,
    "C7" => $C7 === true,
    // Inactive fandom
    "D1" => $D1->getId() !== 0 && $D1->getName() === "Dragon Ball" && $D1->getCreationDate()->format("Y-m-d") === "2024-10-17" && !is_null($D1->getSuppressionDate()),
    "D2" => $D2 === false,
    "D3" => $D3 === false,
    "D4" => $D4 === true,
    "D5" => $D5 === true,
    "D6" => $D6 === true,
];

Functions::sendJson($results);