<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Airtable Printer</title>


        <style>
            html
            {
                margin: 0;
                padding: 0;

            }
            @page {
                background: #eee;
                margin: 0;
                padding: 0;
            }
            @media print
            {
                .hidden-print
                {
                    display: none;
                }
                .barcode-label{
                    outline: none !important;
                    border: none !important;
                }
            }


            .barcode-label{
                -webkit-box-sizing: content-box;
                -moz-box-sizing: content-box;
                box-sizing: content-box;
                font-size: 15px;
                width:80%;
                height: 1.8in;
                margin-right: .195in;

                float: left;
                line-height: 1em;
                letter-spacing: normal;
                overflow: hidden;
                outline: 1px dotted;
            }

            .page-break  {
                clear: left;
                display:block;
                page-break-after:always;
            }

        </style>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.0.1/css/fixedColumns.dataTables.min.css" crossorigin="anonymous">

        <style>
            body {
                background: #17468f;
            }
            page {
                background: white;
                display: block;
                margin: 0 auto;
                margin-bottom: 0.5cm;
                box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
            }
            page[size="custom"] {  
                width: 4.0in;
                height: 2.0in;
            }
            page[size="A4"] {  
                width: 21cm;
                height: 29.7cm; 
            }
            page[size="A4"][layout="landscape"] {
                width: 29.7cm;
                height: 21cm;  
            }
            page[size="A3"] {
                width: 29.7cm;
                height: 42cm;
            }
            page[size="A3"][layout="landscape"] {
                width: 42cm;
                height: 29.7cm;  
            }
            page[size="A5"] {
                width: 14.8cm;
                height: 21cm;
            }
            page[size="A5"][layout="landscape"] {
                width: 21cm;
                height: 14.8cm;  
            }
            @media print {
                body, page {
                    margin: 0;
                    box-shadow: 0;
                }
                .no_print{
                    display: none!important;
                }
            }
            #airtable_wrapper{
                background-color: #fff;
                padding: 10px;
            }
            th,td{
                white-space: nowrap;
            }
            div.dataTables_wrapper {

                margin: 0 auto;
            }
            .cfilter{
                border: 1px solid #aaa;
                border-radius: 3px;
                padding: 5px;
                background-color: transparent;
                margin-left: 3px;
                margin-right: 3px;
            }
        </style>
        <meta name="csrf-token" content="{{ csrf_token() }}" />
    </head>

    <body>
        <div id='filter-div'></div>
        <!--iframe class="airtable-embed" src="https://airtable.com/embed/shrNm3C1bJrk9drEq?backgroundColor=blue&viewControls=on" frameborder="0" onmousewheel="" width="100%" height="533" style="background: transparent; border: 1px solid #ccc;"></iframe-->
        <table id="airtable" class="stripe row-border order-column" style="width:100%" >
            <thead>
                <tr style="background-color: #eee">
                    <th></th>
                    <th>iMed Asset ID</th>
                    <th>Accepted Date</th>
                    <th>Old Asset ID</th>
                    <th>Serial Number</th>
                    <th>Asset Category</th>
                    <th>Subcategory</th>
                    <th>Manufacturer</th>

                    <th>Model Name</th>
                    <th>Model Number</th>
                    <th>Production Status</th>
                    <th>Date Received</th>

                    <th>From Location</th>
                    <th class="select-filter">Warehouse Location</th>
                    <th>Notes</th>
                    <th>Availability</th>

                    <th>Last Modified</th>
                </tr>
            </thead>
            <tbody>
                @foreach ( $records as $record )
                <tr data-row-id="{{$record->getId()}}">
                    <td></td>
                    <td>{{$record->getFields()['iMed Asset ID']}}</td>
                    <td>{{$record->getFields()['Accepted Date']}}</td>
                    <td>{{$record->getFields()['Old Asset ID']}}</td>
                    <td>{{$record->getFields()['Serial Number']}}</td>
                    <td>{{$airtable->getRecord('Categories',$record->getFields()['Asset Category'][0])->getFields()['Category']}}</td>
                    <td>{{$record->getFields()['Subcategory (from Category copy 2)'][0]}}</td>
                    <td>{{$record->getFields()['Manufacturer']}}</td>

                    <td>{{$record->getFields()['Model Name']}}</td>
                    <td>{{$record->getFields()['Model Number']}}</td>
                    <td>{{$record->getFields()['Production Status']}}</td>
                    <td>{{$record->getFields()['Date Received']}}</td>

                    <td>{{$record->getFields()['From Location']}}</td>
                    <td>{{$record->getFields()['Warehouse Location']}}</td>
                    <td>{{$record->getFields()['Notes']}}</td>
                    <td>{{$record->getFields()['Availability']}}</td>
                    <td>{{$record->getFields()['Last Modified']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="col-12 text-center" style="padding: 10px">
            <button style="width:200px;" class="no_print" onclick="printLabels()">Print Selected Assets</button>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
        <script src="https://cdn.datatables.net/fixedcolumns/4.0.1/js/dataTables.fixedColumns.min.js"></script>
        <script src="{{ asset('assets/node_modules/moment/moment.js') }}" type="text/javascript"></script>
        <script>
                var table;

                $.fn.dataTable.ext.search.push(
                        function (settings, data, dataIndex) {
                            var date_filter = $('.cfilter').val();

                            if (date_filter !== undefined && date_filter !== '') {

                                var date = moment(data[2]);
                                let diff = moment().diff(moment(date), 'days');
                                //console.log(moment().diff(moment(date),'days'));
                                if (diff <= date_filter) {
                                    return true;
                                } else
                                    return false;
                            } else
                                return true;
                        }
                );
        
                $(document).ready(function () {
                    table = $('#airtable').DataTable({
                        initComplete: function () {
                            this.api().columns([13]).every(function () {
                                var column = this;
                                var select = $('<select class="cfilter"><option value="">Warehouse Location: All</option></select>')
                                        .prependTo($('#airtable_filter'))
                                        .on('change', function () {
                                            var val = $.fn.dataTable.util.escapeRegex(
                                                    $(this).val()
                                                    );

                                            column
                                                    .search(val ? '^' + val + '$' : '', true, false)
                                                    .draw();
                                        });

                                column.data().unique().sort().each(function (d, j) {
                                    select.append('<option value="' + d + '">' + d + '</option>')
                                });
                            });
                            this.api().columns([2]).every(function () {

                                var select = $('<select class="cfilter"><option value="">Accepted Date: All</option></select>')
                                        .prependTo($('#airtable_filter'))
                                        .on('change', function () {
                                            table.draw();
                                        });

                                select.append('<option value="1">1 Day</option>');
                                select.append('<option value="7">7 Days</option>');
                                select.append('<option value="30">30 Days</option>');
                                select.append('<option value="90">90 Days</option>');

                            });
                        },
                        scrollX: true,
                        scrollCollapse: true,
                        fixedColumns: {
                            left: 2,
                        },
                        columnDefs: [{
                                orderable: false,
                                className: 'select-checkbox',
                                targets: 0
                            }],
                        select: {
                            style: 'multi',
                            selector: 'td:first-child'
                        },
                        order: [[1, 'asc']]
                    });

                });
                function printLabels() {
                    var count = table.rows({selected: true}).count();
                    if (count <= 0) {
                        alert('No selected assets! Nothing to print');
                        return 0;
                    }
                    let values = [];
                    for (let i = 0; i < table.rows({selected: true}).data().length; i++) {
                        values[i] = table.rows({selected: true}).data()[i][1];
                    }

                    $.ajax({
                        url: "/airtable",
                        type: "POST",
                        data: {
                            values: JSON.stringify(values)
                        },
                        success: function (response) {
                            console.log(response);
                            if (response) {
                                window.open('/airtable/show?values=' + JSON.stringify(values), '_blank');
                            }
                        },
                        error: function (error) {
                            alert("Error, try again later!");
                            console.log(error);
                        }
                    });
                }

        </script>
    </body>
</html>