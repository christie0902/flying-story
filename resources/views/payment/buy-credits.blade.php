@extends('layout.layout')

@section('content')
    <div class="container">
        <h2>Buy Credits</h2>

       

        <!-- Select Credit Option -->
        <div class="form-group">
            <label for="creditOption">Select Credits Package (1/4/8)</label>
            <select id="creditOption" class="form-control">
                <option value="" disabled selected>Select credits...</option>
                @foreach($paymentInfo as $info)
                    <option value="{{ $info->id }}" data-price="{{ $info->price }}" 
                            data-bank-info="{{ $info->bank_info }}" data-qr="{{ $info->payment_QR_url }}">
                        {{ $info->amount_of_credits }} credits
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Display QR Code -->
        <div id="qrCodeSection" class="mt-4" style="display: none;">
            <img id="qrCodeImage" src="" alt="Payment QR Code" class="img-fluid mb-3" style="max-width: 300px;">
            
            <p><strong>Price:</strong> $<span id="priceText"></span></p>
            <p><strong>Bank Info:</strong> <span id="bankInfoText"></span></p>
        </div>
        <!-- Display the user's payment variable -->
        <h5 class="text-primary font-weight-bold">
           <mark> Please include your payment variable symbol in the transaction: <strong class="text-primary">{{ $userProfile->payment_variable }}</strong> 
            so that we can track your payment.</mark>
        </h5>
        <!-- Payment Buttons -->
        <div class="mt-4">
            <button id="completePaymentButton" class="btn btn-primary">I have completed the payment</button>
            <a href="{{ route('calendar.show') }}" class="btn btn-secondary">Cancel</a>
        </div>

        <!-- Confirmation Modal -->
        <div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Payment</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Did you complete the payment?</p>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('confirm.payment') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </form>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.getElementById('creditOption').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        
        // Get the relevant data from the selected option
        const price = selectedOption.getAttribute('data-price');
        const bankInfo = selectedOption.getAttribute('data-bank-info');
        const qrCodeUrl = selectedOption.getAttribute('data-qr');
        
        // Update the QR code, price, and bank info on the page
        document.getElementById('priceText').textContent = price;
        document.getElementById('bankInfoText').textContent = bankInfo;
        document.getElementById('qrCodeImage').src = qrCodeUrl;
        
        document.getElementById('qrCodeSection').style.display = 'block';
    });

    document.getElementById('completePaymentButton').addEventListener('click', function () {
        $('#confirmationModal').modal('show');
    });
</script>
@endsection
