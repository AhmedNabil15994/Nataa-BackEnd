@extends('apps::dashboard.layouts.app')
@section('title', __('report::dashboard.reports.routes.drivers'))
@section('content')
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <a href="{{ url(route('dashboard.home')) }}">{{ __('apps::dashboard.home.title') }}</a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <a href="#">{{__('report::dashboard.reports.routes.drivers')}}</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light bordered">



                        {{-- DATATABLE FILTER --}}
                        <div class="row">
                            <div class="portlet box grey-cascade">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-gift"></i>
                                        {{__('apps::dashboard.datatable.search')}}
                                    </div>
                                    <div class="tools">
                                        <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div id="filter_data_table">
                                        <div class="panel-body">
                                            <form id="formFilter" class="horizontal-form">
                                                <div class="form-body">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    {{__('apps::dashboard.datatable.form.date_range')}}
                                                                </label>
                                                                <div id="reportrange" class="btn default form-control">
                                                                    <i class="fa fa-calendar"></i> &nbsp;
                                                                    <span> </span>
                                                                    <b class="fa fa-angle-down"></b>
                                                                    <input type="hidden" name="from">
                                                                    <input type="hidden" name="to">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    {{__('user::dashboard.drivers.create.form.company')}}
                                                                </label>
                                                                @inject('driverCompanies','Modules\User\Repositories\Dashboard\DriverRepository')
                                                                <select name="company_id" id="single" class="form-control select2"
                                                                        data-name="company_id">
                                                                    <option value=""></option>
                                                                    @foreach ($driverCompanies->getAllDriversCompanies() as $company)
                                                                        <option value="{{ $company['id'] }}" {{ $company['id'] == old('company_id') ? 'selected' : '' }}>
                                                                            {{ $company->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="help-block"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <label class="control-label">
                                                                    {{__('report::dashboard.reports.drivers.driver')}}
                                                                </label>
                                                                <select name="driver_id" id="single" class="form-control select2"
                                                                        data-name="driver_id">
                                                                    <option value=""></option>
                                                                    @foreach ($driverCompanies->getAllDrivers() as $driver)
                                                                        <option value="{{ $driver['id'] }}" {{ $driver['id'] == old('driver_id') ? 'selected' : '' }}>
                                                                            {{ $driver->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <div class="help-block"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="form-actions">
                                                <button class="btn btn-sm green btn-outline filter-submit margin-bottom"
                                                        id="search">
                                                    <i class="fa fa-search"></i>
                                                    {{__('apps::dashboard.datatable.search')}}
                                                </button>
                                                <button class="btn btn-sm red btn-outline filter-cancel">
                                                    <i class="fa fa-times"></i>
                                                    {{__('apps::dashboard.datatable.reset')}}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- END DATATABLE FILTER --}}


                        <div class="portlet-title">
                            <div class="caption font-dark">
                                <i class="icon-settings font-dark"></i>
                                <span class="caption-subject bold uppercase">
                                {{__('report::dashboard.reports.routes.drivers')}}
                            </span>
                            </div>
                        </div>

                        {{-- DATATABLE CONTENT --}}
                        <div class="portlet-body">
                            <table class="table table-striped table-bordered table-hover" id="dataTable">
                                <thead>
                                <tr>
                                    <th class="desktop" data-priority="1" >#</th>
                                    <th class="desktop" data-priority="1" >{{__('report::dashboard.reports.drivers.company')}}</th>
                                    <th class="desktop" data-priority="1" >{{__('report::dashboard.reports.drivers.driver')}}</th>
                                    <th class="desktop" data-priority="1">{{__('report::dashboard.reports.drivers.total')}}</th>
                                    <th class="desktop" data-priority="1">{{__('report::dashboard.reports.drivers.orders_count')}}</th>
                                    <th class="desktop" data-priority="1">{{__('report::dashboard.reports.vendors.created_at')}}</th>
                                </tr>
                                </thead>
                                <tfoot>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                    <th>-</th>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')

    <script>
        function tableGenerate(data = '') {

            var dataTable =
                $('#dataTable').DataTable({
                    "createdRow": function (row, data, dataIndex) {
                        if (data["deleted_at"] != null) {
                            $(row).addClass('danger');
                        }
                    },
                    ajax: {
                        url: "{{ url(route('dashboard.reports.drivers_datatable')) }}",
                        type: "GET",
                        data: {
                            req: data,
                    },
                    },
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.10.16/i18n/{{ucfirst(LaravelLocalization::getCurrentLocaleName())}}.json"
                    },
                    stateSave: true,
                    processing: true,
                    serverSide: true,
                    responsive: !0,
                    order: [],
                    columns: [
                        {data: 'id', className: 'dt-center'},
                        {data: 'company', className: 'dt-center'},
                        {data: 'driver', className: 'dt-center'},
                        {data: 'total', className: 'dt-center'},
                        {data: 'orders_count', className: 'dt-center'},
                        {data: 'created_at', className: 'dt-center'},
                    ],
                    columnDefs: [],
                    dom: 'Bfrtip',
                    lengthMenu: [
                        [10, 25, 50, 100, 500, 1000, 2000],
                        ['10', '25', '50', '100', '500', "1000", "2000"]
                    ],
                    buttons: [
                        {
                            extend: "pageLength",
                            className: "btn blue btn-outline",
                            text: "{{__('apps::dashboard.datatable.pageLength')}}",
                            exportOptions: {
                                stripHtml: false,
                                columns: ':visible',
                                columns: [0,1, 2, 3, 4]
                            }
                        },
                        {
                            extend: "print",
                            className: "btn blue btn-outline",
                            text: "{{__('apps::dashboard.datatable.print')}}",
                            footer: true,
                            header: true,
                            exportOptions: {
                                stripHtml: true,
                                columns: ':visible',
                                columns: [0,1, 2, 3, 4]
                            }
                        },
                        {
                            extend: "pdfHtml5",
                            className: "btn blue btn-outline",
                            text: "{{__('apps::dashboard.datatable.pdf')}}",
                            footer: true,
                            header: true,
                            "charset": "utf-8",
                            exportOptions: {
                                stripHtml: true,
                                header: true,
                                columns: ':visible',
                                columns: [0, 1, 2, 3, 4],

                            }
                        },
                        {
                            extend: "excel",
                            className: "btn blue btn-outline ",
                            text: "{{__('apps::dashboard.datatable.excel')}}",
                            footer: true,
                            header: true,
                            exportOptions: {
                                stripHtml: true,
                                columns: ':visible',
                                columns: [0,1, 2, 3, 4]
                            }
                        },
                        {
                            extend: "colvis",
                            className: "btn blue btn-outline",
                            text: "{{__('apps::dashboard.datatable.colvis')}}",
                            exportOptions: {
                                stripHtml: false,
                                columns: ':visible',
                                columns: [0, 1, 2, 3, 4]
                            }
                        }
                    ],
                    footerCallback:function(row, data, start, end, display){
                        var api = this.api()
                    }
                });
        }

        jQuery(document).ready(function () {
            tableGenerate();
        });
    </script>

@stop
