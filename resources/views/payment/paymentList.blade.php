@extends('layout.layout')

@section('content')
<div class="container">
    <h2>Payment Information</h2>
    <a href="{{ route('payment_info.create') }}" class="btn btn-primary">Add Payment Info</a>
    
    <table class="table">
        <thead>
            <tr>
                <th>Type</th>
                <th>Price</th>
                <th>Bank Info</th>
                <th>Payment QR URL</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paymentInfos as $info)
                <tr>
                    <td>{{ $info->type }}</td>
                    <td>{{ $info->price }}</td>
                    <td>{{ $info->bank_info }}</td>
                    <td>{{ $info->payment_QR_url }}</td>
                    <td>
                        <a href="{{ route('payment_info.edit', $info->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('payment_info.destroy', $info->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
