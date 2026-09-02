<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\ActivationStatus;
use App\Exceptions\RasilOtpException;
use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use App\Models\User;
use App\Services\PhoneOtpService;
use App\Support\PhoneNumber;
use Exception;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use UnexpectedValueException;

class AuthController extends Controller
{
    public function register(Request $request, PhoneOtpService $otp) {
        $validation = Validator::make($request->all(), [
            'first_name' => 'required_without:full_name',
            'last_name' => 'required_without:full_name',
            'full_name' => 'required_without:first_name',
            'email' => 'required|email|unique:users',
            'phone'=> 'required|unique:users',
            'age' => 'nullable|integer',
            'password' => 'required|min:6',
            'qualification' => 'nullable',
            'experience_year' => 'nullable|integer',
            'governce' => 'nullable'
        ]);
        if($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 401);
        }

        $e164 = PhoneNumber::toE164($request->phone);
        if (! PhoneNumber::isValidE164($e164)) {
            return response()->json(['error' => 'Invalid phone number'], 400);
        }

        if (User::findByPhone($request->phone)) {
            return response()->json(['error' => 'Phone number is already registered'], 401);
        }

        $payload = $this->registrationPayload($request);

        if ($otp->isVerified($request->phone, PhoneOtpService::PURPOSE_REGISTER)) {
            $otp->consumeVerified($request->phone, PhoneOtpService::PURPOSE_REGISTER);

            return $this->createdRegistration($payload);
        }

        $otp->rememberPending($request->phone, $payload, PhoneOtpService::PURPOSE_REGISTER);

        try {
            $otp->send($request->phone, PhoneOtpService::PURPOSE_REGISTER);
        } catch (RasilOtpException $e) {
            return response()->json(['error' => $e->getMessage()], $e->status);
        }

