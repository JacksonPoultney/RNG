document.addEventListener("DOMContentLoaded", function() {
    const displayElement = document.getElementById("emailDisplay");

    function startPicker(emails) {
        let interval = setInterval(() => {
            let randomEntry = emails[Math.floor(Math.random() * emails.length)];

            displayElement.innerHTML = '<span>' + randomEntry.email + '</span>' + randomEntry.name;
        }, 50);

        setTimeout(() => {
            clearInterval(interval);
        }, 4000);
    }

    document.getElementById("startPickerButton").addEventListener("click", function() {
        fetch(picker.ajax_url + "?action=serve_email_list")
            .then(response => response.json())
            .then(emails => {
                startPicker(emails);
            });
    });
});

document.getElementById("startPickerButton").addEventListener("click", function() {
    fetch(picker.ajax_url + "?action=serve_email_list")
        .then(response => response.json())
        .then(data => {
            console.log("Selected 50 names and emails:");
            data.forEach(entry => {
                console.log(entry.name + " - " + entry.email);
            });
        })
        .catch(error => console.error("Error fetching data: ", error));
});
