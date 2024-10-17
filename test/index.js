const pourcentageContainer = document.querySelector(".pourcentage");

document.addEventListener("DOMContentLoaded", function (event) {
    fandoms()
        .then((fandoms) => {
            pourcentageContainer.values = fandoms[0];
            pourcentageContainer.keys = fandoms[1];
            drawPourcentage();
        });
});

function drawPourcentage(){
    const pourcentageBar = document.createElement("div");
    pourcentageBar.classList = "result";
    pourcentageBar.innerHTML = (pourcentageContainer.values / pourcentageContainer.keys * 100) + "%";
    pourcentageBar.style.width = (pourcentageContainer.values / pourcentageContainer.keys * 90) + "%"
    pourcentageContainer.appendChild(pourcentageBar);
}

async function fandoms() {
    try {
        const response = await fetch("testFandom.php", {
            method: "GET",
            headers: {
                "Content-type": "application/json"
            }
        });

        if (!response.ok)
            throw new Error(`Response status: ${response.status}`);
        else {
            const tests = await response.json();
            if(Object.values(tests).length / Object.keys(tests).length === 1)
                document.querySelector("a[href='testFandom.php']").classList = "success";
            return [Object.values(tests).length, Object.keys(tests).length];
        }
    } catch (error) {
        console.log(error.message);
        return false;
    }
}