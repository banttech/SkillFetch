
@extends('layout.supervisor.app')
@section('content')
<div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center align-items-center">
                <div class="col-12">
                    <div class="success-container">
                        <div class="success-icon">
                            <div class="outer-circle">
                                <div class="inner-circle">
                                    <i class="fas fa-check checkmark"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="success-title">Test Submitted Successfully!</h1>
                        <div class="title-underline"></div>
                        <div class="message-box">
                            <p>Thanks for attempting the test. Your answers are saved & shared with the Admin.</p>
                            <p class="highlight">Admin will check the test and confirm the result shortly. Please wait
                                for your
                                result...!!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection