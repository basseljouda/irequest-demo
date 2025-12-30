{{-- Form Patient Info Component
    Parameters:
    - $cost_centers: Collection of cost centers
    - $order: Order model (optional)
--}}
<div class="order-form-section">
    <div class="order-form-section-header">
        <h5 class="order-form-section-title">
            <i class="fa fa-user"></i>
            Patient & Location Information
        </h5>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>@trans(Patient Name/ID)<span class="required"> *</span></label>
                <input type="text" maxlength="199" value="{{isset($order->patient_name) ? $order->patient_name : ''}}" class="form-control" id="patient_name" name="patient_name" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>@trans(Room No)<span class="required"> *</span></label>
                <input type="text" maxlength="20" value="{{isset($order->room_no) ? $order->room_no : ''}}" class="form-control" id="room" name="room" />
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label>@trans(Floor / Unit)<span class="required"> *</span></label>
                <input type="text" maxlength="20" value="{{isset($order->unit_floor) ? $order->unit_floor : ''}}" class="form-control" id="unit_floor" name="unit_floor">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label @permission('add_cost_center')class="add-modal modal-link" href="{{ route('admin.costcenter.create')}}" @endpermission>@trans(Cost Center)<span class="required"> *</span></label>
                <select class="select2 m-b-10 form-control" required="" 
                        data-placeholder="@trans(Select) @trans(Cost Center)" name="cost_center" id="cost_center">
                    <option value=""></option>
                    @foreach ($cost_centers as $item)
                    <option {{selected(isset($order) ? $order->cost_center_id : 174,$item->id)}} value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>@trans(Date Needed)<span class="required"> *</span></label>
                <input required="" maxlength="" value="{{isset($order->date_needed) ? dformat($order->date_needed,true) : ''}}" type="text" class="form-control datepik" id="date_needed" value="" name="date_needed" placeholder="@trans(Select date)">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>@trans(Customer) @trans(Notes)</label>
                <textarea name="notes" class="form-control notes-textarea">{{isset($order->notes) ? $order->notes : ''}}</textarea>
            </div>
        </div>
    </div>
</div>

