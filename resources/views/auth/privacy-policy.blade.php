@extends('layout.layout')

@section('content')
<div class="container my-5">
    <h1 class="text-center mb-4 page-title">Privacy Policy</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">1. Introduction</h5>
            <p class="card-text">
                We value your privacy and are committed to protecting your personal data. This policy outlines how we collect, use, and safeguard your information.
            </p>

            <h5 class="card-title">2. Data Collection</h5>
            <p class="card-text mb-0">We collect the following personal data:</p>
            <ul class="list-group list-group-flush mb-1">
                <li class="list-group-item">Name</li>
                <li class="list-group-item">Email address</li>
                <li class="list-group-item">Payment details</li>
            </ul>

            <h5 class="card-title">3. Use of Data</h5>
            <p class="card-text mb-0">Your data is used for:</p>
            <ul class="list-group list-group-flush mb-1">
                <li class="list-group-item">Managing class bookings</li>
                <li class="list-group-item">Communicating important updates</li>
                <li class="list-group-item">Processing payments</li>
            </ul>

            <h5 class="card-title">4. Data Protection</h5>
            <p class="card-text">
                We implement appropriate security measures to protect your data from unauthorized access, alteration, or disclosure.
            </p>

            <h5 class="card-title">5. Data Retention</h5>
            <p class="card-text">
                Your data is retained only as long as necessary for the purposes outlined above or as required by law.
            </p>

            <h5 class="card-title">6. Your Rights</h5>
            <p class="card-text">
                You have the right to access, correct, or delete your personal data. To exercise these rights, please contact us.
            </p>

            <h5 class="card-title">7. Contact Information</h5>
            <p class="card-text">
                For any questions or concerns regarding your privacy, please contact us at <a href="mailto:flyingstory2022@gmail.com">flyingstory2022@gmail.com</a>
            </p>
        </div>
    </div>
    <div class="text-center mt-4">
        <a href="{{ route('register') }}" class="btn btn-secondary">Back to Registration</a>
    </div>
</div>
@endsection
