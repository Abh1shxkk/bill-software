{{-- Doctor Wise Customers Report --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <h5 class="mb-0">Doctor Wise Patient</h5>
        </div>
        <div class="card-body" style="background-color: #f0f0f0; border-radius: 0;">
            <form id="filterForm" method="GET">
                <div class="row g-2 mb-2">
                    <div class="col-auto">
                        <label class="col-form-label col-form-label-sm">Selective Doctor [ Y/N ]:</label>
                    </div>
                    <div class="col-1">
                        <input type="text" name="selective" class="form-control form-control-sm text-uppercase" 
                               value="{{ request('selective', 'Y') }}" maxlength="1" style="width: 40px;">
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-2">
                        <label class="col-form-label col-form-label-sm">Doctor:</label>
                    </div>
                    <div class="col-4">
                        <select name="doctor" class="form-select form-select-sm">
                            <option value="00">00 - All Doctors</option>
                            @foreach($doctors as $index => $doctor)
                                <option value="{{ $doctor }}" {{ request('doctor') == $doctor ? 'selected' : '' }}>
                                    {{ $index + 1 }} - {{ $doctor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr>
                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" name="view" value="1" class="btn btn-primary btn-sm">View</button>
                        <button type="button" onclick="window.close()" class="btn btn-secondary btn-sm">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($reportData->count() > 0)
    <div class="card mt-3">
        <div class="card-header" style="background-color: #ffc4d0; font-style: italic; font-family: 'Times New Roman', serif;">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Doctor Wise Patient List</h6>
                <button type="button" onclick="printReport()" class="btn btn-sm btn-outline-dark">Print</button>
            </div>
        </div>
        <div class="card-body p-0">
            @foreach($reportData as $doctorData)
            <div class="mb-3">
                <div class="bg-light p-2 border-bottom">
                    <strong>Doctor: {{ $doctorData['doctor_name'] }}</strong>
                    <span class="badge bg-primary ms-2">{{ count($doctorData['patients']) }} Patients</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-striped mb-0" style="font-size: 0.75rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">S.No</th>
                                <th>Patient Name</th>
                                <th>Customer Name</th>
                                <th>Code</th>
                                <th>Mobile</th>
                                <th>Address</th>
                                <th>Prescription Date</th>
                                <th>Validity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($doctorData['patients'] as $index => $patient)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $patient['patient_name'] }}</td>
                                <td>{{ $patient['customer_name'] }}</td>
                                <td>{{ $patient['customer_code'] }}</td>
                                <td>{{ $patient['customer_mobile'] }}</td>
                                <td>{{ $patient['customer_address'] }}</td>
                                <td>{{ $patient['prescription_date'] ? $patient['prescription_date']->format('d-m-Y') : '' }}</td>
                                <td>{{ $patient['validity_date'] ? $patient['validity_date']->format('d-m-Y') : '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @elseif(request()->has('view'))
    <div class="alert alert-info mt-3">No records found matching the criteria.</div>
    @endif
</div>

<script>
function printReport() {
    window.open('{{ route("admin.reports.other.doctor-wise-customers") }}?print=1&' + $('#filterForm').serialize(), '_blank');
}
</script>
@endsection