        return response()->json([
            'message' => 'Verification code sent via WhatsApp',
            'needs_verification' => true,
        ]);
    }

    public function login(Request $request) {
        $validation = Validator::make($request->all(), [
            'phone' => 'nullable|exists:users',
            'email' => 'nullable|exists:users',
            'password' => 'required',
        ]);
        if($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 401);
        }

        $user=User::where('email', $request->email)
        ->orWhere('phone', $request->phone)
        ->first();
        if(!$user || !$user->password || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'The provided credentials are incorrect'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        DeviceToken::syncFromRequest($user, $request);

        return response()->json([
            'message' => 'User successfully logged in',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Send a WhatsApp OTP via Rasil after the user enters a phone on registration.
     *
     * Body: { "phone": "+201000000000" }  (local 01000000000 is also accepted)
     */
    public function sendPhoneOtp(Request $request, PhoneOtpService $otp)
    {
        $validation = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);
        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 401);
        }

        $e164 = PhoneNumber::toE164($request->phone);
        if (! PhoneNumber::isValidE164($e164)) {
            return response()->json(['error' => 'Invalid phone number'], 400);
        }

        if (User::findByPhone($request->phone)) {
            return response()->json(['error' => 'Phone number is already registered'], 401);
        }

        try {
            $otp->send($request->phone, PhoneOtpService::PURPOSE_REGISTER);
        } catch (RasilOtpException $e) {
            return response()->json(['error' => $e->getMessage()], $e->status);
        }

        return response()->json([
            'message' => 'Verification code sent via WhatsApp',
        ]);
    }

    /**
     * Confirm the registration OTP. If the registration form was already submitted,
     * the account is created here.
     *
     * Body: { "phone": "+201000000000", "code": "483920" }
     */
    public function verifyPhoneOtp(Request $request, PhoneOtpService $otp)
    {
        $validation = Validator::make($request->all(), [
            'phone' => 'required|string',
            'code' => ['required', 'string', 'regex:/^[A-Za-z0-9]{4,10}$/'],
        ]);
        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 401);
        }

        if (! $otp->verify($request->phone, $request->code, PhoneOtpService::PURPOSE_REGISTER)) {
            return response()->json(['error' => 'Invalid or expired verification code'], 401);
        }

        $pending = $otp->pullPending($request->phone, PhoneOtpService::PURPOSE_REGISTER);
        if ($pending) {
            return $this->createdRegistration($pending);
        }

        return response()->json([
            'message' => 'Phone number verified',
        ]);
    }

    private function registrationPayload(Request $request): array
    {
        $firstName = $request->first_name;
        $lastName = $request->last_name;

        if ($request->filled('full_name') && (!$firstName || !$lastName)) {
            $parts = explode(' ', trim($request->full_name), 2);
            $firstName = $parts[0] ?? '';
            $lastName = $parts[1] ?? '';
        }

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'qualification' => $request->qualification ?? null,
            'experience_year' => $request->experience_year ?? null,
            'governce' => $request->governce ?? null,
            'role' => 'user',
            'age' => $request->age,
        ];
    }

    /**
     * Start forgot-password: store the new password and send a WhatsApp OTP.
     *
     * Body: { "phone": "01283337172", "password": "newpass1" }
     */
    public function forgotPassword(Request $request, PhoneOtpService $otp)
    {
        $validation = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|min:6',
        ]);
        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 401);
        }

        $e164 = PhoneNumber::toE164($request->phone);
        if (! PhoneNumber::isValidE164($e164)) {
            return response()->json(['error' => 'Invalid phone number'], 400);
        }

        $user = User::findByPhone($request->phone);
        if (! $user) {
            return response()->json(['error' => 'The provided credentials are incorrect'], 401);
        }

        $payload = [
            'user_id' => $user->id,
            'password' => Hash::make($request->password),
        ];

        if ($otp->isVerified($request->phone, PhoneOtpService::PURPOSE_RESET)) {
            $otp->consumeVerified($request->phone, PhoneOtpService::PURPOSE_RESET);

            return $this->resetPassword($user, $payload['password']);
        }

        $otp->rememberPending($request->phone, $payload, PhoneOtpService::PURPOSE_RESET);

        try {
            $otp->send($request->phone, PhoneOtpService::PURPOSE_RESET);
        } catch (RasilOtpException $e) {
            return response()->json(['error' => $e->getMessage()], $e->status);
        }

        return response()->json([
            'message' => 'Verification code sent via WhatsApp',
            'needs_verification' => true,
        ]);
    }

    /**
     * Confirm the forgot-password OTP and apply the new password.
     *
     * Body: { "phone": "01283337172", "code": "483920" }
     */
    public function verifyForgotPassword(Request $request, PhoneOtpService $otp)
    {
        $validation = Validator::make($request->all(), [
            'phone' => 'required|string',
            'code' => ['required', 'string', 'regex:/^[A-Za-z0-9]{4,10}$/'],
        ]);
        if ($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 401);
        }

        if (! $otp->verify($request->phone, $request->code, PhoneOtpService::PURPOSE_RESET)) {
            return response()->json(['error' => 'Invalid or expired verification code'], 401);
        }

        $pending = $otp->pullPending($request->phone, PhoneOtpService::PURPOSE_RESET);
        if (! $pending || empty($pending['user_id']) || empty($pending['password'])) {
            return response()->json([
                'message' => 'Phone number verified',
            ]);
        }

        $user = User::find($pending['user_id']);
        if (! $user) {
            return response()->json(['error' => 'The provided credentials are incorrect'], 401);
        }

        return $this->resetPassword($user, $pending['password']);
    }

    private function resetPassword(User $user, string $password)
    {
        $user->password = $password;
        $user->save();
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Password reset successfully',
            'user' => $user->fresh(),
            'token' => $token,
        ]);
    }

    private function createdRegistration(array $payload)
    {
        $user = User::create($payload);
        $token = $user->createToken('auth_token')->plainTextToken;
        DeviceToken::syncFromRequest($user, request());

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request) {
            $user=$request->user();
            $fcmToken = $request->input('fcm_token') ?? $request->input('device_token');
            if (is_string($fcmToken) && $fcmToken !== '') {
                DeviceToken::where('user_id', $user->id)->where('token', $fcmToken)->delete();
            }
            $user->currentAccessToken()->delete();
            return response()->json(['message'=>'logged out successfully']);
        }

    /**
     * Mobile Google Sign-In: verify Google ID token, then issue a Sanctum API token.
     *
     * Body: { "id_token": "<Google ID token from the mobile SDK>" }
     */
    public function googleAuthenticationCallback(Request $request) {
        $validation = Validator::make($request->all(), [
            'id_token' => 'required|string',
        ]);
        if($validation->fails()) {
            return response()->json(['error' => $validation->errors()], 401);
        }

        $clientId = config('services.google.client_id');

        if (!$clientId) {
            return response()->json(['error' => 'Google authentication is not configured'], 500);
        }

        try {
            $payload = $this->verifyGoogleIdToken($request->input('id_token'), $clientId);

            $googleId = $payload['sub'] ?? null;
            $email = $payload['email'] ?? null;

            if (!$googleId || !$email) {
                return response()->json(['error' => 'Invalid Google token payload'], 401);
            }

            $name = $payload['name'] ?? ($payload['given_name'] ?? 'User');
            $parts = explode(' ', trim($name), 2);
            $firstName = $payload['given_name'] ?? ($parts[0] ?? 'User');
            $lastName = $payload['family_name'] ?? ($parts[1] ?? null);

            $user = User::where('google_id', $googleId)->first()
                ?? User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => null,
                    'google_id' => $googleId,
                    'password' => null,
                    'role' => 'user',
                    'status' => ActivationStatus::Active,
                ]);
            } else {
                $updates = [];

                if (!$user->google_id) {
                    $updates['google_id'] = $googleId;
                }

                if ($user->status !== ActivationStatus::Active) {
                    $updates['status'] = ActivationStatus::Active;
                }

                if ($updates !== []) {
                    $user->update($updates);
                }
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            DeviceToken::syncFromRequest($user, $request);

            return response()->json([
                'message' => 'User successfully authenticated',
                'user' => $user->fresh(),
                'token' => $token,
            ]);

        } catch (UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid Google token'], 401);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Google authentication failed',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify a Google Sign-In ID token using Google's published JWKS
     * (no google/apiclient package required).
     */
    private function verifyGoogleIdToken(string $idToken, string $clientId): array
    {
        $decode = function (array $jwks) use ($idToken) {
            return (array) JWT::decode($idToken, JWK::parseKeySet($jwks));
        };

        try {
            $jwks = $this->googleJwks();
            $payload = $decode($jwks);
        } catch (UnexpectedValueException $e) {
            Cache::forget('google_oauth_jwks');
            $payload = $decode($this->googleJwks(true));
        }

        $aud = $payload['aud'] ?? null;
        $audienceMatches = is_array($aud)
            ? in_array($clientId, $aud, true)
            : $aud === $clientId;

        if (!$audienceMatches) {
            throw new UnexpectedValueException('Invalid token audience');
        }

        $issuer = $payload['iss'] ?? '';
        if (!in_array($issuer, ['accounts.google.com', 'https://accounts.google.com'], true)) {
            throw new UnexpectedValueException('Invalid token issuer');
        }

        return $payload;
    }

    private function googleJwks(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget('google_oauth_jwks');
        }

        return Cache::remember('google_oauth_jwks', now()->addHours(6), function () {
            $response = Http::timeout(10)->get('https://www.googleapis.com/oauth2/v3/certs');
            $response->throw();

            return $response->json();
        });
    }
}
