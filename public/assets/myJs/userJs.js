$(document).ready(function () {
    // setTimeout(function () {
    //     $('.alert-danger').hide()
    // }, 10000);
    load(function () {
    });
});
$(".addUser").click(function (event) {
    var name = $('#name').val().trim();
    var displayName = $('#displayName').val().trim();
    var email = $('#email').val();
    var pass = $('#pass').val();

    var repeatPass = $('#repeat-pass').val();
    if (name == '' || displayName == '' || email == '' || pass == '' || pass !== repeatPass) {
        if (name == '') {
            $('#error_name').text('name is invalid ');
        }
        if (displayName == '') {
            $('#error_dName').text('display name is invalid ');
        }
        if (email == '') {
            $('#error_email').text('email is invalid ');
        }
        if (pass =='') {
            $('#error_pass').text('password not invalid ');
        }
        if (pass !== repeatPass) {
            $('#error_repeatPass').text('password not equal ');
        }

        return;
    }

    $.ajax({
        type: "post",
        url: "/setUser",
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'name': name,
            'displayName': displayName,
            'email': email,
            'pass': pass,
        },
        success: function (data) {
            $('#addUser').modal('hide');
            load(function () {
                toast.fire({
                    icon: "success",
                    title: "add term successfully",
                    background: toastBgSuccess,
                });
            })
        },
        error: function (data) {
            alert("Error" + data)
        }
    });
});



function load(callback) {
    var filter = '';
    filter = getUrlParameter('filter');
    if (filter != undefined && filter != '' ) {
        $('#removeFilter').removeClass('btn-light').addClass('btn-danger');
    }
    var index = 1;
    $.ajax({
        url: 'getUsers',
        type: "get",
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'filter': filter
        },
        success: function (data) {

            $('#user_data').empty();
            var html = "";
            var userDta = data.users;

            $.each(userDta, function (i, user) {
                html =
                    `
                         <tr>
                             <td>${index++}</td>
                             <td>${user.user_login}</td>
                             <td>${user.display_name}</td>
                             <td>${user.user_email}</td>
                              <td class="text-center">
                               <button data-toggle="modal" data-target="#editUser" class="btn text-primary mx-1" data-id="${user.ID}"  data-dName="${user.display_name}" data-name="${user.user_login}" data-email="${user.user_email}" onclick="editUser(this)"  ><i class="fa fa-edit"></i></button>
                               <button class="btn text-danger" onclick="deleteUser(${user.ID})"  ><i class="fa fa-trash"></i></button>
                               <button data-toggle="modal" data-target="#changePass" class="btn text-warning" onclick="setIdPass(${user.ID})"  ><i class="fa fa-key"></i></button>
                              </td>
                          </tr>
					  `;
                $('#user_data').append(html);
            });

        }, complete: function () {
            callback();
        },
        error: function (data) {
            console.log(data);
        }
    });
}

function setIdPass(id){
    $('#changePassSubmit').attr('data-id', id);
}
function deleteUser(id) {
    Swal.fire({
        title: 'are you sure delete item?',
        html: '<small>Are you sure want to delete item !?</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        cancelButtonText: 'cancel',
        confirmButtonText: 'delete!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "deleteUser/" + id,
                type: "delete",
                data: {'_token': $('meta[name="csrf-token"]').attr('content')},
                dataType: "json",
                success: function (response) {
                    if (response) {
                        load(function () {
                            toast.fire({
                                icon: "success",
                                title: "deleted successfully",
                                background: toastBgSuccess,

                            });
                        });

                    } else {
                        console.log(response);

                        load(function () {
                            toast.fire({
                                icon: "error",
                                title: "error delete item",
                                background: toastBgSuccess,

                            });
                        });
                    }
                },
                error: function (err) {
                    console.log(err);
                },

            });
        }
    });
}

function editUser(sender) {
    var name = $(sender).attr("data-name")
    var email = $(sender).attr("data-email")
    var displayName = $(sender).attr("data-dName")
    var id = $(sender).attr("data-id")

    $('#editName').val(name)
    $('#editDName').val(displayName)
    $('#editEmail').val(email)
    $('#updateUser').attr('data-id', id);
}

$("#updateUser").click(function (event) {
    var id = $(this).attr("data-id");
    var name = $('#editName').val();
    var displayName = $('#editDName').val();
    var email = $('#editEmail').val();

    $.ajax({
        type: "post",
        url: "/updateUser/" + id,
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'name': name,
            'displayName': displayName,
            'email': email,
        },
        success: function (data) {
            load(function () {
                toast.fire({
                    icon: "success",
                    title: "successfully update",
                    background: toastBgSuccess,
                });
            })
        },
        error: function (data) {
            alert("Error" + data)
        }
    });
});
$('#filter-user').on('submit', function (e) {
    e.preventDefault();
    history.replaceState("", "", "?" + $(this).serialize());
    $('#removeFilter').removeClass("btn-light").addClass("btn-danger");
    load(function () {

    });
});
$('#removeFilter').on('click', function () {
    $('#filter-form').trigger('reset');
    var queryString = location.href.split('?');
    history.replaceState("", "", queryString[0]);
    $('#filterPost').removeClass("btn-light").addClass("btn-success");
    $('#removeFilter').removeClass("btn-danger").addClass("btn-light");
    $('#search').val('');
    load(function () {

    });
});
$("#changePassSubmit").click(function () {

    var id = $(this).attr("data-id");
    var pass = $('#editPass').val();
    var repeatPass = $('#editRepeat-pass').val();
    if ( pass == '' || pass !== repeatPass) {

        if (pass =='') {
            $('#error_pass').text('password not invalid ');
        }
        if (pass !== repeatPass) {
            $('#error_repeatPass').text('password not equal ');
        }
        return;
    }
    $.ajax({
        type: "post",
        url: "/changePass/" + id,
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'pass': pass,
        },
        success: function (data) {
            $('#addUser').modal('hide');
            load(function () {
                toast.fire({
                    icon: "success",
                    title: "add term successfully",
                    background: toastBgSuccess,
                });
            })
        },
        error: function (data) {
            alert("Error" + data)
        }
    });
});

