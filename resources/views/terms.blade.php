@extends('layouts.main')
@section('content')
    <div id="content" class="main-content">
        <div class="layout-px-spacing">


            <div class="row" id="cancel-row">

                <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
                    <div class="widget-content widget-content-area br-6">
                        <div class="page-header">
                            <div class="row">
                                <div class="col-4"><h5>manege terms</h5></div>
                                <div class="col-4">
                                    <button data-toggle="modal" data-target="#addTerm" class="btn btn-info">
                                        add term
                                    </button>
                                </div>
                                <div class="col-4">
                                    <div class="input-group mb-3">
                                        <form class="d-flex " method="get" id="filter-Term">
                                            <input name="filter" id="search" type="text" class="form-control"
                                                   placeholder="Search">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn mx-2 btn-info" ><i class="fas fa-filter"></i>
                                                </button>
                                                <button id="removeFilter" type="button " class="btn btn-light ">x</button>
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
                                    <th>id term</th>
                                    <th>name</th>
                                    <th>operation</th>
                                </tr>
                                </thead>
                                <tbody id="term_data">
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
    </div>

    {{--    modal for add category--}}
    <div class="modal fade" id="addTerm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">add term</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="name">name</label>
                            <input name="name" type="text" class="form-control" id="name" placeholder="add name"
                                   value="">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" data-dismiss="modal"><i class="flaticon-cancel-12"></i>cancle
                        </button>
                        <button class="btn btn-primary addTerm"  data-dismiss="modal">submit</button>
                    </div>

                </div>
            </div>
        </div>
        </div>


        {{--edite--}}
        <div class="modal fade" id="editTerm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">edit term</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="name">name</label>
                                <input name="editName" type="text" class="form-control" id="editName" placeholder="add name"
                                       value="">
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-danger" data-dismiss="modal"><i class="flaticon-cancel-12"></i>cancel
                            </button>
                            <button class="btn btn-primary " id="updateTerm" data-id="" data-dismiss="modal">submit</button>
                        </div>

                    </div>
                </div>
            </div>
            </div>
            @endsection
            @section('javascript')
                <script src="{{asset('assets/myJs/javascript.js')}}"></script>
@endsection
