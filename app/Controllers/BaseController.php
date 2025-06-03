<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = ['url', 'form', 'text'];

    /**
     * Session instance
     */
    protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Initialize session
        $this->session = session();

        // Set default page title
        $this->data['title'] = 'Clinic Management System';
        $this->data['user'] = [
            'id' => $this->session->get('user_id'),
            'username' => $this->session->get('username'),
            'first_name' => $this->session->get('first_name'),
            'last_name' => $this->session->get('last_name'),
            'role' => $this->session->get('role')
        ];
    }

    /**
     * Get current user's role
     */
    protected function getUserRole(): string
    {
        return $this->session->get('role') ?? '';
    }

    /**
     * Check if user has required role
     */
    protected function checkRole(array $allowedRoles): bool
    {
        return in_array($this->getUserRole(), $allowedRoles);
    }
}
