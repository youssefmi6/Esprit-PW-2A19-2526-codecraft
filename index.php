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
    
    case 'register':
        require_once __DIR__ . '/controllers/authController.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            authRegisterPost();
        } else {
            authRegisterGet();
        }
        break;
    
    case 'logout':
        require_once __DIR__ . '/controllers/authController.php';
        authLogout();
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
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                resourceBuyPost($id);
            } else {
                resourceBuyGet($id);
            }
        } elseif ($subaction === 'add_rating') {
            resourceAddRating();
        } elseif ($subaction === 'add_comment') {
            resourceAddComment();
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

    case 'subscription':
        require_once __DIR__ . '/controllers/subscriptionController.php';
        if ($subaction === 'subscribe') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                subscriptionSubscribePost();
            } else {
                subscriptionPlans();
            }
        } elseif ($subaction === 'playlists') {
            subscriptionPlaylists();
        } else {
            subscriptionPlans();
        }
        break;
    
    case 'admin':
        require_once __DIR__ . '/controllers/adminController.php';
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 0) {
            adminLoginGet();
        } elseif ($subaction === 'dashboard') {
            adminDashboard();
        } elseif ($subaction === 'subscriptions') {
            adminSubscriptions();
        } elseif ($subaction === 'subscription_add') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                adminSubscriptionAddPost();
            } else {
                adminSubscriptionAddGet();
            }
        } elseif ($subaction === 'subscription_edit') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                adminSubscriptionEditPost($id);
            } else {
                adminSubscriptionEditGet($id);
            }
        } elseif ($subaction === 'subscription_delete') {
            adminSubscriptionDelete($id);
        } elseif ($subaction === 'subscription_plan_add') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                adminSubscriptionPlanAddPost();
            } else {
                adminSubscriptionPlanAddGet();
            }
        } elseif ($subaction === 'subscription_plan_edit') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                adminSubscriptionPlanEditPost($id);
            } else {
                adminSubscriptionPlanEditGet($id);
            }
        } elseif ($subaction === 'subscription_plan_delete') {
            adminSubscriptionPlanDelete($id);
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
        } elseif ($subaction === 'delete_user') {
            adminDeleteUser($id);
        } elseif ($subaction === 'edit_resource') {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                adminEditResourcePost($id);
            } else {
                adminEditResourceGet($id);
            }
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