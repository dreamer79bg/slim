var templatesRead = {};


function updateDataTable() {
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
        
        if ($this.data('datatype') == 'posts') {
            $.ajax({
                url: baseAppPath + '/api/posts/list',
                type: 'GET',
                success: function (result) {
                    var tableBody = $this.find('tbody');
                    tableBody.empty();
                    for (var i = 0; i < result.length; i++) {
                        var item = result[i];

                        tableBody.append('<tr><th scope="row">' + item.title + '</th><td>' + item.shortDesc + '</td>'+
                                '<td>' + item.createdAt + '</td>'+
                                '<td>' + item.userFullName + '</td>'+
                                '<td><a href="#" class="modaledit" data-id="' + item.id + '"\n\
 data-toggle="modal" data-target="#formModal" data-tpl="editPost">Edit</a> &nbsp; <a href="#" class="deletepost" data-id="' + item.id + '">Delete</a></td></tr>');
                    }

                }
            });
        }
    });
}



var templatesRead = {};

function updateUserEditDialog(id) {
    $.ajax({
        url: baseAppPath + '/api/users/' + id,
        type: 'GET',
        success: function (result) {
            $('#useredit_username').val(result.userName);
            $('#useredit_fullname').val(result.fullName);
            $('#useredit_id').val(result.id);

            //    updateDataTable();
        }
    });
}

function updatePostEditDialog(id) {
    $.ajax({
        url: baseAppPath + '/api/posts/' + id,
        type: 'GET',
        success: function (result) {
            $('#postedit_title').val(result.title);
            $('#postedit_shortdesc').val(result.shortDesc);
            $('#postedit_content').val(result.content);
            $('#uploadImage').attr('src',baseAppPath+'/images/'+result.imageFile);
            $('#postedit_id').val(result.id);
            $('#uploadCanvasInput').val(result.imageFile);
            $('#postedit_featuredPos').val(result.featuredPos);

            tinymce.remove();
            tinymce.init({
                selector: 'textarea.tinymce'
            });
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
                    url = url + '/' + id;
                }
                $(this).data('action', url);
            });
            updatefunc(id);
            tinymce.remove();
            tinymce.init({
                selector: 'textarea.tinymce'
            });
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
    form.find('.tinymce').each(function(){
       data[this.name]= tinymce.get($(this).attr('id')).getContent();
    });

    $.ajax({
        url: action,
        type: method,
        data: JSON.stringify(data),
        dataType: 'json',
        contentType: 'application/json',
        success: function (result) {
            $('#formModal').modal('hide');
            updateDataTable();
        },
        error: function (result) {
            $('#formModal').modal('hide');
            updateDataTable();
        }
    });
}
);

function loadImage() {
    var input, file, fr, img;

    if (typeof window.FileReader !== 'function') {
        write("The file API isn't supported on this browser yet.");
        return;
    }

    input = document.getElementById('imgfileUpload');
    if (!input) {
        write("Um, couldn't find the imgfile element.");
    } else if (!input.files) {
        write("This browser doesn't seem to support the `files` property of file inputs.");
    } else if (!input.files[0]) {
        write("Please select a file before clicking 'Load'");
    } else {
        file = input.files[0];
        fr = new FileReader();
        fr.onload = createImage;
        fr.readAsDataURL(file);
    }

    function createImage() {
        img=document.getElementById("uploadImage");
        img.src = fr.result;
        document.getElementById("uploadCanvasInput").value= fr.result;
        
    }


    function write(msg) {
        var p = document.createElement('p');
        p.innerHTML = msg;
        document.body.appendChild(p);
    }
}

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

tplUrls['editPost'] = {
    url: '/admin/tpl/editpost',
    updatefunc: updatePostEditDialog
};

tplUrls['createPost'] = {
    url: '/admin/tpl/createpost',
    updatefunc: function () {

    }
};


$('body').on('click', '.modaledit', function () {
    var id = $(this).data('id');
    var tpl = $(this).data('tpl');
    if (typeof templatesRead[tpl] === 'undefined') {
        readTemplate(tpl, id, tplUrls[tpl].url, tplUrls[tpl].updatefunc);
        tinymce.remove();
        tinymce.init({
            selector: 'textarea.tinymce'
        });
    } else {
        $('#formModalContent').html(templatesRead[tpl]);
        tplUrls[tpl].updatefunc(id);
        tinymce.remove();
        tinymce.init({
            selector: 'textarea.tinymce'
        });
    }
});


$('body').on('click', '.deleteuser', function () {
    var id = $(this).data('id');
    $.ajax({
        url: baseAppPath + '/api/users/' + id,
        type: 'DELETE',
        success: function (result) {
            updateDataTable();
        }
    });
});

$('body').on('click', '.deletepost', function () {
    var id = $(this).data('id');
    $.ajax({
        url: baseAppPath + '/api/posts/' + id,
        type: 'DELETE',
        success: function (result) {
            updateDataTable();
        }
    });
});
