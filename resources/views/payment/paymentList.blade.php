@extends('layout.layout')

@section('content')
@if(session('success'))
  <div class="alert alert-success">
      {{ session('success') }}
  </div>
@endif
@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
{{-- Payment Info Update --}}
<div class="container">
    <h2 class="page-title">Payment Information</h2>
    <a href="{{ route('payment.info.create') }}" class="btn btn-primary my-3">Add Payment Info</a>
    
    <table class="table">
        <thead>
            <tr>
                <th>Type</th>
                <th>No. of credits</th>
                <th>Price</th>
                <th>Bank Info</th>
                <th>Payment QR</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paymentInfos as $info)
                <tr>
                    <td class="text-capitalize">{{ $info->type }}</td>
                    <td>
                       {{$info->amount_of_credits  == 0 ? "N/A" : $info->amount_of_credits}}
                    </td>
                    <td>{{ $info->price }}</td>
                    <td>{{ $info->bank_info }}</td>
                    <td><img style="width:70px; height:70px;" src='{{ $info->payment_QR_url }}' alt="qrcode"></td>
                    <td>
                        <a href="{{ route('payment.info.edit', $info->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('payment.info.destroy', $info->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-secondary" onclick="return confirm('Are you sure you want to delete this payment term?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

 <!-- Transactions Section -->
 <h2 class="mt-5 page-title">Transactions</h2>

 <table class="table">
     <thead>
         <tr>
             <th>User Name</th>
             <th>User Email</th>
             <th>Payment Type</th>
             <th>No. of Credits</th>
             <th>Price</th>
             <th>Payment Status</th>
             <th>Payment Date</th>
         </tr>
     </thead>
     <tbody>
         @foreach ($transactions as $transaction)
             <tr>
                 <td>{{ $transaction->user->name }}</td>
                 <td>{{ $transaction->user->email }}</td>
                 <td class="text-capitalize">{{ $transaction->paymentInfo->type }}</td>
                 <td>{{ $transaction->paymentInfo->amount_of_credits == 0 ? "N/A" : $transaction->paymentInfo->amount_of_credits }}</td>
                 <td>{{ $transaction->paymentInfo->formatted_price }}</td>
                 <td>
                    <form action="{{ route('transactions.updateStatus', $transaction->id) }}" method="POST" class="status-form">
                        @csrf
                        @method('PUT')
                        
                        <select name="payment_status" class="form-control" data-current-status="{{ $transaction->payment_status }}" onchange="handleStatusChange(this)">
                            <option value="pending" {{ $transaction->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ $transaction->payment_status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="refunded" {{ $transaction->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </form>
                 </td>
                 <td>{{ \Carbon\Carbon::parse($transaction->payment_date)->format('d M Y H:i') }}</td>
             </tr>
         @endforeach
     </tbody>
 </table>
    <!-- Custom Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {{-- Previous Page Link --}}
            @if ($transactions->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">Previous</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $transactions->previousPageUrl() }}">Previous</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($transactions->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $transactions->nextPageUrl() }}">Next</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Next</span>
                </li>
            @endif
        </ul>
    </nav>

    <div class="text-center mt-3">
        Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}
    </div>
</div>

@endsection
@section('scripts')
<script>
    const handleStatusChange = (selectElement) => {
        const currentStatus = selectElement.dataset.currentStatus;
        
        if(confirm('Are you sure you want to update the status? Number of credits will be adjusted in student account.')) {
            selectElement.form.submit();
        } else {
            selectElement.value = currentStatus;
        }
    }
</script>
@endsection
