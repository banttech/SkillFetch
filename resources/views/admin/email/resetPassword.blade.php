<p>Hello {{ $name }},</p>

<p>Click the link below to reset your password:</p>

<p>
    <a href="{{ route('admin.resetPassword.view',$token) }}">
        Reset Password
    </a>
</p>

<p>If you did not request this, please ignore this email.</p>
