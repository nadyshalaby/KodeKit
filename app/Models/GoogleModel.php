<?php

namespace App\Models;

use App\Libs\Concretes\Model;
use App\Libs\Statics\Config;
use App\Libs\Statics\Hash;
use Carbon\Carbon;
use Google_Client;
use Google_Service_Plus;

class GoogleModel extends Model {

    protected $payload;
    protected $client;
    private $userSlug;
    private $userRememberMe;
    public $table = 'users';
    public $defaults;
    public $insertable = [
        'f_name',
        'l_name',
        'email',
        'pass',
        'remember_me',
        'active',
        'avatar',
        'national_id',
        'slug',
        'role',
        'created_at',
        'updated_at',
    ];

    public function __construct(Google_Client $client = null) {
        $this->client = $client;

        if ($this->client) {
            $this->client->setClientId(Config::extra('social.google.client_id'));
            $this->client->setClientSecret(Config::extra('social.google.client_secret'));
            $this->client->setRedirectUri(route('google'));
            $this->client->setScopes(Config::extra('social.google.scopes'));
        }

        $this->defaults = [
            'role' => 'normal',
            'national_id' => '',
            'pass' => 'nothing',
            'active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    public function getAuthUrl() {
        return $this->client->createAuthUrl();
    }

    public function updateUserInformation() {
        if (isset($_GET['code'])) {
            $this->client->authenticate($_GET['code']);
            $this->setToken($this->client->getAccessToken());
            $plus = new Google_Service_Plus($this->client);
            $person = $plus->people->get('me');

            $email = $this->client->verifyIdToken()->getAttributes()['payload']['email'];
            if (!$this->isUserExist($email)) {

                //fetching user information
                $id = $person['id'];
                $name = $person['displayName'];
                $image = $person['image']['url'];
                $cover = $person['modelData']['cover']['coverPhoto']['url'];
                $gender = $person['gender'];
                $address = $person['modelData']['placesLived'][0]['value'];
                $company = $person['modelData']['organizations'][0]['name'];

                // Fetched normaliy
                $slug = $this->getSlug($name);
                $name = explode(' ', $name);
                $f_name = $name[0];
                $l_name = $name[count($name) - 1];
                $remember_me = $this->getRememberMe();

                $user_columns = [
                    'f_name' => $f_name,
                    'l_name' => $l_name,
                    'email' => $email,
                    'slug' => $slug,
                    'remember_me' => $remember_me,
                    'avatar' => $image,
                ];

                $this->setUserSlug($slug);
                $this->setUserRememberMe($remember_me);
                return self::insert($user_columns);
            } else {
                $user = self::first('email = ?', [$email]);

                $this->setUserSlug($user->slug);
                $this->setUserRememberMe($user->remember_me);

                return true;
            }
        }
        return false;
    }

    //Getters and Setters
    public function getUserRememberMe() {
        return $this->userRememberMe;
    }

    public function setUserRememberMe($userRememberMe) {
        $this->userRememberMe = $userRememberMe;
    }

    public function getUserSlug() {
        return $this->userSlug;
    }

    public function setUserSlug($userSlug) {
        $this->userSlug = $userSlug;
    }

    // Helper methods
    private function setToken($token) {
        $this->client->setAccessToken($token);
    }

    private function getSlug($name) {
        $slug = slugify($name);
        $new_slug = $slug;
        while (self::findBy(['slug' => $new_slug])) {
            $new_slug = $slug . '-' . rand(0, 99999);
        }
        return $new_slug;
    }

    private function getRememberMe() {
        $remember_me = Hash::unique(30);
        while (self::findBy(['remember_me' => $remember_me])) {
            $remember_me = Hash::unique(30);
        }
        return $remember_me;
    }

    private function isUserExist($email) {
        return self::findBy(['email' => $email]);
    }

}
