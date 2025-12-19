<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Entities\User;

class AuthController extends BaseController
{
    // BaseController already loads common helpers like 'auth', 'form', and 'url'

    public function loginView()
    {
        // If user is already logged in, redirect to dashboard
        if (auth()->loggedIn()) {
            return redirect()->to('/');
        }

        $data = [
            'title' => 'Login - AquaSense',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/login', $data);
    }

    public function login()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $credentials = [
            'email'    => $this->request->getPost('email'),
            'password' => $this->request->getPost('password')
        ];

        $remember = (bool)$this->request->getPost('remember');

        // Attempt to login
        $result = auth()->attempt($credentials, $remember);
        
        if (!$result->isOK()) {
            return redirect()->back()->withInput()->with('error', $result->reason());
        }

        return redirect()->to('/');
    }

    public function registerView()
    {
        // If user is already logged in, redirect to dashboard
        if (auth()->loggedIn()) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Register - AquaSense',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/register', $data);
    }

    public function register()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[auth_identities.secret]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Create user using Shield's User entity
        $users = auth()->getProvider();
        
        $user = new User([
            'username' => $this->request->getPost('username'),
            'active'   => 1
        ]);

        // Save user with identity
        try {
            $users->save($user);
            
            // Add email/password identity
            $users->saveIdentity($user, [
                'type' => 'email_password',
                'secret' => $this->request->getPost('email'),
                'secret2' => service('passwords')->hash($this->request->getPost('password'))
            ]);

            // Add to admin group (you can change this as needed)
            $user->addGroup('admin');

            // Auto login after registration
            auth()->login($user);

            return redirect()->to('/dashboard')->with('success', 'Registration successful! Welcome to AquaSense.');
            
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        auth()->logout();
        
        // Destroy session completely
        session()->destroy();
        
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }

    public function forgotPasswordView()
    {
        $data = [
            'title' => 'Forgot Password - AquaSense',
            'validation' => \Config\Services::validation()
        ];

        return view('auth/forgot_password', $data);
    }

    public function forgotPassword()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'email' => 'required|valid_email'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $email = $this->request->getPost('email');
    
        
        return redirect()->to('/login')->with('success', 'If an account exists with that email, a password reset link has been sent.');
    }
}