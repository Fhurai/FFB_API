/**
 * Initialization variables.
 */
const pourcentageContainer = document.querySelector(".pourcentage");
let values = 0;
let keys = 0;

/**
 * Event load page
 */
document.addEventListener("DOMContentLoaded", function (event) {
    // Stats fandoms.
    fandoms()
        .then((fandoms) => {
            values += fandoms[0];
            keys += fandoms[1];
            drawPourcentage();
        });

    // Stats authors.
    authors()
        .then((authors) => {
            values += authors[0];
            keys += authors[1];
            drawPourcentage();
        });
});

/**
 * Draw pourcentage bar from numbers got from all tests.
 */
function drawPourcentage() {
    const pourcentageBar = document.createElement("div");
    pourcentageBar.classList = "result";
    pourcentageBar.innerHTML = Math.round(values / keys * 100) + "%";
    pourcentageBar.style.width = (values / keys * 90) + "%"
    pourcentageContainer.appendChild(pourcentageBar);
}

/**
 * Gets the fandoms tests numbers.
 * @returns [numbers of successful tests, numbers of total tests]
 */
async function fandoms() {
    const url = "testFandom.php";
    return fetchTests(url);
}

/**
 * Gets the authors tests numbers.
 * @returns [numbers of successful tests, numbers of total tests]
 */
async function authors() {
    const url = "testAuthor.php";
    return fetchTests(url);
}

/**
 * Gets the tests number in a given url.
 * @param {string} url 
 * @returns [numbers of successful tests, numbers of total tests]
 */
async function fetchTests(url) {
    try {
        // fetch call
        const response = await fetch(url, {
            method: "GET",
            headers: {
                "Content-type": "application/json"
            }
        });

        if (!response.ok)
            // Fetch unsuccessfull, throw error.
            throw new Error(`Response status: ${response.status}`);
        else {
            // Fetch successfull, manage json.
            const tests = await response.json();

            // Gets successful and total tests numbers.
            let values = Object.values(tests).filter((result) => result === true).length;
            let keys = Object.keys(tests).length;

            if (values / keys === 1)
                // If all tests are successfull, url is success
                document.querySelector("a[href='" + url + "']").classList = "success";
            else
                // Not all tests are successfull, url is fail.
                document.querySelector("a[href='" + url + "']").classList = "fail";

            // Returns the numbers in an array.
            return [values, keys];
        }
    } catch (error) {
        // Error catched, show error message + returns false.
        console.error(error.message);
        return false;
    }
}