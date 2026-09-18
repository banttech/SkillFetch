@extends('layout.supervisor.app')
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="welcome-box">
                        <img src="{{ asset('supervisor_assets/images/Speed.png') }}" alt="dashboard">
                        <h1>Welcome to your dashboard !</h1>
                    </div>

                    <div class="instruction-box">
                        <p>To apply on any jobs, you need to qualify the following test. Please attempt this test.</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="test-name-box">
                                <p>
                                    <span class="label">Test Name:</span>
                                    <span class="ms-2">{{ ucfirst($test->name) }} <br>
                                    </span>
                            
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="questions-box">
                                <p class="div-top-ques2">No. of Questions: {{ $test->total_question ?? 0 }}</p><br>
                                <p class="div-top-ques3">Passing Marks: {{ $test->passing_marks ?? 0 }}</p>
                                 <p class="div-top-ques3">Test Timing: {{ $test->timing ?? 0 }} Min</p>
                                <p class="marks-info">Each question carries {{ $test->marks ?? 0 }} marks</p>
                            </div>
                        </div>
                    </div>

                    <div class="payment-card">
                        <div class="test-fee-badge">
                            <p>Test fee: Rs. {{ number_format($test->fees, 2) }}/-</p>
                        </div>

                        <p class="description">Finish your payment to continue to the test section.</p>

                        <!-- UPDATED BUTTON -->
                        @include('components.razorpay-payment-button', [
                            'buttonId' => 'payBtn',
                            'buttonText' => 'Pay now to give test',
                            'initiateRoute' => route('supervisor.payment.initiate'),
                            'redirectUrl' => '/supervisor/dashboard',
                            'paymentName' => 'Supervisor Test Fee',
                            'requestBody' => [], 
                            'checkCamera' => true,
                        ])

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
