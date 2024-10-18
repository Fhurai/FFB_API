<?php

use entity\Language;
use pdo\LanguageTable;
use utils\Functions;

require_once "../entity/Language.php";
require_once "../pdo/LanguageTable.php";
require_once "../utils/Functions.php";

// Initialization of table.
$table = new LanguageTable("test_fanbk");

/**
 * * * * * * * * * * * * * *
 * * * * * Getters * * * * *
 * * * * * * * * * * * * * *
 * A01 - get() with numeric identifier                                                  - yes
 * A02 - get() with string identifier and selected field                                - yes
 * A03 - getAll() with all fields and all entities                                      - yes
 * A04 - getAll() with selected fields in array                                         - yes
 * A05 - getAll() with selected fields in string                                        - yes
 * A06 - getAll() with limits (4,4 begins to 5 & go to 8)                               - yes
 * A07 - getSearch() with name                                                          - yes
 * A08 - getSearch() with date only update_date & limits (0,5 begins to 1 &  go to 5)  - yes
 * A09 - getSearch() with time only update_date                                         - yes
 * A10 - getSearch() with complete update_date with selected fields in array            - yes
 * A11 - getSearch() with null search                                                   - yes
 */
$A01 = $table->get(5);
$A02 = $table->get("2", ["fields" => ["name", "abbreviation"]]);
$A03 = $table->getAll();
$A04 = $table->getAll(["fields" => ["id", "name", "abbreviation"]]);
$A05 = $table->getAll(["fields" => "id, name, abbreviation, update_date"]);
$A06 = $table->getAll(["start" => 4, "count" => 4]);
$A07 = $table->getSearch(["search" => ["abbreviation" => "CN"]]);
$A08 = $table->getSearch(["search" => ["update_date" => "2024-10-18"], "start" => 0, "count" => 5]);
$A09 = $table->getSearch(["search" => ["update_date" => "10:07:49"]]);
$A10 = $table->getSearch(["search" => ["update_date" => "2024-10-12 12:11:32"], "fields" => ["id", "name", "abbreviation"]]);
$A11 = $table->getSearch(["search" => null]);

/**
 * * * * * * * * * * * * * *
 * * * * Empty Language* * *
 * * * * * * * * * * * * * *
 * B1 - Create  - no
 * B2 - Update  - no
 * B3 - Delete  - no
 * B4 - Restore - no
 * B5 - Remove  - no
 */
$languageB = new Language([]);
$B1 = $table->create($languageB);
$B2 = $table->update($languageB);
$B3 = $table->delete($languageB);
$B4 = $table->restore($languageB);
$B5 = $table->remove($languageB);

/**
 * * * * * * * * * * * * * * *
 * * * * Active Language * * *
 * * * * * * * * * * * * * * *
 * C1 - Create  - yes
 * C2 - Update  - yes
 * C3 - Delete  - yes
 * C4 - Restore - yes
 * C5 - Remove  - no
 * C6 - Delete  - yes
 * C7 - Remove  - yes
 */
$languageC = new Language([
    "name" => "Russian",
    "abbreviation" => "RU",
    "creation_date" => "2024-10-16 11:30:00",
    "update_date" => "2024-10-16 11:30:00",
]);
$C1 = $table->create($languageC);
$languageC->setId($C1->getId());
$languageC->setName("Ukrainian");
$languageC->setAbbreviation("UA");
$C2 = $table->update($languageC, ["dirty" => ["name", "abbreviation"]]);
$C3 = $table->delete($languageC);
$C4 = $table->restore($languageC);
$C5 = $table->remove($languageC);
$C6 = $table->delete($languageC);
$C7 = $table->remove($languageC);


/**
 * * * * * * * * * * * * * * *
 * * * * Inactive Language * * 
 * * * * * * * * * * * * * * *
 * D1 - Create  - yes
 * D2 - Update  - no
 * D3 - Delete  - no
 * D4 - Restore - yes
 * D5 - Delete  - yes
 * D6 - Remove  - yes 
 */
$languageD = new Language([
    "name" => "Vulcain",
    "abbreviation" => "VC",
    "creation_date" => "2024-10-17 11:00:00",
    "update_date" => "2024-10-17 11:15:00",
    "suppression_date" => "2024-10-17 11:15:00",
]);
$D1 = $table->create($languageD);
$languageD->setId($D1->getId());
$languageD->setName("Weekend");
$languageD->setAbbreviation("WK");
$D2 = $table->update($languageD, ["dirty" => ["name", "abbreviation"]]);
$D3 = $table->delete($languageD);
$D4 = $table->restore($languageD);
$D5 = $table->delete($languageD);
$D6 = $table->remove($languageD);

Functions::sendJson([
    // Get from DB
    "A01" => $A01->getName() === "日本語" && $A01->getAbbreviation() === "JP",
    "A02" => $A02["name"] === "Español" && $A02["abbreviation"] === "ES",
    "A03" => count($A03) === 8 && is_object($A03[0]),
    "A04" => count($A04) === 8 && is_array($A04[0]) && count($A04[0]) === 3,
    "A05" => count($A05) === 8 && is_array($A05[0]) && count($A05[0]) == 4,
    "A06" => count($A06) === 4 && $A06[0]->getId() === 5 && $A06[3]->getId() === 8,
    "A07" => count($A07) === 1 && is_object($A07[0]),
    "A08" => count($A08) === 5 && is_object($A08[0]),
    "A09" => count($A09) === 4 && is_object($A09[0]) && $A09[0]->getAbbreviation() === "JP",
    "A10" => count($A10) === 1 && is_array($A10[0]) && count($A10[0]) === 3 && $A10[0]["abbreviation"] === "EN",
    "A11" => count($A11) === 8 && is_object($A11[0]),
    // Empty language
    "B1" => $B1 === false,
    "B2" => $B2 === false,
    "B3" => $B3 === false,
    "B4" => $B4 === false,
    "B5" => $B5 === false,
    // Active language
    "C1" => $C1->getId() !== 0 && $C1->getName() === "Russian" && $C1->getAbbreviation() === "RU" && $C1->getUpdateDate()->format("Y-m-d") === "2024-10-16",
    "C2" => $C2->getId() === $C1->getId() && $C2->getName() === "Ukrainian" && $C2->getAbbreviation() === "UA" && $C1->getUpdateDate() !== $C2->getUpdateDate(),
    "C3" => $C3 === true,
    "C4" => $C4 === true,
    "C5" => $C5 === false,
    "C6" => $C6 === true,
    "C7" => $C7 === true,
    // Inactive language
    "D1" => $D1->getId() !== 0 && $D1->getName() === "Vulcain" && $D1->getCreationDate()->format("Y-m-d") === "2024-10-17" && !is_null($D1->getSuppressionDate()),
    "D2" => $D2 === false,
    "D3" => $D3 === false,
    "D4" => $D4 === true,
    "D5" => $D5 === true,
    "D6" => $D6 === true,
]);