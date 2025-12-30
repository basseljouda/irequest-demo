<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iMedical Parts</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .searchbtn{
            background: #24609d;
        }
        .f13 {
            font-size: 13px;
        }

        .p {
            margin-bottom: 3px;
        }

        .card-grid .card-text {
            font-size: 13px;
        }

        .small {
            font-size: 8px !important;
        }

        .card-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .card-grid .card {
            flex: 1 1 calc(25% - 20px);
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }

        .card-grid .card img {
            max-height: 150px;
            object-fit: contain;
        }

        .card-grid .card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .list-group-item img {
            max-width: 64px;
            max-height: 64px;
            object-fit: contain;
        }

        ul.pagination {
            display: none;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            background-color: #24609d;
            margin-bottom: 20px;
        }

        .header img {
            max-height: 50px;
            margin-bottom: 10px;
        }

        .header h1 {
            color: #fff;
            font-size: 20px;
            text-align: center;
        }

        .call {
            font-size: 18px;
            color: #62bf81 !important;
        }

        .callus {
            color: #62bf81 !important;
        }

        @media (max-width: 1200px) {
            .card-grid .card {
                flex: 1 1 calc(33.33% - 20px);
            }
        }

        @media (max-width: 992px) {
            .card-grid .card {
                flex: 1 1 calc(50% - 20px);
            }
        }

        @media (max-width: 768px) {
            .card-grid .card {
                flex: 1 1 calc(50% - 20px);
            }

            .header h1 {
                font-size: 20px;
            }

            .call {
                font-size: 16px;
            }
        }

        @media (max-width: 576px) {
            .card-grid .card {
                flex: 1 1 100%;
            }

            .header h1 {
                font-size: 20px;
            }

            .call {
                font-size: 14px;
            }
            
            .view-buttons {
            margin-top: 10px;
        }
        }
    </style>
</head>
<body>
<div class="container mt-2">
    <div class="header">
        <img src="{{ asset('logo2024.png') }}" alt="Logo">
        <h1>Call us: <a class="call" href="tel:+1 844-990-0460">+1 844-990-0460</a></h1>
    </div>
    <div class="row mb-4">
    <div class="col-md-8">
        <div class="input-group">
            <input type="text" id="filter_search" class="form-control" placeholder="P/N, description, model, keyword ..">
            <div class="input-group-append">
                <button id="search-btn" class="btn btn-primary searchbtn">Search</button>
            </div>
        </div>
    </div>
    <div class="col-md-4 text-right view-buttons">
        <button id="list-view-btn" class="btn btn-primary">List View</button>
        <button id="grid-view-btn" class="btn btn-secondary">Grid View</button>
    </div>
</div>


    <div id="result-container">
        <div id="list-view" class="list-group d-none"></div>
        <div id="grid-view" class="card-grid d-none"></div>
    </div>
    <nav aria-label="Page navigation pagination">
        <ul class="pagination justify-content-center">
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Previous" id="prev-page">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <!-- Page numbers will be dynamically inserted here -->
            <li class="page-item">
                <a class="page-link" href="#" aria-label="Next" id="next-page">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
