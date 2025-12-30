<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iMedical Parts</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .f13{
            font-size:13px;
        }
        .p{
            margin-bottom: 3px;
        }
        .card-grid .card-text{
            font-size: 13px
        }
        .small{
            font-size: 8px !important;
        }
        .card-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .card-grid .card {
            flex: 1 1 calc(25% - 20px); /* Adjust the percentage to change the number of columns */
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
            align-items: center;
            padding: 20px;
            background-color: #24609d;
            margin-bottom: 20px;
        }
        .header img {
            max-height: 50px;
            margin-right: 20px;
        }
        .header h1 {
            margin-left: 150px;
            color:#fff;
            font-size: 30px;
        }
    </style>
</head>
<body>
    <div class="container mt-2">
        <div class="header">
            <img src="{{ asset('logo2024.png') }}" alt="Logo">
            <h1>iMedical Parts Database</h1>
        </div>
        <div class="row mb-4">
            <div class="col-md-8">
                <input type="text" id="filter_search" class="form-control" placeholder="Enter (P/N, description, model, keyword) to search">
            </div>
            <div class="col-md-4 text-right">
                <button id="list-view-btn" class="btn btn-primary">List View</button>
                <button id="grid-view-btn" class="btn btn-secondary">Grid View</button>
            </div>
        </div>
        <div id="result-container">
            <div id="list-view" class="list-group d-none"></div>
            <div id="grid-view" class="card-grid d-none"></div>
        </div>
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
            function fetchBase64Image(url,id) {
                return $.ajax({
                    url: "{{ route('image.serve') }}",
                    method: 'GET',
                    data: { u: url, id: id }
                });
            }
            function fetchResults(query) {
                loadingOverlay.removeClass('d-none');
                $.ajax({
                    url: "{{ route('parts.catalog') }}",
                    method: 'GET',
                    data: { filter_search: query },
                    success: function (response) {
                        renderResults(response.data); // Assuming response.data contains the products
                    },
                    error: function () {
                        alert('Error fetching data.');
                    },
                    complete: function () {
                        loadingOverlay.addClass('d-none');
                    }
                });
            }

            function renderResults(data) {
                listView.empty();
                gridView.empty();

                data.forEach(item => {
                    fetchBase64Image(item.thumbnailUrl,item.partNumber).then(response => {
                        const imageUrl = response.base64;

                        const listItem = `
                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <img src="${imageUrl}" alt="${item.title}">
                            <div class="flex-grow-1 ml-3">
                                <h5 class="mb-1">${item.description}</h5>
                                <p class="mb-1 f13">OEM: <u>${item.partNumber}</u> ${item.title}</p>
                                <span class="small"><b>Models:</b> ${item.models}</span><br/>    
                                <small>Price (New): <b>$${item.price_imed}</b></small><br>
                                <small>Price (Refurbished): <b>$${item.price_imed_ref}</b></small>
                            </div>
                        </div>
                    `;

                    const gridItem = `
                        <div class="card">
                            <img class="card-img-top" src="${imageUrl}" alt="${item.title}">
                            <div class="card-body">
                                <h5 class="card-title">${item.description}</h5>
                                <p class="card-text f13">OEM: <u>${item.partNumber}</u> ${item.title}</p>
                                <p class="card-text"><strong>Price (New):</strong> $${item.price_imed}</p>
                                <p class="card-text"><strong>Price (Refurbished):</strong> $${item.price_imed_ref}</p>
                            </div>
                        </div>
                    `;

                        listView.append(listItem);
                        gridView.append(gridItem);
                    });
                });
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

            // Initial load
            toggleView('list');
            fetchResults('');
        });
    </script>
</body>
</html>
