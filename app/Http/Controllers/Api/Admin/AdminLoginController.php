<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Passport\Client;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Exception\ClientException;

class AdminLoginController extends Controller
{
    public function login(Request $request)
    {
        $http = new \GuzzleHttp\Client;

        $clients = Client::where('password_client', 1)
            ->where('provider', 'admin')
            ->get();

        if (!sizeof($clients)) {
            abort(403, 'Auth Client not Found!');
        }

        $client = $clients->first();

        $data = [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'scope' => '*'
        ];


        try {
            $response = $http->post(route('passport.token'), [
                'form_params' => $data,
            ]);
        } catch (ClientException $e) {
            if ($e->getCode() >= 400 && $e->getCode() < 404) {
                abort(401, "Authentication Error!");
            } else {
                abort($e->getCode(), json_decode($e->getMessage(), true));
            }
        }


        return json_decode((string) $response->getBody(), true);
    }

    public function refresh(Request $request)
    {
        $http = new \GuzzleHttp\Client;

        $clients = Client::where('password_client', 1)
            ->where('provider', 'admin')
            ->get();

        if (!sizeof($clients)) {
            abort(403, 'Auth Client not Found!');
        }

        $client = $clients->first();

        $data = [
            'grant_type' => 'refresh_token',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'scope' => '*',
            'refresh_token' => $request->get('refresh_token')
        ];


        try {
            $response = $http->post(route('passport.token'), [
                'form_params' => $data,
            ]);
        } catch (ClientException $e) {
            if ($e->getCode() >= 400 && $e->getCode() < 404) {
                abort(401, "Authentication Error!");
            } else {
                abort($e->getCode(), json_decode($e->getMessage(), true));
            }
        }


        return json_decode((string) $response->getBody(), true);
    }

    /**
     * Logout Current User
     *
     * @param Request $reques
     * @return void
     */
    public function logout(Request $reques)
    {
        $user = Auth::user();
        $token = $user->token();

        $token->revoke();
        $token->delete();

        return api_response("Logout Successful", true);
    }

    /**
     * Get Authenticated User's Profile
     *
     * @param Request $request // Requs
     *
     * @return void
     */
    public function profile(Request $request)
    {
        $profile = User::withRoles()->find(Auth::id());
        $profile->loadPermissions();

        return api_response('Profile Loaded', true, $profile);
    }
}
