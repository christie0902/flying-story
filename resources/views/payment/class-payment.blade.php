@extends('layout.layout')

@section('content')
    <div class="container mt-5"> 
        <div class="card shadow-sm">
            <div class="card-body mx-md-5">
                <h2 class="card-title text-center mb-4 my-2 page-title">Class Payment</h2>
            @if ($paymentInfo)
                <h3 class="sub-title">Payment for {{ $paymentInfo->first()->type }}</h3>
                <!-- Select Credit Option -->
                @if (count($paymentInfo) > 1)
                <div class="form-group">
                    
                    <label for="paymentOption" class="fw-bold mb-2 form-label">Select Payment Term</label>
                    <select id="paymentOption" class="form-control w-m-50 form-label">
                        <option value="" disabled selected>Select payment option...</option>
                        @foreach($paymentInfo as $info)
                            <option value="{{ $info->id }}" data-price="{{ $info->formatted_price }}" 
                                    data-bank-info="{{ $info->bank_info }}" data-qr="{{ $info->payment_QR_url }}">
                                {{ $info->amount_of_credits }} credits
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Display QR Code -->
                <div id="qrCodeSection" class="mt-3" style="display: none;">
                    <img id="qrCodeImage" src="" alt="Payment QR Code" class="img-fluid mb-4" style="max-width: 300px;">
                    <p class="sub-title fs-3"><strong>Price:</strong> <span id="priceText"></span></p>
                    <p class="sub-title"><strong>Bank Info:</strong> <span id="bankInfoText"></span></p>
                </div>

                <!-- Display the user's payment variable -->
                <h5 class="text-danger font-weight-bold mt-4 lh-lg form-label">
                    <mark>Please include your payment variable symbol: 
                        <strong class="text-primary">{{ $userProfile->payment_variable }}</strong>  in the transaction and click on <strong class="text-primary">"I have paid"</strong> after you complete the payment.
                    </mark>
                </h5>

               
                @elseif ($paymentInfo->count() == 1)
                <!-- If there's only one payment option -->
                    @php
                        $info = $paymentInfo->first();
                    @endphp
                    <div id="qrCodeSection" class="mt-3">
                        <img id="qrCodeImage" src="{{ $info->payment_QR_url }}" alt="Payment QR Code" class="img-fluid mb-4" style="max-width: 300px;">
                        <p class="sub-title fs-3"><strong>Price:</strong> {{ $info->formatted_price }}</p>
                        <p class="sub-title"><strong>Bank Info:</strong> {{ $info->bank_info }}</p>
                    </div>

                @else
                    <p class="text-danger">No payment information available.</p>
                @endif

                <!-- Display the user's payment variable -->
                <h5 class="text-danger font-weight-bold mt-4 lh-lg form-label">
                    <mark>Please include your payment variable symbol: 
                        <strong class="text-primary">{{ $userProfile->payment_variable }}</strong>  
                        in the transaction and click on <strong class="text-primary">"I have paid"</strong> after you complete the payment.
                    </mark>
                </h5>

             <!-- Payment Buttons -->
             <div class="d-flex justify-content-md-start justify-content-center mt-5 mb-4">
                <button id="completePaymentButton" class="btn btn-primary px-sm-5 sub-title">I have paid</button>
                <a href="{{ route('calendar.show') }}" class="btn btn-secondary mx-4 px-sm-5 sub-title">Cancel</a>
            </div>
        </div> 
    </div>
        @else
            <p class="text-danger">No payment information available for this lesson.</p>
        @endif
        <!-- Confirmation Modal -->
        <div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Payment</h5>
                    </div>
                    <div class="modal-body">
                        <p>Did you complete the payment?</p>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('confirm.payment') }}" method="POST" id="confirmPaymentForm">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            <input type="hidden" name="payment_info_id" id="paymentInfoId" value="">
                            <button type="submit" class="btn btn-primary">Confirm</button>
                        </form>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Class Registration Confirmation Modal -->
        <div id="registrationModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Registration</h5>
                    </div>
                    <div class="modal-body">
                        <p id="lessonDetailsText"></p>
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('payment.register.lesson') }}" method="POST" id="registerLessonForm">
                            @csrf
                            <input type="hidden" name="lesson_id" id="lessonIdInput">
                            <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                            <button type="submit" class="btn btn-primary">Yes, Register me</button>
                        </form>
                        <button type="button" class="btn btn-secondary" id="noThanksButton">No, Thanks</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Passing lesson data --}}
        <div id="lessonData" 
            data-lesson-id="{{ $lesson ? $lesson->id : 'null' }}" 
            data-lesson-title="{{ $lesson ? $lesson->title : 'null' }}" 
            data-lesson-schedule="{{ $lesson ? $lesson->formatted_schedule : 'null' }}">
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const paymentOptionSelect = document.getElementById('paymentOption');

