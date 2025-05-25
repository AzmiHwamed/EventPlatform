<?php

namespace Guard;

class AdminAuthGuard
{
    public static function check()
    {

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            // Redirect to login page or show unauthorized access message
            header('Location: ../login.php');
            exit();
        }
    }
}

// Usage example
AdminAuthGuard::check();