@extends('layouts.main')
@section('content')
    <div id="content" class="main-content">
        <div class="layout-px-spacing">


            <div class="row" id="cancel-row">

                <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                    <div class="widget-content widget-content-area br-6">
                        <div class="page-header">
                            <div class="row">
                                <div class="col-4"><h5>management web service user</h5></div>
                                <div class="col-4">
                                    <button data-toggle="modal" data-target="#addUser" class="btn btn-info">
                                        add user
                                    </button>
                                </div>
                                <div class="col-4">
                                    <div class="input-group mb-3 ">
                                         <form class="d-flex gap-3" method="get" id="filter-user" data-action="{{route('getUsers')}}">
                                        <input name="filter" id="search" type="text" class="form-control"
                                               placeholder="Search">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-info mx-2" ><i class="fas fa-filter"></i>
                                            </button>
                                            <button id="removeFilter" type="button" class="btn btn-light ">X</button>
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
                                    <th>user login</th>
                                    <th>display name</th>
                                    <th>email</th>
                                    <th>operation</th>
                                </tr>
                                </thead>
                                <tbody id="user_data">
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
    </div>

    {{--    modal for add user--}}
    <div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">add user</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="name">user name:</label>
                            <input name="name" type="text" class="form-control" id="name" placeholder="add name"
                                   value="">
                            <span class="text-danger" id="error_name"></span>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="dName">display name:</label>
                            <input name="dName" type="text" class="form-control" id="displayName"
                                   placeholder="display name"
                                   value="">
                            <span class="text-danger" id="error_dName"></span>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="email">email:</label>
                            <input name="email" type="email" class="form-control" id="email" placeholder="email"
                                   value="">
                            <span class="text-danger" id="error_email"></span>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="pass">password:</label>
                            <input name="pass" type="password" class="form-control" id="pass" placeholder="password"
                                   value="">
                            <span class="text-danger" id="error_pass"></span>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="repeat-pass">repeat pass</label>
                            <input name="repeat-pass" type="password" class="form-control" id="repeat-pass"
                                   placeholder="repeat password"
                                   value="">
                            <span class="text-danger" id="error_repeatPass"></span>
                        </div>
                        <span class="text-danger" id="error_pass"></span>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal"><i class="flaticon-cancel-12"></i>cancel
                        </button>
                        <button class="btn btn-primary addUser">submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{--edite--}}
    <div class="modal fade" id="editUser" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">edit user</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="name">user name:</label>
                            <input name="name" type="text" class="form-control" id="editName" placeholder="add name"
                                   value="">
                            <span class="text-danger" id="error_name"></span>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="dName">display name:</label>
                            <input name="dName" type="text" class="form-control" id="editDName"
                                   placeholder="display name"
                                   value="">
                            <span class="text-danger" id="error_dName"></span>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="email">email:</label>
                            <input name="email" type="email" class="form-control" id="editEmail" placeholder="email"
                                   value="">
                            <span class="text-danger" id="error_email"></span>
                        </div>

                        <span class="text-danger" id="error_pass"></span>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal"><i class="flaticon-cancel-12"></i>cancel
                        </button>
                        <button class="btn btn-primary " id="updateUser" data-id="" data-dismiss="modal">edit</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{--change pass--}}
    <div class="modal fade" id="changePass" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">change password</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="pass">password:</label>
                            <input name="pass" type="password" class="form-control" id="editPass" placeholder="password"
                                   value="">
                            <span class="text-danger" id="error_pass"></span>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="repeat-pass">repeat pass</label>
                            <input name="repeat-pass" type="password" class="form-control" id="editRepeat-pass"
                                   placeholder="repeat password"
                                   value="">
                            <span class="text-danger" id="error_repeatPass"></span>
                        </div>
                        <span class="text-danger" id="error_pass"></span>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal"><i class="flaticon-cancel-12"></i>cancel
                        </button>
                        <button class="btn btn-primary " id="changePassSubmit" data-id="" >change password</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script src="{{asset('assets/myJs/userJs.js')}}"></script>
@endsection
