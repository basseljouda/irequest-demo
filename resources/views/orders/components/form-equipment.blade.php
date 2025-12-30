{{-- Form Equipment Selection Component
    Parameters:
    - $order: Order model (optional)
--}}
<div class="order-equipment-section">
    <div class="order-equipment-card">
        <div class="order-equipment-card-header">
            <h5 class="order-equipment-card-title">
                <i class="fa fa-cogs"></i>
                @trans(Select) @trans(Equipment)<span class="required"> *</span>
            </h5>
        </div>
        <div class="order-equipment-card-body">
            <table id="eq_table" class="order-equipment-table">
                        @if (isset($order))

                        @php $i=0 @endphp
                        @foreach($order->equipments as $order_equipment)
                        <tr class="tr_clone">
                            <td class="equipment-select-cell">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <select class="select2 items m-b-10 form-control equipments select-combo" 
                                                data-eq="{{$order_equipment->equipment->id}}"       data-placeholder="@trans(Click to Select)" name="equipments[]" id="items">

                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input type="hidden" class="form-control" id="quantity" name="quantity[]" value ="1" placeholder="Qnt">
                                        <input type="hidden" class="eq-id" name="id[]" value ="{{$order_equipment->id}}">
                                    </div>
                                </div>
                            </td>
                            <td >
                                <!--div class="form-group">
                                    <div class="col-md-12">
                                        <input disabled="" type="text" value="{{isset($order_equipment->notes) ? $order_equipment->notes : ''}}" class="form-control" id="equipment_notes" name="" placeholder="Notes">
                                    </div>
                                </div-->
                            </td>
                            <td class="equipment-action-cell">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        @if ($i == 0)
                                        <button class="btn btn-success tr_clone_add" type="button"><i class="fa fa-plus"></i></button>
                                        @else
                                        <button class="btn tr_clone_remove btn-danger" type="button"><i class="fa fa-minus"></i></button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @php $i++ @endphp
                        @endforeach
                        @else

                        <tr class="tr_clone">
                            <td class="equipment-select-cell">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <select class="select2 items m-b-10 form-control equipments select-combo" 
                                                data-placeholder="@trans(Click to Select)" name="equipments[]" id="items">

                                        </select>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" id="quantity" name="quantity[]" value ="1" >
                                    </div>
                                </div>
                            </td>
                            <td >
                                <!--div class="form-group">
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" id="equipment_notes" name="equipment_notes[]" placeholder="Notes">
                                    </div>
                                </div-->
                            </td>
                            <td class="equipment-action-cell">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button class="btn btn-success tr_clone_add" type="button"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
            </table>
        </div>
    </div>
</div>

