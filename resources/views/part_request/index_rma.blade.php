@extends('layouts.app')
@include('part_request.style')
@section('content')
<div class="container">
    <!--div class="d-flex justify-content-end mb-4" style="position: absolute;right: 2%;zoom: 70%">
        <button id="block-view-btn" class="views btn btn-small btn-secondary active">Block View</button>
        <button id="list-view-btn" class="views btn btn-small btn-secondary ml-2">List View</button>
    </div-->
    <ul class="partsnav nav nav-pills mb-4" id="statusTabs">
        <li class="nav-item">
            <a class="nav-link active" data-status="RFQ Requested" href="#">RMA Requests</a>
        </li>
        <!--li class="nav-item">
            <a class="nav-link" data-status="replied" href="#">Quotes</a>
        </li-->
        <li class="nav-item">
            <a class="nav-link" data-status="ordered" href="#">Approved RMA</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-status="RFQ rejected" href="#">Rejected RMA</a>
        </li>
    </ul>
    <hr>
    <div id="part-requests-container">
        @include('part_request.block_view', ['partRequests' => $partRequests])
    </div>
</div>
@endsection

@push('head-script')
<style>
   
    .views.active {
        background: #0044cc !important;
    }

    svg, .relative.z-0.inline-flex.shadow-sm.rounded-md {
        display: none !important;
    }

    .pagination {
        line-height: 2.5rem;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #62bf81 !important;
        color: #fff;
        font-weight: bolder;
    }

    .btn-link {
        color: #000;
        text-decoration: none;
    }

    .partsnav.nav-pills .nav-link {
        border-radius: 50px;
        margin-right: 10px;
        padding: 10px 20px;
        transition: background-color 0.3s, color 0.3s;
    }
    .partsnav.nav-pills .nav-link {
        color: #0a2e64;
        font-weight: 700;
    }
    .partsnav.nav-pills .nav-link.active {
        background-color: var(--main-color) !important;
        color: white !important;
    }

    .partsnav.nav-pills .nav-link:hover {
        
       
    }
</style>
@endpush

@push('footer-script')
<script>
    $(document).ready(function() {
        var currentView = 'block'; // Set default view to block
        var currentStatus = 'RFQ Requested'; // Set default status to new
        
        $(document).on('click', '.request-rfq-btn', function(e) {
            e.preventDefault();
            var button = $(this);
            var requestId = button.data('id');

            $.ajax({
                url: '{{ route("part_request.request_rfq") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: requestId
                },
                success: function(response) {
                    if (response.status === 'success') {
                        button.replaceWith('<span class="badge badge-success">RFQ Requested</span>');
                        loadView(currentView, currentStatus);
                    } else {
                        alert('An error occurred while requesting RFQ.');
                    }
                },
                error: function() {
                    alert('An error occurred while requesting RFQ.');
                }
            });
        });

        function loadView(viewType, status, page = 1) {
            currentView = viewType;
            currentStatus = status;
            $.ajax({
                url: '{{ route("part_request.index") }}',
                type: 'GET',
                data: {
                    view_type: viewType,
                    status: status,
                    page: page
                },
                success: function(data) {
                    $('#part-requests-container').html(data);
                    updateActiveButton();
                }
            });
        }

        function updateActiveButton() {
            if (currentView === 'list') {
                $('#list-view-btn').addClass('active');
                $('#block-view-btn').removeClass('active');
            } else {
                $('#block-view-btn').addClass('active');
                $('#list-view-btn').removeClass('active');
            }
        }

        $('.partsnav .nav-link').click(function(e) {
            e.preventDefault();
            var status = $(this).data('status');
            $('.partsnav .nav-link').removeClass('active');
            $(this).addClass('active');
            loadView(currentView, status);
        });

        $('#list-view-btn').click(function() {
            loadView('list', currentStatus);
            $('#list-view-btn').addClass("active");
            $('#block-view-btn').removeClass("active");
        });

        $('#block-view-btn').click(function() {
            loadView('block', currentStatus);
            $('#list-view-btn').removeClass("active");
            $('#block-view-btn').addClass("active");
        });

        $(document).on('click', '.pagination a', function(e) {
            e.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            loadView(currentView, currentStatus, page);
        });

        // Initialize the default view on page load
        loadView(currentView, currentStatus);
    });
</script>
@endpush
