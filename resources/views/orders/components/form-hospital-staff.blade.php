{{-- Form Hospital/Staff/Contact Component
    Parameters:
    - $hospitals: Collection of hospitals
    - $order: Order model (optional)
--}}
<div class="order-form-section">
    <div class="order-form-section-header">
        <h5 class="order-form-section-title">
            <i class="fa fa-hospital"></i>
            Site & Contact Information
        </h5>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label @permission('add_hospital') class="add-modal modal-link" href="{{ route('admin.hospital.create')}}" @endpermission>Site<span class="required"> *</span></label>
                <select xdata-link="#staff" xdata-list='' class="select2 m-b-10 form-control" 
                        data-placeholder="@trans(Site): @trans(viewAll)" name="hospital" id="hospital" required="">
                    @foreach ($hospitals as $item)
                    <option {{selected(isset($order) ? $order->hospital_id : '',$item->id,$item->id)}} value="{{ $item->id }}">{{ ucwords($item->name) }}</option>   
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label @permission('add_hospital_staff') class="add-modal modal-link" target-modal="#staff-modal" href="{{ route('admin.hospital-staff.create')}}" @endpermission>@trans(Contact Name)<span class="required"> *</span></label>
                <select class="select2 m-b-10 form-control select-staff" 
                        data-placeholder="@trans(Select Staff)" name="staff" id="staff">
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>@trans(Contact Phone)<span class="required"> *</span></label>
                <input type="text" maxlength="20" value="{{isset($order->contact_phone) ? $order->contact_phone : ''}}" class="form-control" required="" id="contact_phone" name="contact_phone" />
            </div>
        </div>
    </div>
</div>

