<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
    }

    public function index()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        
        return view('auth/login');
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        $user = $this->userModel->authenticate($username, $password);
        
        if ($user) {
            $this->session->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'isLoggedIn' => true
            ]);
            
            return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user['first_name']);
        }
        
        return redirect()->back()->withInput()->with('error', 'Invalid username or password');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url())->with('success', 'You have been logged out successfully');
    }

    public function changePassword()
    {
        if (!$this->session->get('user_id')) {
            return redirect()->to('/auth');
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'current_password' => 'required',
                'new_password' => 'required|min_length[8]',
                'confirm_password' => 'required|matches[new_password]'
            ];

            if ($this->validate($rules)) {
                $userId = $this->session->get('user_id');
                $currentPassword = $this->request->getPost('current_password');
                $newPassword = $this->request->getPost('new_password');

                $user = $this->userModel->find($userId);
                
                if (password_verify($currentPassword, $user['password'])) {
                    $this->userModel->update($userId, ['password' => $newPassword]);
                    return redirect()->to('/profile')->with('success', 'Password changed successfully');
                }
                
                return redirect()->back()->with('error', 'Current password is incorrect');
            }
            
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        return view('auth/change_password');
    }

    public function forgotPassword()
    {
        if ($this->request->getMethod() === 'post') {
            $rules = ['email' => 'required|valid_email'];

            if ($this->validate($rules)) {
                $email = $this->request->getPost('email');
                $user = $this->userModel->where('email', $email)->first();

                if ($user) {
                    // Generate reset token
                    $token = bin2hex(random_bytes(32));
                    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    $this->userModel->update($user['id'], [
                        'reset_token' => $token,
                        'reset_token_expires' => $expiry
                    ]);

                    // Send reset email (implement email sending later)
                    return redirect()->back()->with('success', 'Password reset instructions have been sent to your email');
                }
            }

            // Don't reveal if email exists or not for security
            return redirect()->back()->with('success', 'If your email exists in our system, you will receive reset instructions');
        }

        return view('auth/forgot_password');
    }
} 