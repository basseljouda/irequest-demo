<div class="col-12 r-panel-body mb-3 mt-3" >

    <table class="table table-borderless">
        <tr class="bg-whitesmoke">
            <!--th>
                Equipment Combo
            </th-->
            <th>
                Asset
            </th>
            <th>
                Latest Inspection Date
            </th>
            <th>
                Inspection Status
            </th>
            <th></th>
        </tr>
        @foreach ($order->equipments as $item)
        @if($item->assets != '')
        @foreach($order->viewAssets($item->id) as $asset)
        @if (is_array($item->removed_assets) && in_array($asset->id, $item->removed_assets))
        @continue;
        @endif
        <tr>
            <!--td>
                {{$item->equipment->name}}
                <input type="hidden" value="{{$item->equipment_id}}" class="eq_id" />
            </td-->
            <td>
                <div class="text-center">
                    {{$asset->name}}  
                </div>
            </td>
            <th>
                NA
            </th>
            <th>
                NA
            </th>
            <td>
                <a href="#"  class="btn btn-info btn-amazon">Print Sheet</a>
            </td>
        </tr>
        @endforeach
        @endif
        @endforeach
    </table>
</div>