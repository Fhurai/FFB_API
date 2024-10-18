<?php

use entity\Tag;
use pdo\TagTable;
use utils\Functions;

require_once "../entity/Tag.php";
require_once "../pdo/TagTable.php";
require_once "../utils/Functions.php";

// Initialization of table.
$table = new TagTable("test_fanbk");

/**
 * * * * * * * * * * * * * *
 * * * * * Getters * * * * *
 * * * * * * * * * * * * * *
 * A01 - get() with numeric identifier                                                  - yes
 * A02 - get() with string identifier and selected field                                - yes
 * A03 - getAll() with all fields and all entities                                      - yes
 * A04 - getAll() with selected fields in array                                         - yes
 * A05 - getAll() with selected fields in string                                        - yes
 * A06 - getAll() with limits (4,4 begins to 5 & go to 8)                              - yes
 * A07 - getSearch() with name                                                          - yes
 * A08 - getSearch() with date only update_date & limits (0,4 begins to 1 &  go to 14)  - yes
 * A09 - getSearch() with time only update_date                                         - yes
 * A10 - getSearch() with complete update_date with selected fields in array            - yes
 * A11 - getSearch() with null search                                                   - yes
 */
$A01 = $table->get(5);
$A02 = $table->get("2", ["fields" => ["name", "description"]]);
$A03 = $table->getAll();
$A04 = $table->getAll(["fields" => ["id", "name", "description"]]);
$A05 = $table->getAll(["fields" => "id, name, description, update_date"]);
$A06 = $table->getAll(["start" => 4, "count" => 4]);
$A07 = $table->getSearch(["search" => ["description" => "parents"]]);
$A08 = $table->getSearch(["search" => ["update_date" => "2024-10-18"], "start" => 0, "count" => 4]);
$A09 = $table->getSearch(["search" => ["update_date" => "12:57:10"]]);
$A10 = $table->getSearch(["search" => ["update_date" => "2024-10-18 13:05:47"], "fields" => ["id", "name", "description"]]);
$A11 = $table->getSearch(["search" => null]);

/**
 * * * * * * * * * * * * * *
 * * * * Empty Tag * * * * *
 * * * * * * * * * * * * * *
 * B1 - Create  - no
 * B2 - Update  - no
 * B3 - Delete  - no
 * B4 - Restore - no
 * B5 - Remove  - no
 */
$tagB = new Tag([]);
$B1 = $table->create($tagB);
$B2 = $table->update($tagB);
$B3 = $table->delete($tagB);
$B4 = $table->restore($tagB);
$B5 = $table->remove($tagB);


/**
 * * * * * * * * * * * * * * *
 * * * * Active Tag* * * * * *
 * * * * * * * * * * * * * * *
 * C1 - Create  - yes
 * C2 - Update  - yes
 * C3 - Delete  - yes
 * C4 - Restore - yes
 * C5 - Remove  - no
 * C6 - Delete  - yes
 * C7 - Remove  - yes
 */
$tagC = new Tag([
    "name"=> "AU",
    "description" => "Alternative Universe",
    "creation_date" => "2024-10-16 11:30:00",
    "update_date" => "2024-10-16 11:30:00",
]);
$C1 = $table->create($tagC);
$tagC->setId($C1->getId());
$tagC->setName("Alternative Universe");
$tagC->setDescription("The story takes place in a different settings of the original story.");
$C2 = $table->update($tagC, ["dirty" => ["name", "description"]]);
$C3 = $table->delete($tagC);
$C4 = $table->restore($tagC);
$C5 = $table->remove($tagC);
$C6 = $table->delete($tagC);
$C7 = $table->remove($tagC);


/**
 * * * * * * * * * * * * * * *
 * * * * Inactive Tag* * * * *
 * * * * * * * * * * * * * * *
 * D1 - Create  - yes
 * D2 - Update  - no
 * D3 - Delete  - no
 * D4 - Restore - yes
 * D5 - Delete  - yes
 * D6 - Remove  - yes 
 */
$tagD = new Tag([
    "name" => "Crossover",
    "description" => "The story melt universes together.",
    "creation_date" => "2024-10-17 11:00:00",
    "update_date" => "2024-10-17 11:15:00",
    "suppression_date" => "2024-10-17 11:15:00",
]);
$D1 = $table->create($tagD);
$tagD->setId($D1->getId());
$tagD->setName("Cross-over");
$tagD->setDescription("The story takes place in a universe resulting from the fusion of two ore more fandoms.");
$D2 = $table->update($tagD, ["dirty" => ["name", "description"]]);
$D3 = $table->delete($tagD);
$D4 = $table->restore($tagD);
$D5 = $table->delete($tagD);
$D6 = $table->remove($tagD);

Functions::sendJson([
    // Get from DB
    "A01" => $A01->getName() === "Post-canon" && $A01->getDescription() === "Story takes place after the original timeline of the universe.",
    "A02" => $A02["name"] === "Friendship" && $A02["description"] === "Story is about or mentions bit of friendship between two or more characters.",
    "A03" => count($A03) === 12 && is_object($A03[0]),
    "A04" => count($A04) === 12 && is_array($A04[0]) && count($A04[0]) === 3,
    "A05" => count($A05) === 12 && is_array($A05[0]) && count($A05[0]) == 4,
    "A06" => count($A06) === 4 && $A06[0]->getId() === 5 && $A06[3]->getId() === 8,
    "A07" => count($A07) === 2 && is_object($A07[0]),
    "A08" => count($A08) === 4 && is_object($A08[0]),
    "A09" => count($A09) === 4 && is_object($A09[0]) && $A09[0]->getName() === "Pre-canon",
    "A10" => count($A10) === 5 && is_array($A10[0]) && count($A10[0]) === 3 && $A10[0]["name"] === "Hetero / straight",
    "A11" => count($A11) === 12 && is_object($A11[0]),
    // Empty language
    "B1" => $B1 === false,
    "B2" => $B2 === false,
    "B3" => $B3 === false,
    "B4" => $B4 === false,
    "B5" => $B5 === false,
    // Active language
    "C1" => $C1->getId() !== 0 && $C1->getName() === "AU" && $C1->getDescription() === "Alternative Universe" && $C1->getUpdateDate()->format("Y-m-d") === "2024-10-16",
    "C2" => $C2->getId() === $C1->getId() && $C2->getName() === "Alternative Universe" && $C2->getDescription() === "The story takes place in a different settings of the original story." && $C1->getUpdateDate() !== $C2->getUpdateDate(),
    "C3" => $C3 === true,
    "C4" => $C4 === true,
    "C5" => $C5 === false,
    "C6" => $C6 === true,
    "C7" => $C7 === true,
    // Inactive language
    "D1" => $D1->getId() !== 0 && $D1->getName() === "Crossover" && $D1->getCreationDate()->format("Y-m-d") === "2024-10-17" && !is_null($D1->getSuppressionDate()),
    "D2" => $D2 === false,
    "D3" => $D3 === false,
    "D4" => $D4 === true,
    "D5" => $D5 === true,
    "D6" => $D6 === true,
]);