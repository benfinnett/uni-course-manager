function checkForm(formid, buttonid) {
    var allowed_chars = /^[A-Za-z \d\-,\/:&.]+$/;
    var form = document.getElementById(formid).value;
    // If the search box is empty or contains disallowed characters
    if (form === "" || !form.match(allowed_chars)) { 
        // Disable search button
        document.getElementById(buttonid).disabled = true;
        // Display error message if the box isn't empty
        if (form !== "") {
            var error = document.getElementById("error");
            error.textContent = "Search query must only contain letters, digits and spaces!";
            error.style.color = "red";
        }
    } else {
        // When the box has text made up of allowed chars, enable search button and hide error message
        document.getElementById(buttonid).disabled = false;
        var error = document.getElementById("error");
        error.textContent = "";
    }
}