<?php

namespace App\Http\Controllers\Employer;

use App\Models\OtpValidation;

trait OtpTrait
{
    protected function createOtp($phone, $type)
    {
        try {
            // Delete ALL previous OTPs for same phone & type (verified or not)
            OtpValidation::where('phone', $phone)
                ->where('type', $type)
                ->delete();

            $otp = rand(100000, 999999);

            // Different expiry times for different types
            $expiryMinutes = $type === 'supervisor_login' || $type === 'employer_login' ? 5 : 10;

            OtpValidation::create([
                'phone' => $phone,
                'otp' => $otp,
                'type' => $type,
                'is_verified' => 0,
                'attempts' => 0,
                'expire_at' => now()->addMinutes($expiryMinutes),
            ]);

            return $otp;

        } catch (\Exception $e) {
            return [
                'error' => 'OTP generation failed',
                'details' => $e->getMessage()
            ];
        }
    }

    protected function validateOtp($phone, $otp, $type)
    {
        try {
            // First, delete all expired OTPs for this phone/type
            OtpValidation::where('phone', $phone)
                ->where('type', $type)
                ->where('expire_at', '<', now())
                ->delete();

            // Get the latest OTP record
            $otpRec = OtpValidation::where('phone', $phone)
                ->where('type', $type)
                ->orderByDesc('id')
                ->first();

            if (!$otpRec) {
                return ['error' => 'OTP not found. Please request a new one.'];
            }

            // CHECK EXPIRY
            if (now()->greaterThan($otpRec->expire_at)) {
                // Delete expired OTP
                $otpRec->delete();
                return ['error' => 'OTP expired. Please request a new one.'];
            }

            // CHECK ATTEMPT LIMIT
            if ($otpRec->attempts >= 5) {
                return ['error' => 'Too many incorrect attempts. Please request a new OTP.'];
            }

            // WRONG OTP
            if ($otpRec->otp != $otp) {
                $otpRec->increment('attempts');
                $remainingAttempts = 5 - $otpRec->attempts;
                return ['error' => "Incorrect OTP. Please try again."];
            }

            // SUCCESS — MARK VERIFIED
            $otpRec->update([
                'is_verified' => 1,
                'attempts' => 0,
            ]);

            return ['success' => true];

        } catch (\Exception $e) {
            return [
                'error' => 'OTP validation failed',
                'details' => $e->getMessage()
            ];
        }
    }
}