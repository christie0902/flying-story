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

<div class="container">
    <h2>Payment Information</h2>
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
@endsection
