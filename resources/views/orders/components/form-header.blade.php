{{-- Form Header Component (for edit mode)
    Parameters:
    - $order: Order model (optional, only shown if exists)
--}}
@isset($order)
<div class="order-form-header-section">
    <div class="order-form-section-header">
        <h5 class="order-form-section-title">
            <i class="fa fa-cog"></i>
            Order Management
            <span class="order-form-section-subtitle">Edit order status and dates</span>
        </h5>
    </div>
    
    <div class="order-form-section">
        <div class="row">
            <div class="col-md-4">
                <label>@trans(Status)</label>
                <div class="form-group">
                    <select name="status" id="filter_status" class="select2 form-control">
                        @foreach (config('constant.orders') as $key => $value)
                        <option {{selected($key,$order->status)}} value="{{ $key }}">{{ ucwords($value) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <label class="text-danger">@lang('Bill Start Date')</label>
                <div class="form-group">
                    <input type="text" value="{{isset($order->bill_started) ? dformat($order->bill_started) : ''}}" class="form-control datepiktime" id="bill_started" name="bill_started">
                </div>
            </div>
            <div class="col-md-4">
                <label class="text-danger">@trans(Bill End Date)</label>
                <div class="form-group">
                    <input type="text" value="{{isset($order->bill_completed) ? dformat($order->bill_completed,true) : ''}}" class="form-control datepik" id="bill_completed" name="bill_completed">
                </div>
            </div>
        </div>
        @role('superadmin')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="exclude_from_report" id="exclude_from_report" {{isset($order->exclude_from_report)&&$order->exclude_from_report==1?'checked':''}}>
                        <label class="form-check-label label-brown" for="exclude_from_report">
                            Exclude order delivery time from Report
                        </label>
                    </div>
                </div>
            </div>
        </div>
        @endrole
    </div>
    
    <div class="order-form-section">
        <div class="row">
            <div class="col-md-3">
                <label>@trans(Delivery)</label>
                <div class="form-group">
                    <input type="text" value="{{isset($order->delivered_at) ? dformat($order->delivered_at) : ''}}" class="form-control datepiktime" id="delivered_at" name="delivered_at">
                </div>
            </div>
            <div class="col-md-3">
                <label>@trans(Accepted)</label>
                <div class="form-group">
                    <input type="text" value="{{isset($order->accepted_at) ? dformat($order->accepted_at) : ''}}" class="form-control datepiktime" id="accepted_at" name="accepted_at">
                </div>
            </div>
            <div class="col-md-3">
                <label>@trans(Picked Up)</label>
                <div class="form-group">
                    <input type="text" value="{{isset($order->picked_at) ? dformat($order->picked_at,true) : ''}}" class="form-control datepik" id="picked_at" name="picked_at">
                </div>
            </div>
            <div class="col-md-3">
                <label>@trans(Reassigned)</label>
                <div class="form-group">
                    <input type="text" value="{{isset($order->reassigned_at) ? dformat($order->reassigned_at) : ''}}" class="form-control datepiktime" id="reassigned_at" name="reassigned_at">
                </div>
            </div>
        </div>
    </div>
</div>
@endisset

