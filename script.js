document.addEventListener("DOMContentLoaded", function () {
    const checkAvailabilityButton = document.getElementById("checkAvailability");
    const reserveButton = document.getElementById("reserveButton");
    const messageBox = document.getElementById("messageBox");
    
    checkAvailabilityButton.addEventListener("click", function () {
        const startDate = document.getElementById("startDate").value;
        const endDate = document.getElementById("endDate").value;
        
        if (!startDate || !endDate) {
            messageBox.innerText = "Моля, изберете начална и крайна дата.";
            messageBox.style.color = "red";
            return;
        }

        const available = checkAvailability(startDate, endDate);
        
        if (available) {
            messageBox.innerText = "Има свободни места! Можете да резервирате.";
            messageBox.style.color = "green";
            reserveButton.style.display = "block";
        } else {
            messageBox.innerText = "Няма свободни места за резервации в избрания период. Искате ли да изберете друг?";
            messageBox.style.color = "red";
            reserveButton.style.display = "none";
        }
    });

    reserveButton.addEventListener("click", function () {
        alert("Вашата резервация е направена успешно!");
        // Тук можете да добавите логика за изпращане на данните към сървър
    });
});

function checkAvailability(start, end) {
    // Тук ще бъде логиката за проверка на наличността
    // Това е примерна симулация – може да замените с реални данни от база
    const unavailableDates = ["2025-02-20", "2025-02-21", "2025-02-22"];
    const startDate = new Date(start);
    const endDate = new Date(end);
    
    for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
        if (unavailableDates.includes(d.toISOString().split("T")[0])) {
            return false;
        }
    }
    return true;
}
