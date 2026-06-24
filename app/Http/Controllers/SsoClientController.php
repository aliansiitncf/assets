<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SsoClientController extends Controller
{
    public function callback(Request $request)
    {
        $token = $request->query('sso_token');

        if (!$token) {
            return redirect('/')->with('error', 'Token tidak ditemukan.');
        }

        try {
            // Verifikasi token ke SSO server
            $response = Http::withToken($token)
                ->get(config('sso.server_url') . '/api/me');

            if ($response->failed()) {
                return redirect('/')->with('error', 'Token tidak valid.');
            }

            if ($response->json() == null) {
                return redirect('/')->with('error', 'Token tidak valid.');
            }

            $userData = $response->json();

            // Cari atau buat user lokal berdasarkan data SSO
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name'     => $userData['name'],
                    'password' => bcrypt(Str::random(32)),
                ]
            );
            
            if($userData['role'] == 'admin') {
                $user->assignRole("Administrator"); // Assign role admin
            } else {
                $user->assignRole("User"); // Assign role default
            }

            // Login user di client app
            Auth::login($user);

            // Simpan token SSO di session untuk keperluan logout
            session(['sso_token' => $token]);

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect('/')->with('error', 'SSO gagal: ' . $e->getMessage());
        }
    }
}