if (paymentOptionSelect) {
    paymentOptionSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];

        const price = selectedOption.getAttribute('data-price');
        const bankInfo = selectedOption.getAttribute('data-bank-info');
        const qrCodeUrl = selectedOption.getAttribute('data-qr');
        const paymentInfoId = selectedOption.value;

        const priceText = document.getElementById('priceText');
        const bankInfoText = document.getElementById('bankInfoText');
        const qrCodeImage = document.getElementById('qrCodeImage');
        const qrCodeSection = document.getElementById('qrCodeSection');

        if (priceText) {
            priceText.textContent = price;
        }

        if (bankInfoText) {
            bankInfoText.textContent = bankInfo;
        }

        if (qrCodeImage) {
            qrCodeImage.src = qrCodeUrl;
        }

        if (qrCodeSection) {
            qrCodeSection.style.display = 'block';
        }

        const paymentInfoIdInput = document.getElementById('paymentInfoId');
        if (paymentInfoIdInput) {
            paymentInfoIdInput.value = paymentInfoId;
        }
    });
} else {
    const singlePaymentInfo = {!! $paymentInfo->count() == 1 ? json_encode($paymentInfo->first()) : 'null' !!};

    if (singlePaymentInfo) {
        const priceText = document.getElementById('priceText');
        const bankInfoText = document.getElementById('bankInfoText');
        const qrCodeImage = document.getElementById('qrCodeImage');
        const qrCodeSection = document.getElementById('qrCodeSection');
        const paymentInfoIdInput = document.getElementById('paymentInfoId');

        if (priceText) {
            priceText.textContent = singlePaymentInfo.formatted_price;
        }

        if (bankInfoText) {
            bankInfoText.textContent = singlePaymentInfo.bank_info;
        }

        if (qrCodeImage) {
            qrCodeImage.src = singlePaymentInfo.payment_QR_url;
        }

        if (qrCodeSection) {
            qrCodeSection.style.display = 'block';
        }

        if (paymentInfoIdInput) {
            paymentInfoIdInput.value = singlePaymentInfo.id;
        }
    }
}

document.getElementById('completePaymentButton').addEventListener('click', function () {
        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        confirmationModal.show();
});

document.getElementById('confirmPaymentForm').addEventListener('submit', function (event) {
    event.preventDefault();
    
    const lessonData = document.getElementById('lessonData');
    const lessonId = lessonData.getAttribute('data-lesson-id');
    const lessonTitle = lessonData.getAttribute('data-lesson-title');
    const lessonSchedule = lessonData.getAttribute('data-lesson-schedule');
    
    if (lessonId && lessonId !== 'null') {
            const registrationModal = new bootstrap.Modal(document.getElementById('registrationModal'));
            const detailText = document.getElementById('lessonDetailsText');
            const lessonIdInput = document.getElementById('lessonIdInput');
            
            if(detailText) {
                detailText.textContent = `Do you want to automatically register for the lesson: "${lessonTitle}" at ${lessonSchedule}?`;
            }

            if (lessonIdInput) {
                lessonIdInput.value = lessonId;
            }

            registrationModal.show();
        } else {
            this.submit();
    }
});
document.getElementById('noThanksButton').addEventListener('click', function () {
        event.preventDefault(); 
        const confirmPaymentForm = document.getElementById('confirmPaymentForm');
        
        if (confirmPaymentForm) {
            confirmPaymentForm.submit();
        }
});
</script>
@endsection
