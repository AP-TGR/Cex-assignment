<?php

namespace App\Controllers;

use App\Config;
use App\Models\User;
use Core\JWT;

/**
 * User controller
 */
class Users extends \Core\Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_user = new User();
        parent::__construct();
    }

    /**
     * Show the profile of user
     *
     * @return void
     */
    public function ShowAction($id)
    {
        $data = $this->_user->findById($id);
        if (!$data) {
            $this->sendResponse(
                Config::HTTP_BAD_REQUEST,
                "User not found",
                [],
                ['Invalid user id.'],
            );
        }

        // Don't send the password and access token
        unset($data['password']);
        unset($data['access_token']);

        $this->sendResponse(
            Config::HTTP_SUCCESS,
            "User details found",
            $data
        );
    }

    /**
     * Login action
     *
     * @return void
     */
    public function login()
    {
        // Get the input params
        $userName = $this->_request->request->get('username');
        $password = $this->_request->request->get('password');

        $user = $this->_user->login($userName, $password);

        if (!$user) {
            $this->sendResponse(
                Config::HTTP_UNAUTHORIZED,
                'Unauthorize',
                null,
                ['Invalid login details.']
            );
        }

        $token = (new JWT())->getToken($user);
        if (!$token || !$this->_user->update(['access_token' => $token], ['id' => $user['id']])) {
            $this->sendResponse(
                Config::HTTP_INTERNAL_SERVER_ERROR,
                'Something went wrong, please try again'
            );
        }

        $this->sendResponse(
            Config::HTTP_SUCCESS,
            'You are Login Successfully.',
            [
                'id' => $user['id'],
                'token' => $token
            ],
        );
    }

    /**
     * Login action
     *
     * @return void
     */
    public function register()
    {
        // Get the input params
        $userName = $this->_request->request->get('username');
        $email = $this->_request->request->get('email');
        $password = $this->_request->request->get('password');
        $firstName = $this->_request->request->get('first_name');
        $lastName = $this->_request->request->get('last_name');

        // Validate the request params
        $validationErrors = [];
        if (!$userName || preg_match('/[^a-z.]/', $userName)) {
            $validationErrors[] = "The username must have small letter alphabet";
        }
        
        // Check if the username alredy taken
        if ($this->_user->hasUserName($userName)) {
            $validationErrors[] = "The username already taken";
        }

        // Check if the email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $validationErrors[] = "The email address is invalid.";
        }

        // Check if the email alredy taken
        if ($this->_user->hasEmail($email)) {
            $validationErrors[] = "The email already exist";
        }

        // Valoidate Password
        if (!preg_match('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/', $password)) {
            $validationErrors[] = "The password length must be 6 with atleast one capital letter, one small letter, one digit and one specicia character.";
        }

        if (!empty($validationErrors)) {
            $this->sendResponse(
                Config::HTTP_BAD_REQUEST,
                "Validation failed.",
                [],
                $validationErrors
            );
        }

        // Prepare data to save the user
        $userId = $this->_user->insert([
            'username' => $userName,
            'password' => $password,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);

        if ($userId) {
            $this->sendResponse(
                Config::HTTP_SUCCESS,
                "User registered successfully",
                ['user_id' => $userId]
            );
        }
        else {
            $this->sendResponse(
                Config::HTTP_INTERNAL_SERVER_ERROR,
                'Something went wrong, please try again'
            );
        }
    }
}
