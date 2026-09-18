@extends('layout.supervisor.app')

@section('content')

    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center align-items-center">
                <div class="col-12">
                    <div class="success-container">
                        <div class="success-icon2">
                            <img src="{{asset('supervisor_assets/images/congrates.png')}}" alt="congratulations">
                        </div>
                        <h1 class="success-title2">Congratulations!!</h1>
                        <p class="congrate-2">You have successfully cleared the qualification test.</p>
                        <p class="congrate-3">You are now the verified supervisor.</p>
                        <div class="message-box2">
                            <img src="{{asset('supervisor_assets/images/Briefcase.png')}}" alt="Briefcase">
                            <p class="highlight">You can now search and apply on the available jobs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection