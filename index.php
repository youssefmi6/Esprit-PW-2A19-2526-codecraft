<?php
// index.php - Routeur principal
require_once __DIR__ . '/config.php';

// Vérifier si la session n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action = $_GET['action'] ?? 'home';
$subaction = $_GET['subaction'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Routing
switch($action) {
    case 'home':
        require_once __DIR__ . '/controllers/homeController.php';
        homeIndex();
        break;
    
    case 'login':
        require_once __DIR__ . '/controllers/authController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            authLoginPost();
        } else {
            authLoginGet();
        }
        break;

    case 'login_face':
        require_once __DIR__ . '/controllers/authController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            authLoginFacePost();
        } else {
            header('Location: index.php?action=login');
            exit();
        }
        break;
    
    case 'register':
        require_once __DIR__ . '/controllers/authController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            authRegisterPost();
        } else {
            authRegisterGet();
        }
        break;

    case 'activate_account':
        require_once __DIR__ . '/controllers/authController.php';
        authActivateAccount();
        break;

    case 'resend_activation':
        require_once __DIR__ . '/controllers/authController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            authResendActivationPost();
        } else {
            authResendActivationGet();
        }
        break;
    
    case 'logout':
        require_once __DIR__ . '/controllers/authController.php';
        authLogout();
        break;
    
    case 'forgot_password':
        require_once __DIR__ . '/controllers/authController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            authForgotPasswordPost();
        } else {
            authForgotPasswordGet();
        }
        break;
    
    case 'resource':
        require_once __DIR__ . '/controllers/resourceController.php';
        if ($subaction === 'detail') {
            resourceDetail($id);
        } elseif ($subaction === 'upload') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                resourceUploadPost();
            } else {
                resourceUploadGet();
            }
        } elseif ($subaction === 'edit') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                resourceEditPost($id);
            } else {
                resourceEditGet($id);
            }
        } elseif ($subaction === 'delete') {
            resourceDelete($id);
        } elseif ($subaction === 'download') {
            resourceDownload($id);
        } elseif ($subaction === 'buy') {
            resourceBuyCreateCheckout($id);
        } elseif ($subaction === 'buy_checkout') {
            resourceBuyCreateCheckout($id);
        } elseif ($subaction === 'buy_intent') {
            resourceBuyCreateIntent($id);
        } elseif ($subaction === 'buy_success') {
            resourceBuySuccess($id);
        } elseif ($subaction === 'buy_cancel') {
            resourceBuyCancel($id);
        } elseif ($subaction === 'add_rating') {
            resourceAddRating();
        } elseif ($subaction === 'add_comment') {
            resourceAddComment();
        } elseif ($subaction === 'update_comment') {
            resourceUpdateComment();
        } elseif ($subaction === 'delete_comment') {
            resourceDeleteComment();
        } elseif ($subaction === 'react_comment') {
            resourceReactComment();
        } else {
            resourceDetail($id);
        }
        break;
    
    case 'profile':
        require_once __DIR__ . '/controllers/profileController.php';
        if ($subaction === 'edit') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                profileEditPost();
            } else {
                profileEditGet();
            }
        } elseif ($subaction === 'view') {
            profileView($id);
        } else {
            profileIndex();
        }
        break;

    case 'profile_generate_photo':
        require_once __DIR__ . '/controllers/profileController.php';
        profileGeneratePhoto();
        break;
    
    case 'admin':
        require_once __DIR__ . '/controllers/adminController.php';
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 0) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                adminLoginPost();
            } else {
                adminLoginGet();
            }
        } elseif ($subaction === 'dashboard') {
            adminDashboard();
        } elseif ($subaction === 'users') {
            adminUsers();
        } elseif ($subaction === 'resources') {
            adminResources();
        } elseif ($subaction === 'comments') {
            adminComments();
        } elseif ($subaction === 'profile') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                adminProfilePost();
            } else {
                adminProfileGet();
            }
        } elseif ($subaction === 'edit_user') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                adminEditUserPost($id);
            } else {
                adminEditUserGet($id);
            }
        } elseif ($subaction === 'view_user') {
            adminViewUser($id);
        } elseif ($subaction === 'delete_user') {
            adminDeleteUser($id);
        } elseif ($subaction === 'toggle_user_status') {
            adminToggleUserStatus($id);
        } elseif ($subaction === 'edit_resource') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                adminEditResourcePost($id);
            } else {
                adminEditResourceGet($id);
            }
        } elseif ($subaction === 'view_resource') {
            adminViewResource($id);
        } elseif ($subaction === 'delete_resource') {
            adminDeleteResource($id);
        } elseif ($subaction === 'delete_comment') {
            adminDeleteComment($id);
        } elseif ($subaction === 'download_resource') {
            adminDownloadResource($id);
        } else {
            adminDashboard();
        }
        break;
    
    default:
        require_once __DIR__ . '/controllers/homeController.php';
        homeIndex();
        break;
}
?>