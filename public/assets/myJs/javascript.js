$(document).ready(function () {
    // setTimeout(function () {
    //     $('.alert-danger').hide()
    // }, 10000);
    load(function () {
    });
});
$(".addTerm").click(function (event) {
    $.ajax({
        type: "post",
        url: "/setTerm",
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'name': $('#name').val(),
        },
        success: function (data) {
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
$('#search').on('input', function () {
    if ($(this).val().length > 1) {
        insert_param_url('search', $(this).val(), function () {
            load(function () {
            });
        });
    } else if ($(this).val().length <= 0) {
        insert_param_url('search', $(this).val(), function () {
            load(function () {
            });
        });
    }
});

function load(callback) {
    var filter = 'All';
    if (getUrlParameter('search') != '') {
        filter = getUrlParameter('search');
    }
    var index = 1;
    $.ajax({
        url: 'getTerms',
        type: "get",
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'filter': filter
        },
        success: function (data) {

            $('#term_data').empty();
            var html = "";
            var termDta = data.terms;
            $.each(termDta, function (i, term) {
                html =
                    `
                         <tr>
                             <td>${index++}</td>
                             <td>${term.term_id}</td>
                             <td>${term.name}</td>
                              <td class="text-center"><button data-toggle="modal" data-target="#editTerm" class="btn text-primary mx-1" data-id="${term.term_id}" data-name="${term.name}" onclick="editTerm(this)"  ><i class="fa fa-edit"></i></button><button class="btn text-danger" onclick="deleteTerm(${term.term_id})"  ><i class="fa fa-trash"></i></button>
                          </tr>
					  `;
                $('#term_data').append(html);
            });

        }, complete: function () {
            callback();
        },
        error: function (data) {
            console.log(data);
        }
    });
}

function deleteTerm(id) {
    Swal.fire({
        title: 'are you sure delete item?',
        html: '<small>این عمل غیرقابل بازگشت است!</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        cancelButtonText: 'cancel',
        confirmButtonText: 'delete!'
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: "/deleteTerm/" + id,
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

function editTerm(sender){
   var name= $(sender).attr("data-name")
   var id= $(sender).attr("data-id")
    $('#editName').val(name)

    $('#updateTerm').attr('data-id' , id);
}
$("#updateTerm").click(function (event) {
    var id= $(this).attr("data-id");
    $.ajax({
        type: "post",
        url: "/updateTerm/" + id,
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'name': $('#editName').val(),
        },
        success: function (data) {
            load(function () {
                toast.fire({
                    icon: "success",
                    title: "عملیات با موفقیت انجام شد.",
                    background: toastBgSuccess,
                });
            })
        },
        error: function (data) {
            alert("Error" + data)
        }
    });
});

