<?php
if(!isset($_SESSION)) { 
    session_start(); 
}

require_once('Manager.php');
class PictureManager extends Manager
{

    public function __construct()
    {
        parent::__construct();
    } 

    private function getProfilePathAndName($userId) {
        $req = $this->_connection->prepare("SELECT id, username, profile_url FROM users WHERE id = :id");
        $req->execute(array(
            "id" => $userId
        ));
        $data = $req->fetch(PDO::FETCH_ASSOC);
        $req->closeCursor();
        if (!empty($data["profile_url"]) && !empty($data["username"])) {
            return (array(
                "profilePath" => $data["profile_url"],
                "username" => $data["username"]
            ));
        } else {
            return (array(
                "profilePath" => "./data/default/profilePicture.jpg",
                "username" => "username"
            ));
        }
    }

    public function getProfilePicturePath() {
        $userId = (!empty($_SESSION['id']))? $_SESSION["id"]: -1;
        $req = $this->_connection->prepare("SELECT id, profile_url FROM users WHERE id = :id");
        $req->execute(array(
            "id" => $userId
        ));
        $data = $req->fetch(PDO::FETCH_ASSOC);
        $req->closeCursor();
        if (!empty($data["profile_url"])) {
            return $data["profile_url"];
        } else {
            return "./data/default/profilePicture.jpg";
        }
    }

    public function getImage($imageId) {
        $req = $this->_connection->prepare("SELECT user_id, title, description, tags,
           price, bookmark FROM pictures WHERE id = :id");
        $req->execute(array(
            "id" => $imageId
        ));
        $data = $req->fetch(PDO::FETCH_ASSOC);
        $req->closeCursor();
        
        $userData = $this->getProfilePathAndName($data["user_id"]);

        if (!empty($data["user_id"])) {
            return (array(
                "id" => $imageId,
                "path" => "./data/" . $data["usser_id"] . "/medium/" . $imageId . "jpg",
                "title" => $data["title"],
                "description" => $data["description"],
                "tags" => $data["tags"],
                "price" => $data["price"],
                "bookmark" => $data["bookmark"],
                "username" => $userData["username"],
                "profilePicture" => $userData["profilePath"]
            ));
        } else {
            return (array(
                "id" => -1,
                "path" => "./data/default/image.jpg",
                "title" => "title",
                "description" => "description",
                "tags" => "tags",
                "price" => "price",
                "bookmark" => "bookmark",
                "username" => "username",
                "profilePicture" => "profilePath"
            ));
        }
    }
    
    private function getSmallImage($imageId) {
        $req = $this->_connection->prepare("SELECT user_id, title, description, tags,
           price, bookmark FROM pictures WHERE id = :id");
        $req->execute(array(
            "id" => $imageId
        ));
        $data = $req->fetch(PDO::FETCH_ASSOC);
        $req->closeCursor();
        
        $userData = $this->getProfilePathAndName($data["user_id"]);

        if (!empty($data["user_id"])) {
            return (array(
                "id" => $imageId,
                "path" => "./data/" . $data["usser_id"] . "/small/" . $imageId . "jpg",
                "title" => $data["title"],
                "price" => $data["price"],
                "bookmark" => $data["bookmark"],
                "username" => $userData["username"],
                "profilePicture" => $userData["profilePath"]
            ));
        } else {
            return (array(
                "id" => -1,
                "path" => "./data/default/image.jpg",
                "title" => "title",
                "price" => "price",
                "bookmark" => "bookmark",
                "username" => "username",
                "profilePicture" => "profilePath"
            ));
        }
    }

    public function getNumberImages() {
        $req = $this->_connection->query("SELECT id FROM pictures");
        $data = $req->fetchAll(PDO::FETCH_ASSOC);
        $req->closeCursor();
        return $data.length;
    }
    
    public function getNumberImagesFromId($userId) {
        $req = $this->_connection->prepare("SELECT id FROM pictures WHERE user_id = :user_id");
        $req->execute(array(
            "user_id" => $userId
        ));
        $data = $req->fetchAll(PDO::FETCH_ASSOC);
        $req->closeCursor();
        return $data.length;
    }

    public function getRandomImages($imageslist=[]) {
        $req = $this->_connection->query("SELECT id FROM pictures");
        $data = $req->fetchAll(PDO::FETCH_ASSOC);
        $req->closeCursor();
        if (imagesList.length<data.length){
            shuffle($data);
            foreach ($data as $dat) {
                if (!in_array($dat,$imagesList)) {
                    array_push($imagesList,$dat);
                    $val = getSmallImage($dat);
                    return (array($imagesList,$val));
                }
            }
        } else {
            return "default"; 
        }
    }

    public function getRandomImagesFromId($userId, $imagesList=[]) {
        $req = $this->_connection->prepare("SELECT id FROM pictures WHERE user_id = :user_id");
        $req->execute(array(
            "user_id" => $userId
        ));
        $data = $req->fetchAll(PDO::FETCH_ASSOC);
        $req->closeCursor();
        if (imagesList.length<data.length){
            shuffle($data);
            foreach ($data as $dat) {
                if (!in_array($dat,$imagesList)) {
                    array_push($imagesList,$dat);
                    $val = getSmallImage($dat);
                    return (array($imagesList,$val));
                }
            }
        } else {
            return "default"; 
        }
    }

    public function getLikedImages($imagesList=[]) {

    }

    public function getPurchasedImages($imagesList=[]) {

    }

    public function setProfilePicture($file) {
        if(!isset($_SESSION)) { 
            session_start(); 
        }
        //$currentDir = getcwd();
        $dataDir = "./data/";
        //$currentDir = dirname(dirname(__FILE__));
        // Store all errors
        $errors = [];
        
        // Available file extensions
        $fileExtensions = ["jpeg", "jpg", "png", "gif"];
        
        // get user ID if define, otherwise set to default
        if (!empty($_SESSION["id"])) {
            $user = $_SESSION["id"];
        } else {
            $user = "default";
        }

        $stmt = $this->_connection->prepare("SELECT * FROM users WHERE id=?");
        $stmt->bindParam(1, $user, PDO::PARAM_STR);
        $stmt->execute(); 
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($file ?? null)) {
            $fileName = $file["name"];
            $fileTmpName = $file["tmp_name"];
            $fileType = $file["type"];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
            //$user_id = $_POST["userID"];
            $uploadPath = $dataDir . $user . "/profilePicture.jpg";
            //$message = "";//$uploadPath;
            if (isset($fileName)) {
                if (!in_array($fileExtension, $fileExtensions)) {
                    $errors[] = "JPEG, JPG, PNG and GIF images are only supported";
                }
                if (empty($errors)) {
                    if (!file_exists($dataDir . $user)) {
                        mkdir($dataDir . $user);
                    }
                    $didUpload = move_uploaded_file($fileTmpName, $uploadPath);
                    if ($didUpload) {
                        echo "The image " . basename($fileName) . " has been uploaded.";

                        //update profile pic path in database 
                        $stmt = $this->_connection->prepare("UPDATE users SET profile_url = 'profilePicture.jpg' WHERE id=?");
                        $stmt->bindParam(1, $user, PDO::PARAM_STR);
                        $stmt->execute();                 
                    
                    } else {
                        echo "An error occurred while uploading. Try again.";
                    }
                } else {
                    foreach ($errors as $error) {
                        echo "The following error occured: " . $error . "\n";
                    }
                }
            }
        }


    }

    public function setImage() {
    
    }

    public function setImageInfo($params) {

    }

    public function deleteImage($imageId) {

    }

}