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
        <style>
            body {
                background: #17468f!important;
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

        </style>
    </head>

    <body onload="window.print()">
    @foreach ( $records as $record )
    @if (in_array($record->getFields()['iMed Asset ID'],json_decode($_GET['values'],true)))
    <page size="custom">
        <div class='barcode-label' data-row-id="{{$record->getId()}}">
            <div class="col-12 text-center">
                <img height="40" width="135" src="{{ asset('imedical.png') }}">
            </div>
        <br/>
            <div class="row">
                <div class="offset-1 col-6 text-left">
                    <h4 style="font-size: 14px;" >{{$record->getFields()['iMed Asset ID']}}</h4>
                {{$record->getFields()['Manufacturer']}}
                        {{$record->getFields()['Model Name']}}
                    	{{$airtable->getRecord('Categories',$record->getFields()['Asset Category'][0])->getFields()['Category']}}
                
                </div>
                <div class="col-5">
                    <img height="70" width="70" src="{{ asset('imedicalshop_qr.png') }}" />
                </div>
            </div>


        </div>
        <div class="page-break"></div>
    </page>
    @endif
 @endforeach
</body>
</html>