$('#login').submit(function (e) { 
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: "./controller/login.php",
        data: $('#login').serialize(),
        dataType: "json",
        success: function (response) {
            if(response.success == 1){
                alert(response.message)
                location.href = '/sample1/welcome.php';
            }else{
                alert(response.message)
            }
        }
    });
});

$('#signup').submit(function (e) { 
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: "./controller/register.php",
        data: $('#signup').serialize(),
        dataType: "json",
        success: function (response) {
            if(response.success == 1){
                alert(response.message)
                location.href = '/sample1/index.php';
            }else{
                alert(response.message)
            }
        }
    });
});