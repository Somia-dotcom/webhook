function fetchFarmers(category) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "pages/fetch_farmers.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById("farmer-list").innerHTML = xhr.responseText;
        }
    };
    xhr.send("category=" + category);
}
