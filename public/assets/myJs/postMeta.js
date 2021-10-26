$(document).ready(function () {
    // setTimeout(function () {
    //     $('.alert-danger').hide()
    // }, 10000);
    getStatus(function () {
        getUsername(function () {
            load('');
        });
    });
});
var terms = '';
var users = '';

function load(page) {
    var filter = '';

    filter = getUrlParameter('filter');

    if (filter == undefined || filter == '') {
        $('#remove_filter').removeClass('btn-danger');
    }

    var index = 1;
    var PerPage = 30;
    if (parseInt(page) > 1) {
        var passedindex = parseInt(page - 1) * PerPage;
    } else {
        passedindex = 0;
    }
    index += passedindex;
    var html = '';
    var pagination = '';

    $.ajax({
        method: 'POST',
        url: "http://127.0.0.1:8000/wp-transport-rest/?page=" + page,
        // url: "http://127.0.0.1:8000/wp-transport-rest/?page="+page,
        data: {
            token: 'sVR7nTA3O8S01XciOrjB5Y3vplzxLtBok',
            action: 'LoadTrackNumbers',
            PerPage: PerPage,
            filter: filter,
        },
        success: function (res) {
            $('#paginatePagesNav').empty();
            $('#post_data').empty();
            const response = JSON.parse(res);
            if (response.status == 200) {
                var PagePrev = parseInt(response.current_page) - 1;
                var PageNext = parseInt(response.current_page) + 1;
                const data = response.data.data;
                const links = response.data.links


                $.each(data, function (i, post) {
                    html =
                        `
                         <tr>
                             <td>${index++}</td>
                             <td>${post.meta_id}</td>
                             <td>${post.meta_value}</td>
                               <td class="text-center">
                               <button data-toggle="modal" data-target="#addStatus" class="btn text-primary mx-1" onclick="getStatus(${post.meta_value})"  >add status<i class="fa fa-edit"></i></button>
                               <button data-toggle="modal" data-target="#editTrackCode" class="btn text-primary" onclick="editTrack(${post.meta_value})"  ><i class="fas fa-edit"></i></button>
                               <button data-toggle="modal" onclick="viewStatus(${post.meta_value})" data-target="#viewStatus" class="btn text-warning" onclick="setIdPass()"  ><i class="fa fa-eye"></i></button>
                              </td>
                          </tr>
					  `;
                    $('#post_data').append(html);
                });
                //$('#paginatePagesNav').append(Buttons);
                $.each(links, function (i, link) {
                    pagination += `<li class="${link.active == true ? "active" : ""}"><a href="#" onclick="load(${link.label})">${link.label}</a></li>`;
                });
            }
            $('#paginatePagesNav').append(pagination);
        },
    });
}

$('#filter-post').on('submit', function (e) {
    e.preventDefault();
    history.replaceState("", "", "?" + $(this).serialize());
    $('#remove_filter').removeClass("btn-light").addClass("btn-danger");
    load(function () {

    });
});
$('#remove_filter').on('click', function () {
    $('#filter-form').trigger('reset');
    var queryString = location.href.split('?');
    history.replaceState("", "", queryString[0]);

    $('#search').val('');
    load(function () {
    });
});

function getStatus(callback) {
    terms = '';
    $.ajax({
        url: 'getTerms',
        type: "get",
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
        },
        success: function (data) {
            $.each(data.terms, function (i, term) {
                terms += `<option value="${term.name}">${term.name}</option>`;
            });
            $('#statusTerm').append(terms);
        },
        error: function (data) {
            console.log(data);
        },
        complete: function (data) {
            callback();
        }
    });
}


function getUsername(callback) {
    users = '';
    $.ajax({
        url: 'getUsers',
        type: "get",
        data: {
            '_token': $('meta[name="csrf-token"]').attr('content'),
        },
        success: function (data) {
            $.each(data.users, function (i, user) {
                users += `<option value="${user.user_login}">${user.user_login}</option>`;
            });
            $('#username').append(users);
        },
        error: function (data) {
            console.log(data);
        },
        complete: function (data) {
            callback();
        }
    });
}

$('#form-add-status').on('submit', function (e) {
    e.preventDefault();
    var username = $('#username').find(":selected").val();
    var track_code = $('#trackCode').find(":selected").val();
    var status = $('#statusTerm').find(":selected").val();
    var date = $('#basicFlatpickr').val();
    var location = $('#location').val();
    var receiver = $('#receiver').val();

    $.ajax({
        method: 'POST',
        url: "http://127.0.0.1:8000/wp-transport-rest/",
        data: {
            token: 'sVR7nTA3O8S01XciOrjB5Y3vplzxLtBok',
            action: 'add_details',
            username: username,
            track_code: track_code,
            date: date,
            location: location,
            status: status,
            receiver: receiver
        },
        success: function (res) {
            $('#addStatus').modal('hide');
            console.log(res);
        },
    });
});

function viewStatus(sender) {
    var html = "";
    var index = 1;
    $.ajax({
        method: 'POST',
        url: "http://127.0.0.1:8000/wp-transport-rest/",
        data: {
            token: "sVR7nTA3O8S01XciOrjB5Y3vplzxLtBok",
            action: 'get_details_min',
            track_code: [sender]
        },
        success: function (data) {
            data = JSON.parse(data);
            $.each(data.data.details, function (i, track) {
                html = `<tr>
                            <th class="border" scope="row">${index++}</th>
                            <td class="border">${track.time}</td>
                            <td class="border">${track.location}</td>
                            <td class="border">${track.status}</td>
                        </tr>`;

            });
            $('#statusTable').append(html);

        }
        , error: function (err) {
            return err;
        }, done: function (res) {
            return res;
        },
    });
}


function editTrack(sender) {

    $('#table_detail_track').empty();
    $('#trackCodeEdit').val(sender);
    $.ajax({
        method: 'POST',
        url: "http://127.0.0.1:8000/wp-transport-rest/",
        data: {
            token: "sVR7nTA3O8S01XciOrjB5Y3vplzxLtBok",
            action: 'get_details_min',
            track_code: [sender]
        },
        success: function (data) {
            data = JSON.parse(data);
            $.each(data.data.details, function (i, track) {
                html = `<tr>
                            <td>${i + 1}</td>
                            <td><input class="form-control" value="${track.time}" type="text"></td>
                            <td><input class="form-control" value="${track.location}" type="text"></td>
                            <td>
                            <select  id="statusTerm" class="placeholder js-states form-control">
                            ` + terms + `
                             </select>
                        </tr>`;
                $('#table_detail_track').append(html);
                $('#statusTerm select').val(`${track.status}`).change();

            });
        }
        , error: function (err) {
            return err;
        }, done: function (res) {
            return res;
        },
    });
}

function edit_track(e) {
    e.preventDefault();
    var datas = [];

    var track_code = $('#trackCodeEdit').val();
    var username = $('#username').find(":selected").val();
    datas.push({
        date: '2022/02/10 18:58',
        location: 'IRI',
        status: "Shipment's Clearance Needs Official Registration Or Permission And Declared To Consignee",
        receiver: 'Test receiver name'
    });
    $.ajax({
        method: 'POST',
        url: "http://127.0.0.1:8000/wp-transport-rest/",
        data: {
            token: 'sVR7nTA3O8S01XciOrjB5Y3vplzxLtBok',
            action: 'edit_details',
            username: username,
            track_code: track_code,
            data: {datas}
        },
        success: function (res) {
            console.log(res);
        },
    });
}