<div class="loading-overlay d-none">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        const listView = $('#list-view');
        const gridView = $('#grid-view');
        const listViewBtn = $('#list-view-btn');
        const gridViewBtn = $('#grid-view-btn');
        const filterSearch = $('#filter_search');
        const loadingOverlay = $('.loading-overlay');
        let currentPage = 1;
        let lastPage = 1;

        function toggleView(view) {
            if (view === 'list') {
                listView.removeClass('d-none');
                gridView.addClass('d-none');
                listViewBtn.addClass('btn-primary').removeClass('btn-secondary');
                gridViewBtn.addClass('btn-secondary').removeClass('btn-primary');
            } else {
                listView.addClass('d-none');
                gridView.removeClass('d-none');
                listViewBtn.addClass('btn-secondary').removeClass('btn-primary');
                gridViewBtn.addClass('btn-primary').removeClass('btn-secondary');
            }
        }

        function fetchBase64Image(url, id) {
            return $.ajax({
                url: "{{ route('image.serve') }}",
                method: 'GET',
                data: {u: url, id: id}
            });
        }

        function fetchResults(query, page = 1) {
            loadingOverlay.removeClass('d-none');
            $.ajax({
                url: "{{ route('parts.local_catalog') }}",
                method: 'GET',
                data: {filter_search: query, page: page},
                success: function (response) {
                    renderResults(response.data); // Assuming response.data contains the products
                    currentPage = response.current_page;
                    lastPage = response.last_page;
                    updatePagination();
                    if (lastPage != currentPage) {
                        $('ul.pagination').css('display', 'flex');
                    }
                },
                error: function () {
                    alert('Error fetching data.');
                },
                complete: function () {
                    loadingOverlay.addClass('d-none');
                }
            });
        }

        function renderModels(models) {
            if (!models) {
                return '';
            }

            // Convert the models string to an array
            let modelArray;
            try {
                modelArray = JSON.parse(models);
            } catch (e) {
                console.error('Error parsing models:', e);
                return '';
            }

            if (!Array.isArray(modelArray) || modelArray.length === 0) {
                return '';
            }

            if (modelArray.length <= 5) {
                return `<span class="small"><b>Models:</b> ${modelArray.join(', ')}</span><br/>`;
            }

            const initialModels = modelArray.slice(0, 5).join(', ');
            const remainingModels = modelArray.slice(5).join(', ');
            return `
                <span class="small"><b>Models:</b> ${initialModels} <a href="#" class="more-models">... More</a></span>
                <span class="small d-none more-models-list">${remainingModels}</span><br/>
            `;
        }

        function renderResults(data) {
            listView.empty();
            gridView.empty();

            data.forEach(item => {
                fetchBase64Image(item.thumbnailUrl, item.partNumber).then(response => {
                    const imageUrl = response.base64;
                    // Format prices to 3 decimal places
                    const callus = '<a class="callus" href="tel:+1 844-990-0460">Call us</a>';
                    const priceImed = item.price_imed ? '$' + parseFloat(item.price_imed).toFixed(3) : callus;
                    const priceImedRef = item.price_imed_ref ? '$' + parseFloat(item.price_imed_ref).toFixed(3) : callus;

                    const listItem = `
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <img src="${imageUrl}" alt="${item.title}">
                            <div class="flex-grow-1 ml-3">
                                <h5 class="mb-1">${item.description}</h5>
                                <p class="mb-1 f13">OEM: <u>${item.partNumber}</u> ${item.title}</p>
                                ${renderModels(item.models)}
                                <small>Price (New Aftermarket): <b>${priceImed}</b></small><br>
                                <small>Price (Refurbished): <b>${priceImedRef}</b></small>
                            </div>
                        </div>
                    `;

                    const gridItem = `
                        <div class="card">
                            <img class="card-img-top" src="${imageUrl}" alt="${item.title}">
                            <div class="card-body">
                                <h5 class="card-title">${item.description}</h5>
                                <p class="card-text f13">OEM: <u>${item.partNumber}</u> ${item.title}</p>
                                ${renderModels(item.models)}
                                <p class="card-text"><strong>Price (New Aftermarket):</strong> ${priceImed}</p>
                                <p class="card-text"><strong>Price (Refurbished):</strong> ${priceImedRef}</p>
                            </div>
                        </div>
                    `;

                    listView.append(listItem);
                    gridView.append(gridItem);
                });
            });
        }

        function updatePagination() {
            const pagination = $('.pagination');
            pagination.find('.page-item').not(':first').not(':last').remove();

            const maxVisiblePages = 5;
            const startPage = Math.max(currentPage - Math.floor(maxVisiblePages / 2), 1);
            const endPage = Math.min(startPage + maxVisiblePages - 1, lastPage);

            for (let i = startPage; i <= endPage; i++) {
                const pageItem = $('<li class="page-item"></li>').toggleClass('active', i === currentPage);
                const pageLink = $('<a class="page-link" href="#"></a>').text(i).on('click', function (e) {
                    e.preventDefault();
                    fetchResults(filterSearch.val(), i);
                });

                pageItem.append(pageLink);
                $('#next-page').parent().before(pageItem);
            }

            if (startPage > 1) {
                const firstPageItem = $('<li class="page-item"></li>');
                const firstPageLink = $('<a class="page-link" href="#"></a>').text(1).on('click', function (e) {
                    e.preventDefault();
                    fetchResults(filterSearch.val(), 1);
                });

                firstPageItem.append(firstPageLink);
                $('#prev-page').parent().after(firstPageItem);

                if (startPage > 2) {
                    const ellipsisItem = $('<li class="page-item disabled"><span class="page-link">...</span></li>');
                    $('#prev-page').parent().after(ellipsisItem);
                }
            }

            if (endPage < lastPage) {
                const lastPageItem = $('<li class="page-item"></li>');
                const lastPageLink = $('<a class="page-link" href="#"></a>').text(lastPage).on('click', function (e) {
                    e.preventDefault();
                    fetchResults(filterSearch.val(), lastPage);
                });

                lastPageItem.append(lastPageLink);
                $('#next-page').parent().before(lastPageItem);

                if (endPage < lastPage - 1) {
                    const ellipsisItem = $('<li class="page-item disabled"><span class="page-link">...</span></li>');
                    $('#next-page').parent().before(ellipsisItem);
                }
            }

            $('#prev-page').parent().toggleClass('disabled', currentPage === 1);
            $('#next-page').parent().toggleClass('disabled', currentPage === lastPage);
        }

        filterSearch.on('keypress', function (e) {
            if (e.which === 13) {
                fetchResults($(this).val());
            }
        });

        listViewBtn.on('click', function () {
            toggleView('list');
        });

        gridViewBtn.on('click', function () {
            toggleView('grid');
        });

        $('#prev-page').on('click', function (e) {
            e.preventDefault();
            if (currentPage > 1) {
                fetchResults(filterSearch.val(), currentPage - 1);
            }
        });

        $('#next-page').on('click', function (e) {
            e.preventDefault();
            if (currentPage < lastPage) {
                fetchResults(filterSearch.val(), currentPage + 1);
            }
        });

        $(document).on('click', '.more-models', function (e) {
    e.preventDefault();
    const moreModelsList = $(this).closest('span').next('.more-models-list');
    moreModelsList.toggleClass('d-none');
    $(this).text('');
});

$('#search-btn').on('click', function () {
    fetchResults($('#filter_search').val());
});


        // Initial load
        toggleView('list');
        fetchResults('');
    });
</script>
</body>
</html>
