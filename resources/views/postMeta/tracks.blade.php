@extends('layouts.main')
@section('content')
    <div id="content" class="main-content">
        <div class="layout-px-spacing">
            <div class="row" id="cancel-row">
                <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                    <div class="widget-content widget-content-area br-6">
                        <div class="page-header">
                            <div class="row">
                                <div class="col-4"><h5>managing tracking code</h5></div>
                                <div class="col-4">
                                    <button data-toggle="modal" data-target="#addTerm" class="btn btn-info">
                                        add term
                                    </button>
                                </div>
                                <div class="col-4">
                                    <div class="input-group mb-3">
                                        <form class="d-flex " method="get" id="filter-post">
                                            <input name="filter" id="search" type="text" class="form-control"
                                                   placeholder="Search">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn mx-2 btn-info"><i
                                                        class="fas fa-filter"></i>
                                                </button>
                                                <button id="remove_filter" type="button " class="btn btn-light ">x
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover style-3 non-hover">
                                <thead>
                                <tr>
                                    <th>item</th>
                                    <th>id</th>
                                    <th>tracking code</th>

                                </tr>
                                </thead>
                                <tbody id="post_data">
                                </tbody>
                            </table>
                        </div>
                        <div class="paginating-container pagination-solid">
                            <ul class="pagination" id="paginatePagesNav">
                                {{--                                <li  class="prev"><a href="javascript:void(0);"><i class="fas fa-angle-left"></i></a></li>--}}
                                {{--                                <li><a href="javascript:void(0);">1</a></li>--}}
                                {{--                                <li class="active"><a href="javascript:void(0);">2</a></li>--}}
                                {{--                                <li><a href="javascript:void(0);">3</a></li>--}}
                                {{--                                <li class="next"><a href="javascript:void(0);"><i class="fas fa-angle-right"></i></a></li>--}}
                            </ul>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
    </div>

    <div class="modal fade" id="addStatus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">add status</h5>
                </div>
                <form method="get" id="form-add-status">
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="name">track code:</label>
                                <input name="trackCode" type="text" class="form-control" id="trackCode"
                                       placeholder="trackCode"
                                       value="">
                                <span class="text-danger" id="error_name"></span>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="name">status:</label>
                                <select id="statusTerm" class="placeholder js-states form-control">

                                </select>
                                <span class="text-danger" id="error_name"></span>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="dName">username:</label>
                                <select id="username" class="placeholder js-states form-control">

                                </select>
                                <span class="text-danger" id="error_dName"></span>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="email">location:</label>
                                <textarea class="form-control" name="location" id="location"></textarea>
                                <span class="text-danger" id="error_location"></span>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="email">receiver:</label>
                                <textarea class="form-control" name="receiver" id="receiver"></textarea>
                                <span class="text-danger" id="error_receiver"></span>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="email">date:</label>
                                <div class="form-group mb-0">
                                    <input id="basicFlatpickr" value="2019-09-19 12:00"
                                           class="form-control flatpickr flatpickr-input active" type="text"
                                           placeholder="Select Date..">

                                </div>
                                <span class="text-danger" id="error_receiver"></span>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" data-dismiss="modal"><i class="flaticon-cancel-12"></i>cancel
                            </button>
                            <button type="submit" class="btn btn-primary addUser">submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade " id="editTrackCode" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">add user</h5>
                </div>
                <form method="get" id="edit_track">
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="name">track code:</label>
                                <input name="trackCode" type="text" class="form-control" id="trackCodeEdit"
                                       placeholder="trackCode"
                                       value="">
                                <span class="text-danger" id="error_name"></span>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">time</th>
                                    <th scope="col">location</th>
                                    <th scope="col">status</th>
                                </tr>
                                </thead>
                                <tbody id="table_detail_track">
                                <tr>
                                    <th scope="row">1</th>
                                    <td><input type="text"></td>
                                    <td><input type="text"></td>
                                    <td><input type="text"></td>

                                </tr>


                                </tbody>
                            </table>

                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" data-dismiss="modal"><i class="flaticon-cancel-12"></i>cancel
                            </button>
                            <button type="submit" class="btn btn-primary addUser">edit track</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="viewStatus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">track detail</h5>
                </div>
                <form method="get">
                    <div class="modal-body">
                        <div  class="form-row">

                            <div class="col-12">

                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th class="border" scope="col">#</th>
                                        <th class="border" scope="col">time</th>
                                        <th class="border" scope="col">location</th>
                                        <th class="border" scope="col">status</th>
                                    </tr>
                                    </thead>
                                    <tbody id="statusTable" >

                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('javascript')
    <script>
        var f2 = flatpickr(document.getElementById('basicFlatpickr'), {
            enableTime: true,
            dateFormat: "Y/m/d H:i:S",
        });
        $('#username').select2({
        });
        $('#statusTerm').select2({
        });

    </script>
    <script src="{{asset('assets/myJs/postMeta.js')}}"></script>
@endsection
