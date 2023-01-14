$('#login').submit(function (e) { 
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: "./controller/login.php",
        data: $('#login').serialize(),
        dataType: "json",
        success: function (response) {
            console.log(response);
        }
    });
});