$(document).ready(function () {
    // setTimeout(function () {
    //     $('.alert-danger').hide()
    // }, 10000);
    load(function () {
    });
});
function load(callback) {

    $.ajax({
        url: '',
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
                               <buttonn data-toggle="modal" data-target="#changePass" class="btn text-warning" onclick="setIdPass(${user.ID})"  ><i class="fa fa-key"></i></buttonn>
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








