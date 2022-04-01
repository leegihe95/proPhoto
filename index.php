<?php
require('./controller/controller.php');

try {
    $action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : null;

    switch ($action) {
        case 'homepage':
            homepage();
            break;
        case 'photo':
            photo($_REQUEST);
            break;
        case "registerView":
            registerView();
            break;
        case "registerAction":
            registerAction($_REQUEST);
            break;
        case 'googleUser':
            if (!empty($_POST['google_token']) && !empty($_POST['email']) && !empty($_POST['profile_url'])) {
                insertUser($_POST);
            } else {
                throw new ErrorException('Impossible to add a user please try again');
            }
            break;
        case "loginView":
            loginView();
            break;
        case "loginAction":
            loginAction($_REQUEST);
            break;
        case "publicProfView":
            publicProfView($_REQUEST);
            break;
        case "privateProfView":
            privateProfView($_REQUEST);
            break;
        case "setProfilePicture":
            setProfilePicture($_REQUEST);
            break;
        case "photoEdit":
            photoEdit($_REQUEST);
            break;
        case "submitPhotoEdit":
            submitPhotoEdit($_REQUEST);
            break;
        case "uploadImage":
            uploadImage($_REQUEST);
            break;
        case "logoutAction":
            logoutAction($_REQUEST);
            break;
        case "removeImage":
            removeImage($_POST);
            break;
        default:
            homepage();
            break;
    }
} catch (Exception $e) {
    require('./view/exceptionView.php');
}
