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
    <h2>Edit Payment Info</h2>
    <form id="paymentForm" action="{{ route('payment.info.update', $paymentInfo->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="type">Type</label>
            <input type="text" name="type" class="form-control text-capitalize" value="{{ $paymentInfo->type }}" required>
        </div>

        <div class="form-group">
            <label for="price">Number of credits</label>
            <input type="number" name="amount_of_credits" class="form-control" value="{{ $paymentInfo->amount_of_credits }}" required step="1">
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" class="form-control" value="{{ $paymentInfo->price }}" required step="1">
        </div>
        <div class="form-group">
            <label for="bank_info">Bank Info</label>
            <input type="text" name="bank_info" id="bankInfo" class="form-control" value="{{ $paymentInfo->bank_info }}" required>
            <!-- Store original bank info -->
            <input type="hidden" id="originalBankInfo" value="{{ $paymentInfo->bank_info }}">
        </div>
        <div class="form-group">
            <label for="payment_QR_url">Payment QR URL</label>
            <input type="text" name="payment_QR_url" class="form-control" value="{{ $paymentInfo->payment_QR_url }}">
        </div>
        <input type="hidden" name="update_all" id="updateAll" value="no">
        <button type="button" class="btn btn-primary my-3 updateButton" id="updateButton">Update</button>
        <a href="{{route('payment.info.index')}}" class="btn btn-secondary my-3 mx-2">Cancel</a>
    </form>

     <!-- Confirmation Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Update Bank Info</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Do you want to update the bank account info for all other payment terms as well?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="updateCurrent">No, only this term</button>
                    <button type="button" class="btn btn-primary" id="updateAllTerms">Yes, update all</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
        const updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
        const updateBtn = document.querySelector('#updateButton.updateButton');        
        
        updateBtn.addEventListener('click', () => {
            const originalBankInfo = document.getElementById('originalBankInfo').value;
            const currentBankInfo = document.getElementById('bankInfo').value;

            if (originalBankInfo !== currentBankInfo) {
                updateModal.show();
            } else {
                document.getElementById('paymentForm').submit();
            }
        });

        document.getElementById('updateCurrent').addEventListener('click', () => {
            document.getElementById('updateAll').value = 'no';
            updateModal.hide();
            document.getElementById('paymentForm').submit();
        });

        document.getElementById('updateAllTerms').addEventListener('click', function () {
            document.getElementById('updateAll').value = 'yes';
            updateModal.hide();
            document.getElementById('paymentForm').submit();
        });
</script>
@endsection