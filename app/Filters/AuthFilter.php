<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // Get the current URI path
        $currentPath = $request->getUri()->getPath();
        
        // Skip authentication for login-related routes
        $publicRoutes = ['auth', 'auth/login', 'auth/forgot-password'];
        if (in_array($currentPath, $publicRoutes)) {
            return;
        }
        
        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/auth')->with('error', 'Please login first');
        }

        // Get the first segment of the URI (module)
        $segments = explode('/', trim($currentPath, '/'));
        $module = $segments[0] ?? '';

        // Define role-based access
        $roleAccess = [
            'doctors' => ['admin'],
            'medical-records' => ['admin', 'doctor'],
            'billings' => ['admin', 'receptionist'],
            'reports' => ['admin']
        ];

        // Check role-based access if module requires specific roles
        if (isset($roleAccess[$module])) {
            $userRole = $session->get('role');
            if (!in_array($userRole, $roleAccess[$module])) {
                return redirect()->to('/dashboard')->with('error', 'You do not have permission to access this section');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing after the request
    }
} 