@extends('layouts.app')

@section('content')

@section('create-button')
<a href="#" id="preview-form" class="btn btn-info btn-sm m-l-15"><i class="ti-save"></i>
    Submit Request
</a>
@endsection
@push('head-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css" />

<link rel="stylesheet"
      href="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
<style>
    .thumbnail-img {
    width: 64px;  /* Adjust the size as needed */
    height: auto;
    margin-right: 10px;  /* Space between the image and the title */
    vertical-align: middle;  /* Aligns the image with the text */
}
    .search {
        position: relative;
        margin-top: 15px;
    }

    .loading-icon {
        display: none;
        position: absolute;
        right: 25px;
        top: 50%;
        transform: translateY(-50%);
    }

    .result-item:hover {
        cursor: pointer;
        background: lightblue;
    }

    .search-row {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .search-input {
        margin-right: 10px;
        position: relative;
    }

    .quantity-input {
        margin-right: 10px;
    }

    .select2-container{
        width: -webkit-fill-available !important;
    }
    #collapseOne{

        overflow: auto;
    }
    .help-block{
        position: absolute;
        font-size: 12px;
        right: 0;
    }
</style>
@endpush
<form class="ajax-form" id="OrderForm" method="{{isset($order) ? 'PUT' : 'POST'}}" role="form" action="{{isset($order) ? route('orders.update', $order->id) : route('orders.store')}}">
    @csrf

    <div class="row">
        <div class="col-md-12 card" style="padding: 15px">

            <div class="card-body">
                @isset($order)
                <p class="text-right" title="Click here to preview the order">
                    <small>@trans(Last update): {{dformat($order->updated_at).' By: '.$order->createdby->name}}  </small>
                    <strong onclick="showOrder('{{$order->id}}', true)" class="cursor-pointer order-head-status text-white bg-{{$order->status}}">{{ucwords($order->status).' '}}</strong></p>
                @endisset


                <div class="row hide">
                    <div class="offset-md-1 col-md-3">
                        <div class="form-group">
                            <label>@trans(PO Number)</label>
                            <input type="text" maxlength="10" value="{{isset($order->order_no) ? $order->order_no : ''}}" class="form-control" id="order_no" name="order_no" placeholder="" />
                        </div>
                    </div>
                    <div class="col-md-3 hide">
                        <div class="form-group">
                            <label>@trans(Business Unit)</label>
                            <select class="form-control select2">
                                <option>Parts</option>
                                <option>Rental</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>@trans(Type)</label>
                            <select class="form-control select2">
                                <option>Contracted Site</option>
                                <option>Corporate</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 hide">
                        <div class="form-group">
                            <label>@trans(Order Date) <span class="required"> *</span></label>
                            <input type="text" required="" value="{{isset($order->created_at) ? dformat($order->created_at) : dformat('now')}}" class="form-control datepiktime" id="created_at" value="" name="created_at" placeholder="Select date">
                        </div>
                    </div>
                    <div class="col">

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 offset-md-1">
                        <div class="form-group">
                            <label >Site<span class="required"> *</span></label>
                            <select xdata-link="#staff"  xdata-list='' class="select2 m-b-10 form-control" 
                                    data-placeholder="@trans(Site): @trans(viewAll)" name="hospital" id="hospital" required="">

                                @foreach ($hospitals as $item)
                                <option {{selected(isset($order) ? $order->hospital_id : '',$item->id,$item->id)}} value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>@trans(Contact Name)<span class="required"></span></label>
                            <input type="text" maxlength="200" value="{{isset($order->contact_name) ? $order->contact_phone : ''}}" class="form-control" required="" id="contact_name" name="contact_name" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>@trans(Contact Phone)<span class="required"> *</span></label>
                            <input type="text" maxlength="20" value="{{isset($order->contact_phone) ? $order->contact_phone : ''}}" class="form-control" required="" id="contact_phone" name="contact_phone" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="offset-md-1 col-md-3">
                        <div class="form-group">
                            <label>@trans(Date Needed)<span class="required"> *</span></label>
                            <input required="" maxlength="" value="{{isset($order->date_needed) ? dformat($order->date_needed,true) : ''}}" type="text" class="form-control datepik" id="date_needed" value="" name="date_needed" placeholder="@trans(Select date)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>@trans(Notes)</label>
                            <textarea name="notes" class="form-control" style="height: 34px;min-height: 34px;max-height: 110px">{{isset($order->notes) ? $order->notes : ''}}</textarea>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">
               
                <div class="col-md-12 info-box">
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                        <div class="panel">
                            <div class="panel-heading panel-info" role="tab" id="headingOne">
                                <h4 class="panel-title"> 
                                    <a data-toggle="collapse">
                                        @trans(Parts to Request)<span class="required"> *</span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse show" role="tabpanel" aria-labelledby="headingOne">
                                <div id="search-container" >
                                    <div class="search-row">
                                        <div class="search-input col-7">
                                            <input type="text" class="form-control search" placeholder="Search...">
                                            <span class="loading-icon">
                                                <img src="{{asset('assets/images/loader.gif')}}" alt="Loading" width="20" height="20">
                                            </span>
                                            <ul class="list-group mt-3 results"></ul>
                                        </div>
                                        <div class="search-input col-3">
                                            <select class="form-control select2">
                                                <option>AfterMarket Price</option>
                                                <option>Refurbished  Price</option>
                                            </select>
                                        </div>
                                        <input type="number" class="form-control quantity-input" value="1" min="1" style="width: 80px;">
                                        <button type="button" class="btn btn-primary add-line">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@push('footer-script')
<script src="{{ asset('assets/node_modules/select2/dist/js/select2.full.min.js') }}"
type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"
type="text/javascript"></script>
<script>

                        $("table").on('click', '.tr_clone_add', function () {

                        $('.items').select2("destroy");
                        var $tr = $(this).closest('.tr_clone');
                        var $clone = $tr.clone();
                        $tr.after($clone);
                        $('.items').select2({tags: true});
                        $clone.find('.items').select2({'val': ''});
                        $clone.find('input[type=text]').val('1');
                        $clone.find('input.eq-id').val('0');
                        $clone.find('.tr_clone_add').
                                removeClass('tr_clone_add').
                                removeClass('btn-success').
                                addClass('tr_clone_remove').
                                addClass('btn-danger').
                                html('<i class="fa fa-minus"></i>');
                        $('.items').select2({tags: true});
                        });
                        $("table").on('click', '.tr_clone_remove', function () {
                        $(this).closest('.tr_clone').remove();
                        });
                        $('#preview-form').click(function () {
                        swal({
                        title: $(this).text(),
                                text: "@trans(Are you sure)?",
                                type: "info",
                                showCancelButton: true,
                                confirmButtonText: "@lang('app.yes')",
                                cancelButtonText: "@lang('app.no')",
                                closeOnConfirm: true,
                                closeOnCancel: true,
                        }, function (isConfirm) {
                        if (isConfirm) {

                        $.easyAjax({
                        url: $('#OrderForm').attr('action'),
                                container: '#OrderForm',
                                type: $('#OrderForm').attr('method'),
                                redirect: true,
                                data: $('#OrderForm').serialize()
                        }
                        );
                        }
                        }
                        )
                        });</script>
<script>

    $(document).ready(function() {
    $('.items').select2({tags: true});
    $('.datepiktime').bootstrapMaterialDatePicker({format: 'MM/DD/YYYY HH:mm', weekStart: 0, time: true});
    $('#hospital').change(function () {
    var site_id = $(this).val();
    $('.select-staff').empty().append('<option value="">@trans(Staff): @trans(All)</option>');
    $('.select-combo').empty().append('<option value="">@trans(Equipment): @trans(All)</option>');
    $.ajax({
    url: '/orders/fetch',
            data: {"site_id":site_id},
            type: 'GET',
            success: function (data) {
            $.each(data.staff, function (key, value) {
            var selected = '';
            var is_staff = "{{isset(user()->is_staff) ? user()->is_staff->id : ''}}";
            if (is_staff === key)
                    selected = "selected";
            @isset($order)
                    var staff = '{{$order->staff_id}}';
            if (staff === key)
                    selected = 'selected';
            @endisset
                    $('.select-staff').append('<option ' + selected + ' value="' + key + '">' + value + '</option>');
            });
            $('.select-combo').each(function(index) {
            var selectCombo = $(this);
            var eq = $(this).attr("data-eq");
            $.each(data.combo, function (key, value) {
            var option = '<option value="' + key + '">' + value + '</option>';
            selectCombo.append(option);
            if (key === eq) {
            selectCombo.val(key).trigger('change');
            }
            });
            });
            }
    });
    });
    $('#hospital').change();
    });</script>
<script type="text/javascript">
    function debounce(func, delay) {
    let debounceTimer;
    return function() {
    const context = this;
    const args = arguments;
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => func.apply(context, args), delay);
    };
    }

    $(document).ready(function() {

    function addNewSearchRow() {
    var newRow = `
            <div class="search-row">
                <div class="search search-input col-md-7">
                    <input type="text" class="form-control search" placeholder="Search...">
                    <span class="loading-icon">
                        <img src="{{asset('assets/images/loader.gif')}}" alt="Loading" width="20" height="20">
                    </span>
                    <ul class="list-group mt-3 results"></ul>
                </div>
                                    <div class="search-input col-3">
                                            <select class="form-control select2">
                                                <option>AfterMarket Price</option>
                                                <option>Refurbished  Price</option>
                                            </select>
                                        </div>
                <input type="number" class="form-control quantity-input" value="1" min="1" style="width: 80px; margin-right: 10px;">
                <button type="button" class="btn btn-primary add-line">+</button>
            </div>
        `;
    $('#search-container').append(newRow);
    var $newSearchInput = $('#search-container .search-row').last().find('.search');
    //handleSearchInput($newSearchInput);
    }

    // Initialize the first search input
    //handleSearchInput($('.search').first());

    // Add a new line when clicking the add-line button
    $(document).on('click', '.add-line', function() {
    addNewSearchRow();
    });
    // Handle search item selection
    $(document).on('click', '.result-item', function() {
    var selectedName = $(this).attr("data-id");
    //console.log(selectedName);
    var $searchInput = $(this).closest('.search-input').find('input.search');
    $searchInput.val(selectedName);
    $(this).closest('.results').html(''); // Clear the results
    });
    // Initialize any additional dynamically added search inputs
    $(document).on('keyup', '.search', function() {
    var query = $(this).val();
    var $results = $(this).siblings('.results');
    var $loadingIcon = $(this).siblings('.loading-icon');
    if (query.length > 0) {
    $loadingIcon.show();
    $.ajax({
    url: "{{ route('search-ps-ajax') }}",
            type: "GET",
            data: {'query': query},
            success: function(data) {
            $results.html('');
            $.each(data.products, function(index, item) {
            $results.append('<li class="list-group-item result-item" data-id="' + item.title + '">'
    + '<img src="' + item.thumbnailUrl + '" alt="Thumbnail" class="thumbnail-img">'
    + item.title
    + '</li>');
            });
            },
            complete: function() {
            $loadingIcon.hide();
            }
    });
    } else {
    $results.html('');
    $loadingIcon.hide();
    }

    });
    });
</script>
@endpush
@endsection