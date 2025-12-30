<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Order Acceptance Note</title>

        <style>
            .invoice-box {
                max-width: 800px;
                margin: auto;
                padding: 30px;
                border: 1px solid #eee;
                box-shadow: 0 0 10px rgba(0, 0, 0, .15);
                font-size: 16px;
                line-height: 24px;
                font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
                color: #555;
            }

            .invoice-box table {
                width: 100%;
                line-height: inherit;
                text-align: left;
            }

            .invoice-box table td {
                padding: 5px;
                vertical-align: top;
            }

            .invoice-box table tr td:nth-child(2) {
                text-align: right;
            }

            .invoice-box table tr.top table td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.top table td.title {
                font-size: 16px;

                color: #333;
            }

            .invoice-box table tr.information table td {
                padding-bottom: 40px;
            }

            .invoice-box table tr.heading td {
                background: #eee;
                border-bottom: 1px solid #ddd;
                font-weight: bold;
                text-align: left;
            }

            .invoice-box table tr.details td {
                padding-bottom: 20px;
            }

            .invoice-box table tr.item td{
                border-bottom: 1px solid #eee;
                padding-bottom: 20px;
            }

            .invoice-box table tr.item.end td{

                border: 1px solid #eee;
            }

            .invoice-box table tr.item.last td {
                border-bottom: none;
            }

            .invoice-box table tr.total td:nth-child(2) {
                border-top: 2px solid #eee;
                font-weight: bold;
            }

            @media only screen and (max-width: 600px) {
                .invoice-box table tr.top table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }

                .invoice-box table tr.information table td {
                    width: 100%;
                    display: block;
                    text-align: center;
                }
            }

            /** RTL **/
            .rtl {
                direction: rtl;
                font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            }

            .rtl table {
                text-align: right;
            }

            .rtl table tr td:nth-child(2) {
                text-align: left;
            }
            .main-table > tbody > tr.item > td, .main-table > tbody > tr.heading > td{
                padding: 5px;
            }
            .main-table > tbody > tr:last-child > td:first-child{
                padding-bottom: 120px;
            }
        </style>
    </head>

    <body>
        <div class="invoice-box">
            <table cellpadding="0" cellspacing="0" class="main-table">
                <tr class="top">
                    <td colspan="3">
                        <table>
                            <tr>
                                <td class="title">
                                    <img style="width: 150px" src="{{ asset('imedical_2024.png') }}" alt="iMediacal logo"><br/>
                                </td>

                                <td>
                                    <strong>Order #: {{$order->id}}</strong><br>
                                    Delivered: {{dformat($order->delivered_at)}}<br>
                                    <strong>Billing Started</strong>: {{dformat($order->bill_started,true)}}<br/>
                                    <strong>Billing Closed</strong>: {{isset($order->bill_completed) ? dformat($order->bill_completed,true) : '______________'}}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="information">
                    <td colspan="3">
                        <table>
                            <tr>
                                <td>
                                    {{$hospital->name}}<br>
                                    {{$hospital->address}}<br>
                                    {{$hospital->city.', '.$hospital->state.' '.$hospital->zip}}
                                </td>

                                <td>
                                    Patient: {{$order->patient_name}}<br>
                                    Room: {{$order->room_no}}<br>
                                    Unit/Floor: {{$order->unit_floor}}<br/>
                                    Cost Center: {{$order->costcenter->name}}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="heading">
                    <td>
                        Description
                    </td>

                    <td>
                        DOT#
                    </td>

                    <td>
                        Serial
                    </td>
                </tr>
                @foreach ($order->equipments as $item)
                <tr class="item end">
                    <td>
                        {{$item->equipment->name.' - '.$item->equipment->modality->name.' - '.$item->equipment->sub_modality->name}}
                    </td>

                    <td style="text-align:  left">
                        {{$serial->findorfail($item->serial_no)->dot_value}}
                    </td>

                    <td>
                        {{$serial->findorfail($item->serial_no)->serial_value}}
                    </td>
                </tr>
                @endforeach
            </table>
            <table cellpadding="0" cellspacing="0">
                <tr class="details">
                    <td colspan="4"></td>
                </tr>
                <tr class="heading" >
                    <td colspan="4" style="background: #fff">
                        Notes
                    </td>
                </tr>
                <tr class="item">
                    <td colspan="4" style="padding-bottom: 90px;border: 1px solid #eee">
                        {{$order->sign_notes}}
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center">
                        Accepted by:
                        <b>{{' '.isset($order->staffaccepted->firstname) ? ($order->staffaccepted->firstname.' '.$order->staffaccepted->lastname) : ''}}</b>
                        <span style="font-size:11px">{{$order->staffaccepted->email}}</span>
                        <br/>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center">
                        @isset($order->staffaccepted->firstname)
                        <img width="60%" class="img-signature" src="{{asset ('user-uploads/signatures/'.$order->staff_signature)}}" />
                        @endisset
                        <br/>
                        <span style="font-size:10px">(Disgital Signature)</span>
                    </td>
                </tr>          
            </table>
        </div>
    </body>
</html>