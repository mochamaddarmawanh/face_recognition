$('#train_class').load('models/train_class.php');

function add_new_class() {
    $.ajax({
        url: 'models/train_class.php',
        type: 'POST',
        dataType: 'html',
        success: function (response) {
            $('#train_class').append(response);
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
        }
    });
}