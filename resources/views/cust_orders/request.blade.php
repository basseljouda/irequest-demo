@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Request Part</h1>

    <form action="{{ route('cust_orders.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="company_id">Company</label>
            <input type="text" class="form-control" id="company_id" name="company_id" required>
        </div>
        <div class="form-group">
            <label for="hospital_id">Site</label>
            <input type="text" class="form-control" id="hospital_id" name="hospital_id" required>
        </div>
        <div class="form-group">
            <label for="contact_name">Contact Name</label>
            <input type="text" class="form-control" id="contact_name" name="contact_name" required>
        </div>
        <div class="form-group">
            <label for="contact_phone">Contact Phone</label>
            <input type="text" class="form-control" id="contact_phone" name="contact_phone" required>
        </div>
        <div class="form-group">
            <label for="contact_email">Contact Email</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" required>
        </div>
        <div class="form-group">
            <label for="date_needed">Date Needed</label>
            <input type="date" class="form-control" id="date_needed" name="date_needed" required>
        </div>
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea class="form-control" id="notes" name="notes"></textarea>
        </div>

        <h3>Part Details</h3>
        <div id="part-details">
            <div class="form-group">
                <label for="details[0][part_title]">Part Title</label>
                <input type="text" class="form-control" id="details[0][part_title]" name="details[0][part_title]" required>
            </div>
            <div class="form-group">
                <label for="details[0][part_oem]">Part OEM</label>
                <input type="text" class="form-control" id="details[0][part_oem]" name="details[0][part_oem]" required>
            </div>
            <div class="form-group">
                <label for="details[0][price_type]">Price Type</label>
                <input type="text" class="form-control" id="details[0][price_type]" name="details[0][price_type]" required>
            </div>
            <div class="form-group">
                <label for="details[0][qty]">Quantity</label>
                <input type="number" class="form-control" id="details[0][qty]" name="details[0][qty]" required>
            </div>
        </div>
        <button type="button" class="btn btn-secondary" onclick="addPartDetail()">Add Another Part</button>

        <button type="submit" class="btn btn-primary mt-4">Submit</button>
    </form>
</div>

<script>
    let partDetailIndex = 1;

    function addPartDetail() {
        const partDetailsDiv = document.getElementById('part-details');
        const newPartDetail = document.createElement('div');
        newPartDetail.innerHTML = `
            <div class="form-group">
                <label for="details[${partDetailIndex}][part_title]">Part Title</label>
                <input type="text" class="form-control" id="details[${partDetailIndex}][part_title]" name="details[${partDetailIndex}][part_title]" required>
            </div>
            <div class="form-group">
                <label for="details[${partDetailIndex}][part_oem]">Part OEM</label>
                <input type="text" class="form-control" id="details[${partDetailIndex}][part_oem]" name="details[${partDetailIndex}][part_oem]" required>
            </div>
            <div class="form-group">
                <label for="details[${partDetailIndex}][price_type]">Price Type</label>
                <input type="text" class="form-control" id="details[${partDetailIndex}][price_type]" name="details[${partDetailIndex}][price_type]" required>
            </div>
            <div class="form-group">
                <label for="details[${partDetailIndex}][qty]">Quantity</label>
                <input type="number" class="form-control" id="details[${partDetailIndex}][qty]" name="details[${partDetailIndex}][qty]" required>
            </div>
        `;
        partDetailsDiv.appendChild(newPartDetail);
        partDetailIndex++;
    }
</script>
@endsection
