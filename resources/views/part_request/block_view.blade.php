<div class="row">
    @foreach ($partRequests as $request)
    @php
        $rfq = 0;
        foreach ($request->details as $detail) {
            if ($detail->part_price == '' || $detail->part_price == 0) {
                $rfq++;
            }
        }
        $cardHeaderColor = '#304257';
    if ($request->status == 'completed')
    {
        $cardHeaderColor = '#62bf81';
    }else if ($request->status == 'RFQ Rejected')
    {
        $cardHeaderColor = '#dc3545';
    }
    @endphp
    
    <div class="col-md-6">
        <div class="card mb-4" style="height: auto;border:1px solid #62bf81" id="request-{{$request->id}}">
            <div class="card-header d-flex justify-content-between align-items-center" style="background-color: {{$cardHeaderColor}} !important">
                <span style="position: absolute;font-size:120%">#{{ $request->id }}</span>
                @if ($request->deleted_at == null)
                    <div class="status mx-auto" style="font-size: 16px;">
                        @if ($request->status == "RFQ Requested")
                            @if (auth()->user()->can('reply_rfq'))
                                <button class="act-btn btn btn-warning reply-rfq-btn" data-toggle="modal" data-target="#replyRfqModal{{ $request->id }}">Reply RFQ</button>
                            @else
                                <span class="badge badge-warning">Waiting RFQ Reply</span>
                            @endif
                        @elseif ($request->status == "RFQ Rejected")
                            <span class="badge badge-danger">RFQ Rejected</span>
                        @elseif ($request->orderRequest->count() > 0)
                            <span class="badge badge-primary">
                                <b>{{ strtoupper($request->status) }}</b>
                            </span>
                        @elseif ($request->status == "replied")
                            @permission('accept_rfq_part_request')
                                <button type="button" class="act-btn btn text-success text-bold request-new-btn" data-id="{{ $request->id }}" onclick="window.location.href='{{ url('part_request/create-order/'.$request->id) }}'">Accept</button>
                                <button class="act-btn btn text-danger" data-toggle="modal" data-target="#rejectRfqModal{{ $request->id }}">Reject All</button>
                            @else
                                <strong class='badge badge-info'>Waiting RFQ Acceptance</strong>
                            @endpermission
                        @elseif ($request->status == "new")
                            @if ($rfq > 0)
                                @permission('rfq_part_request')
                                    <a href="javascript:;" class="act-btn btn btn-secondary btn-secondary-outline request-rfq-btn" data-id="{{ $request->id }}">Request RFQ</a>
                                    @else
                                    <a href="javascript:;" onClick="swal('You don\'t have the right permissions to request an RFQ')" class="btn btn-secondary btn-secondary-outline" title="You dont't have permission to Request RFQ!">Request RFQ</a>
                                @endpermission
                            @else
                            @permission('accept_rfq_part_request')
                                <button type="button" class="act-btn btn text-primary request-new-btn" data-id="{{ $request->id }}" onclick="window.location.href='{{ url('part_request/create-order/'.$request->id) }}'">Create Order</button>
                                @else
                                <button type="button" class="act-btn disabled btn text-primary request-new-btn" data-id="{{ $request->id }}" >Create Order</button>
                                @endpermission
                            @endif
                        @endif
                    </div>
                
                    <div class="dropdown hide" style="position: absolute; right: 0;">
                        <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton{{ $request->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            &#x22EE;
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton{{ $request->id }}">
                           
                            @role('superadmin')
                                <form action="{{ route('part_request.destroy', $request->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this part request?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">Delete</button>
                                </form>
                            @endrole
                        </div>
                    </div>
                @endif
            </div>
            <div class="card-body" style="overflow-y: auto;">
                @if (isset($request->rfqRequestedBy->name) && $request->orderRequest->count() == 0)
                <p class="topstatus text-center small">RFQ Requested By: <b>{{$request->rfqRequestedBy->name}}</b> On {{dformat($request->rfq_requested_on)}}</p>
                @endif
                @if ($request->status=='RFQ Rejected')
                    @isset($request->rfqRejectedBy->name)
                    <p class="topstatus text-left bg-whitesmoke" style="border:1px solid gray;padding: 10px">
                        Rejected By: <b>{{$request->rfqRejectedBy->name}}</b> On {{dformat($request->rfq_rejected_on)}}
                        <br/>
                        <b>Reason:</b> {{$request->rfq_reject_reason}}
                    </p>
                    @endisset
                @endif
                <p>
                    <strong style="font-size: 100%">{{ $request->hospital->name }}</strong>
                </p>
                <p><strong>Biomed Contact:</strong> {{ $request->contact_name }}<br/>
                    <small>Email: {{ $request->contact_email }} Phone: {{ $request->contact_phone }}</small> </p>
                @if ($request->orderRequest->count() == 0)
                <div class="request-parts">
                <h5><strong>Parts:</strong></h5>
                <ul>
                    @foreach ($request->details as $detail)
                    <li>{{ $detail->part_title }} (Requested Qty:{{ $detail->qty }})
                        @if(!is_null($detail->available_qty ) && $detail->available_qty >= 0 && $detail->available_qty < $detail->qty)
                        <strong class="nowrap"> Available Qty:{{$detail->available_qty}}</strong>
                        @endif
                        @if ($detail->delay_reason != 'NA' && $detail->delay_reason != '')
                        <p><small><u>Delay Reason: {{$detail->delay_reason}}</u></small></p>
                        @endif 
                        @if ($detail->part_price > 0)
                        <p style="color: darkgreen">{{ ucfirst($detail->price_type) . ' Price: $' . $detail->part_price }}</p>
                        @else
                        @if (!$detail->is_alternative_accepted)
                        <p>{{ ucfirst($detail->price_type) . ' Price: -- ' }}</p>
                        @else
                        <p>
                            <strong class='text-primary'>Alternative Replacement:<br/>
                                <span style="color:var(--main-color) !important">
                                    {{$detail->alternative_part_oem.' | '.$detail->alternative_part_title}}<br/>
                                    <u>{{ucwords($detail->alternative_part_condition).': $'.$detail->alternative_part_price}}</u>
                                </span>
                            </strong>
                        </p>
                        @endif
                        @endif
                    </li>
                    @endforeach
                </ul>
                <p><strong>Date Needed:</strong> {{ dformat($request->date_needed, true) }}</p>
                <p><strong>Notes:</strong> {{ $request->notes }}</p>
                </div>
                @else
                @if ($request->rma && count($request->rma->where("status","!=","completed")) > 0)
                <h6 class="text-primary"><strong>RMA Requests:</strong></h6>
                <table class="table table-nohover">
                    @foreach ($request->rma->where("status","!=","completed") as $rma)
                    <tr style="border:1px solid #ddd;background: whitesmoke">
                        <td>RMA#{{$rma->id}}</td>
                        <td>{{dformat($rma->created_at,true)}}</td>
                        
                        <td class="{{$rma->status=='approved'?'text-danger':''}} text-bold"><u>{{ ucwords(config('constant.rma_orders.'.$rma->status)) }}</u></td>
                        @permission('request_rma')
                        <td>
                            <a href="javascript:;" onclick="showOrderRMA({{$rma->id}},true)" class=" mt-1"><i class="fa fa-arrow-right">&nbsp;</i>Expand</a>
                        </td>
                            <td>
                                 @if ($rma->customer_reply_status == 1)
                                <a href='{{ route('part_request.resolveModal',$rma->id) }}' target-modal='#application-lg-modal' class="modal-link text-danger pulse" title="Click here to take action" ><i class="fa fa-exclamation-triangle">&nbsp;</i>CLICK TO RESOLVE</a>
                                @endif
                            </td>
                        
                        @endpermission
                    </tr>
                    @endforeach
                </table>
                <hr/>
                @endif
                <div class="order-parts">
                    <h5><strong>Order Details:</strong></h5>
                        <table class="table table-nohover">
                            @foreach($request->orderRequest as $order)
                            <tbody style="border:1px ridge #a6a2a2 !important">
                                <tr style="font-weight: 600;background: whitesmoke !important" class="order_part_tr nowrap">
                                <td>
                                    #{{$order->getCustomID()}}
                                </td>
                                <td>{{$order->updated_at->format('m/d/Y')}}</td>
                                <td style="text-transform: uppercase">{{config('constant.cust_orders.'.$order->status)}}</td>
                                <td>
                                @if ($order->is_partial)
                                <span class="text-primary">Partial Shipment</span>
                                @else
                                    @if ($order->status == 'pending' || $order->status == 'fulfill')
                                     @permission('edit_order_request')
                                    <a class="btnbtn" href="{{ route('part_request.edit_order', $order->id) }}"><i class="fa fa-edit">&nbsp;</i>Edit</a>
                                    @endpermission
                                    @else
                                    <span class="text-success">Full Shipment</span>
                                    @endif
                               
                                    @permission('request_rma')
                            @if ($order->status=='completed')
                                
                                    <a class="btnbtn text-danger ml-3" href="{{ route('part_request.rma_order', $order->id) }}"><i class="fa fa-undo">&nbsp;</i>Request RMA</a>
                                
                            @endif
                            @endpermission
                                @endif
                                
                                
                                    @if ($order->shipping_delay_reason != '' && $order->status=='processing')
                                    Delayed: <span class='text-danger'>{{ucwords($order->shipping_delay_reason)}}</span>
                                    @endif
                                    <!--a href="#" onclick="showOrderRequest({{$order->id}},true)" class=" mt-1">Expand</a-->
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    @if($order->status=='shipped' && isset($order->shipment->tracking_number))
                                    <p class="text-center"><a href='{{ route('part_request.showTracking',[$order->shipment->tracking_number,$order->id]) }}' target-modal='#application-lg-modal' class="modal-link show-tracking" title="Click here to open the tracking url" >Shipping Tracking Status: <span class="text-success text-danger">{{$order->shipment->tracking_status}}</span></a></p>
                                    @endif
                                    <ul>
                                    @foreach($order->details as $detail)
                                    <li style="margin-bottom:5px">{{ $detail->part_oem.' '.$detail->part_title.' '}} <b>Requested Qty:</b> {{$detail->qty}}
                                        
                                        <span style="background: whitesmoke;padding: 2px;">
                                            {{ucwords($detail->price_type). ' $'.$detail->part_price}}
                                            @if ($order->status != 'pending' && $order->status != 'fulfill')
                                                @if (strtolower($detail->delay_reason) != 'na')
                                                <b style="padding: 5px;">{{$detail->delay_reason}}</b>
                                                @endif
                                                @if ($order->status == 'shipped' || $order->status == 'delivered' || $order->status == 'completed')
                                                <b class="text-success"> - Shipped: {{ $detail->available_qty.'/'. $detail->qty}}</b>
                                                @else
                                                <b> - Shipping: {{ $detail->available_qty.'/'. $detail->qty}}</b>
                                                @endif
                                                <b> - Total: ${{number_format($detail->part_price * $detail->available_qty,2)}}</b>
                                            @endif
                                            @if ($detail->rma_qty > 0)
                                            <label class="text-danger text-bold ml-3">RMA Qty: {{$detail->rma_qty}}</label>
                                            @endif
                                        </li>
                                    @endforeach
                                    </ul>
                                </td>
                            </tr>
                            </tbody>
                            @endforeach
                        </table>
                     
                </div>
                @endif
            </div>
            @if ($request->deleted_at == null)
            <div class="d-flex justify-content-center mb-4">
                <small>{{ dformat($request->created_at) . ' by ' . $request->user->name }}</small>
            </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

 @foreach ($partRequests->where('status','RFQ Requested') as $request)
<!-- Modal -->
<div class="modal fade replyRfqModal" id="replyRfqModal{{ $request->id }}" tabindex="-1" role="dialog" aria-labelledby="replyRfqModalLabel{{ $request->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('part_request.reply_rfq', $request->id) }}" method="POST" class="ajax-form">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="replyRfqModalLabel{{ $request->id }}">Reply RFQ for Request #{{ $request->id }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                    @foreach ($request->details as $detail)
                    @if ($detail->part_price <= 0 )
                    <div class="form-group">
                        <label for="part_price_{{ $detail->id }}">{{ $detail->part_title }} <small> (Reference Price: {{'$'.($detail->psprice??0)}})</small></label>
                        <input type="number" class="form-control part-price" id="part_price_{{ $detail->id }}" name="part_prices[{{ $detail->id }}]" value="" required="" step="0.01" min="1" placeholder="Enter Part Price">
                    </div>
                    
                    <!-- Placeholder for dynamically added alternative items -->
                    <div id="alternative-items-{{ $detail->id }}"></div>
                    
                    <!-- Add Alternative Button for Each Item -->
                    <button type="button" class="btn btn-secondary add-alternative-btn mb-3" data-detail-id="{{ $detail->id }}">Add Alternative Part</button>
                    <button type="button" class="btn btn-danger cancel-alternative-btn d-none mb-3" data-detail-id="{{ $detail->id }}">Cancel Alternative Part</button>
                    @endif
                    <br/>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Reply</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endforeach
@foreach ($partRequests->where('status','replied') as $request)
<div class="modal fade" id="rejectRfqModal{{ $request->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectRfqModalLabel{{ $request->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('part_request.reject_rfq', $request->id) }}" method="POST" class="ajax-form">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="replyRfqModalLabel{{ $request->id }}">Please enter the Rejection Reason</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body"> 
                    <div class="form-group">
                        <textarea name="reject_reason" class="form-control" id="reject_reason" required="" placeholder="@trans(Reject Reason)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach


<div class="pagination">
    {{ $partRequests->links() }}
</div>