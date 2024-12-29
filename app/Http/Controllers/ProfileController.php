<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return Profile::all();
    }

    public function show(Profile $profile)
    {
        return $profile;
    }

    public function store()
    {
        $profileData = [
            'user_id' => 1,
            'login' => 'NickName',
            'address' => 'Some Address',
            'phone' => '+19094892340091',
            'avatar' => 'avatar.jpg',
            'description' => 'Some description',
            'gender' => 'Male',
            'birthed_at' => '2001-12-31',
        ];

        return Profile::create($profileData);
    }

    public function update(Profile $profile)
    {
        $profileData = [
            'login' => 'NickName Edited',
            'address' => 'Some Address Edited',
            'phone' => '+233245665768678769',
        ];

        $profile->update($profileData);

        return $profile;
    }

    public function destroy(Profile $profile)
    {

        $profile->delete();

        return response(['message' => 'Profile has been deleted']);
    }
}
