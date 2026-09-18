@extends('layout.frontend.app')

@section('content')

    <section>
        <div class="card-container card-only-regitsers">
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

                <a href="{{ route('employer.register.view') }}">
                    <button class="register-btn2">Register as Employer</button>
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

                <a href="{{ route('supervisor.register.view') }}">
                    <button class="register-btn2 register-2">Register as Supervisor</button>
                </a>
            </div>
        </div>

    </section>
@endsection