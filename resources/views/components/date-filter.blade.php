<div class="date-filter-component">
    <div class="filter-type">
        <select id="filterType" class="form-select select2" name="filterType" onchange="toggleFilterOptions()">
            <option value="range">{{ $placeholder }}</option>
            <option value="year">By Year</option>
            <option value="month">By Month</option>
            <option value="quarter">By Quarter</option>
        </select>
    </div>

    <div class="filter-options">
        <div id="dateRange" class="filter-option">
            <input type="date" id="fromDate" name="fromDate" class="form-control" placeholder="From">
            <input type="date" id="toDate" name="toDate" class="form-control" placeholder="To">
        </div>

        <div id="yearFilter" class="filter-option" style="display: none;">
            <select id="year" name="year" class="form-select select2">
                @for($i = now()->year; $i >= 2000; $i--)
                    <option value="{{ $i }}">{{ $i }}</option>
                @endfor
            </select>
        </div>

        <div id="monthFilter" class="filter-option" style="display: none;">
            <select id="month" name="month" class="form-select select2">
                @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $key => $month)
                    <option value="{{ $key + 1 }}">{{ $month }}</option>
                @endforeach
            </select>
        </div>

        <div id="quarterFilter" class="filter-option" style="display: none;">
            <select id="quarter" name="quarter" class="form-select select2">
                <option value="1">Q1 (Jan-Mar)</option>
                <option value="2">Q2 (Apr-Jun)</option>
                <option value="3">Q3 (Jul-Sep)</option>
                <option value="4">Q4 (Oct-Dec)</option>
            </select>
        </div>
    </div>
</div>

<script>
    function toggleFilterOptions() {
        const filterType = document.getElementById('filterType').value;
        document.querySelectorAll('.filter-option').forEach(el => el.style.display = 'none');
        if (filterType === 'range') document.getElementById('dateRange').style.display = 'flex';
        if (filterType === 'year') document.getElementById('yearFilter').style.display = 'block';
        if (filterType === 'month') document.getElementById('monthFilter').style.display = 'block';
        if (filterType === 'quarter') {
            document.getElementById('yearFilter').style.display = 'block';
            document.getElementById('quarterFilter').style.display = 'block';
        }
    }
</script>

<style>
    .date-filter-component {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem; /* Spacing between components */
        align-items: center; /* Align items horizontally and vertically */
    }

    .filter-type, .filter-options {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem; /* Space between inputs */
        align-items: center; /* Align dropdowns and inputs */
    }

    .filter-option {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap; /* Allows wrapping if needed */
    }

    .form-control,
    .form-select,
    .select2-container--default .select2-selection--single {
        flex: 1; /* Ensure inputs take equal width */
        min-width: 150px; /* Minimum width to prevent overflow */
        max-width: 300px; /* Optional: Maximum width for consistency */
        box-sizing: border-box;
    }

    .select2-container {
        flex: 1; /* Make select2 dropdown full width */
        max-width: 300px;
        width: 100% !important; /* Override inline styles */
    }

    @media (max-width: 768px) {
        .date-filter-component {
            flex-direction: column; /* Stack filters vertically on smaller screens */
        }

        .filter-type, .filter-options {
            flex-direction: column; /* Stack options for better mobile UX */
        }

        .form-control,
        .form-select,
        .select2-container--default .select2-selection--single {
            max-width: 100%; /* Allow inputs to take full width on small screens */
        }
    }
</style>


