var templatesRead = {};


function updateUsersTable() {
    $('.ajaxtable').each(function () {
        var $this = $(this);
        if ($this.data('datatype') == 'users') {
            $.ajax({
                url: baseAppPath + '/api/users/list',
                type: 'GET',
                success: function (result) {
                    var tableBody = $this.find('tbody');
                    tableBody.empty();
                    for (var i = 0; i < result.length; i++) {
                        var item = result[i];

                        tableBody.append('<tr><th scope="row">' + item.userName + '</th><td>' + item.fullName + '</td><td><a href="#" class="modaledit" data-id="' + item.id + '"\n\
 data-toggle="modal" data-target="#formModal" data-tpl="editUser">Edit</a> &nbsp; <a href="#" class="deleteuser" data-id="' + item.id + '">Delete</a></td></tr>');
                    }

                }
            });
        }
    });
}

$('body').on('click', '.deleteuser', function () {
    var id = $(this).data('id');
    $.ajax({
        url: baseAppPath + '/api/users/' + id,
        type: 'DELETE',
        success: function (result) {
            updateUsersTable();
        }
    });
});

var templatesRead = {};

function updateUserEditDialog(id) {
    $.ajax({
        url: baseAppPath + '/api/users/' + id,
        type: 'GET',
        success: function (result) {
            $('#useredit_username').val(result.userName);
            $('#useredit_fullname').val(result.fullName);
            $('#useredit_id').val(result.id);

            //    updateUsersTable();
        }
    });
}


function readTemplate(tag, id, tplPath, updatefunc) {
    $.ajax({
        url: baseAppPath + tplPath,
        type: 'GET',
        success: function (result) {
            $('#formModalContent').empty();
            $('#formModalContent').html(result);
            $('#formModalContent').find('form').each(function () {
                var url = baseAppPath + $(this).data('actionurl');
                if (typeof id !== 'undefined' && id !== null && id !== '') {
                    url = url + '/'+id;
                }
                $(this).data('action', url);
            });
            updatefunc(id);
        },
        error: function (error) {
            $('#formModal').modal('hide');
        }
    });
}


$('body').on('click', '.formsubmit', function () {
    var formId = $(this).data('form');
    var form = $('#' + formId);
    var method = form.data('method');
    var action = form.data('action');
    var data = {};
    const array = form.serializeArray(); // Encodes the set of form elements as an array of names and values.
    $.each(array, function () {
        data[this.name] = this.value || "";
    });
    console.log(action);
    
    $.ajax({
        url: action,
        type: method,
        data: JSON.stringify(data),
        dataType: 'json',
        contentType: 'application/json',
        success: function (result) {
            $('#formModal').modal('hide');
            updateUsersTable();
        },
        error: function (result) {
            $('#formModal').modal('hide');
            updateUsersTable();
        }
    });
}
);

var tplUrls = {};

tplUrls['editUser'] = {
    url: '/admin/tpl/edituser',
    updatefunc: updateUserEditDialog
};

tplUrls['createUser'] = {
    url: '/admin/tpl/createuser',
    updatefunc: function () {

    }
};

$('body').on('click', '.modaledit', function () {
    var id = $(this).data('id');
    var tpl = $(this).data('tpl');
    console.log(tpl);
    if (typeof templatesRead[tpl] === 'undefined') {
        readTemplate(tpl, id, tplUrls[tpl].url, tplUrls[tpl].updatefunc);
    } else {
        $('#formModalContent').html(templatesRead[tpl]);
        tplUrls[tpl].updatefunc(id);
    }
});



