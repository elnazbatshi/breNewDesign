$(document).ready(function () {
    // setTimeout(function () {
    //     $('.alert-danger').hide()
    // }, 10000);
    load(function () {
    });
});
$(".addCustomerCategory").click(function (event) {
    $.ajax({
        type: "post",
        url: "/customer/set_category",
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'customerCategory': {
                'name': $('#name').val(),
            }
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
                              <td class="text-center"><button class="btn text-primary mx-1" onclick="editeTerm(${term.term_id})"  ><i class="fa fa-edit"></i></button><button class="btn text-danger" onclick="deleteTerm(${term.term_id})"  ><i class="fa fa-trash"></i></button>
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
        title: 'آیا از حذف این دسته بندی اطمینان دارید؟',
        html: '<small>این عمل غیرقابل بازگشت است!</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        cancelButtonText: 'انصراف',
        confirmButtonText: 'حذف!'
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
                                title: "عملیات با موفقیت انجام شد",
                                background: toastBgSuccess,

                            });
                        });

                    } else {
                        console.log(response);

                        load(function () {
                            toast.fire({
                                icon: "error",
                                title: "عملیات با موفقیت انجام نشد",
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

