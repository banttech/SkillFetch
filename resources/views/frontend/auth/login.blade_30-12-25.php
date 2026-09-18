@extends('layout.frontend.app')

@section('content')



<style>
   .role-card {
    background: #0e253a;
    border: 3px solid #0e253a;
}.employer-header {
    background-color: #edf6fd;
    color: #0d2941;
    border: 2px solid #0c2a41;
    font-size: 24px;
    font-weight: 800;
}.register-btn2 {
    border-radius: 40px;
    background: #ffffff;
    color: #0d2941;
    font-weight: 700;
}.register-btn2:hover {
    background-color: #bec8cd;
}
.card-container{
        background: #bec8cd;
}.card-title {
    color: #fff;
}.card-description {
    color: #ddd;
}.supervisor-header {
    background-color: #edf6fd;
    color: #0d2941;
    border: 2px solid #0c2a41;
    font-size: 24px;
    font-weight: 800;
}
</style>
    <section>
        <div class="card-container">
            <!-- Employer Card -->
            <div class="role-card">
                <button class="card-header-btn employer-header">Employer</button>

                <div class="com-heighs">
                    <h2 class="card-title">Are you an Employer?</h2>

                    <p class="card-description">
                        Find qualified supervisors and manage your workforce efficiently. Post jobs, track progress, and
                        grow your
                        business with the right team.
                    </p>
                </div>

                <a href="{{ route('employer.login.view') }}">
                    <button class="register-btn2">Login as Employer</button>
                </a>
            </div>

            <!-- Supervisor Card -->
            <div class="role-card">
                <button class="card-header-btn supervisor-header">Supervisor</button>

                <div class="com-heighs">
                    <h2 class="card-title">Are you a Supervisor?</h2>

                    <p class="card-description">
                        Connect with top employers and showcase your leadership skills. Find exciting opportunities and
                        advance your
                        career with orekr
                    </p>
                </div>

                <a href="{{ route('supervisor.login.view') }}">
                    <button class="register-btn2 register-2">Login as Supervisor</button>
                </a>
            </div>
        </div>

    </section>
@endsection