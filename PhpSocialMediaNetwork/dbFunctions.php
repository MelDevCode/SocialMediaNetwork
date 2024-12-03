<?php
include_once 'EntityClassLib.php';

function getPDO(){
    $dbConnection = parse_ini_file("db_config.ini");
    extract($dbConnection);
    return new PDO($dsn, $user, $password);
}

function validateLogin($userId, $password) {
    $pdo = getPDO();
    
    // Prepare the SQL query
    $sql = "SELECT UserId, Name, Phone, Password FROM user WHERE UserId = :userId";
    
    // Use prepared statements to avoid SQL injection
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':userId', $userId);
    
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row) {
        // Verify the password using password_verify (not password_hash)
        if (password_verify($password, $row['Password'])) {
            // Return the user data if password matches
            return [
                'UserId' => $row['UserId'],
                'Name' => $row['Name'],
                'Phone' => $row['Phone']
            ];
        } else {
            // Password doesn't match
            return null;
        }
    } else {
        // User not found
        return null;
    }
}

function addNewUser($userId, $name, $phone, $password) {
    try {
        $pdo = getPDO();
        
        // Use a prepared statement for security and to avoid SQL injection
        $sql = "INSERT INTO user (UserId, Name, Phone, Password) VALUES (:userId, :name, :phone, :password)";
        $stmt = $pdo->prepare($sql);
        
        // Bind the parameters
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':password', $password);

        // Execute the statement
        $stmt->execute();
    } catch (PDOException $e) {
        // Check if error is due to a duplicate entry
        if ($e->getCode() == 23000) { // 23000 is the SQLSTATE code for a constraint violation, like a duplicate
            throw new Exception("A student with this ID already exists.");
        } else {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
}

function getUserId($userId) {
    try {
        $pdo = getPDO();
        $sql = "SELECT UserId FROM user WHERE UserId = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $studentExists = $row ? true : false;
        return $studentExists;
    } catch (PDOException $ex) {
        die("Database error: " . $ex->getMessage());
    }
}

function validateFriendship($friendRequesterId, $friendRequesteeId) {
    try {
        $pdo = getPDO();
        $sql = "SELECT Status FROM friendship WHERE Friend_RequesterId = :friendRequesterId AND Friend_RequesteeId = :friendRequesteeId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':friendRequesterId', $friendRequesterId);
        $stmt->bindParam(':friendRequesteeId',$friendRequesteeId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $friendshipExists = $row['Status'] == 'accepted' ? true : false;
            return $friendshipExists;
        } else {
            return false;
        }
    } catch (PDOException $ex) {
        die("Database error: " . $ex->getMessage());
    }
}


function validateFriendshipRequest($friendRequesterId,$friendRequesteeId) {
    try {
        $pdo = getPDO();
        $sql = "SELECT Friend_RequesterId FROM friendship "
                ."WHERE Friend_RequesteeId = :friendRequesteeId AND Status = 'request'";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':friendRequesteeId',$friendRequesteeId);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row) {
            $friendshipRequestExists = $row['Friend_RequesterId'] == $friendRequesterId ? true : false;
            return $friendshipRequestExists;
        } else {
            return false;
        }
    } catch (PDOException $ex) {
        die("Database error: " . $ex->getMessage());
    }
}

function addFriendRequest($friendRequesterId, $friendRequesteeId) {
    try {
        $pdo = getPDO();
        
        if(getUserId($friendRequesteeId)) {
            $sql = "INSERT INTO friendship (Friend_RequesterId, Friend_RequesteeId, Status) VALUES (:friendRequesterId, :friendRequesteeId, 'request')";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':friendRequesterId', $friendRequesterId);
            $stmt->bindParam(':friendRequesteeId', $friendRequesteeId);

            $stmt->execute();
        }
        
    } catch (PDOException $e) {
        
        throw new Exception("Database error: " . $e->getMessage());
    }
}

function addFriend($friendRequesterId, $friendRequesteeId) {
    try {
        $pdo = getPDO();
        $sql = "UPDATE friendship SET status = 'accepted' WHERE Friend_RequesterId = :friendRequesterId AND Friend_RequesteeId = :friendRequesteeId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':friendRequesterId', $friendRequesterId);
        $stmt->bindParam(':friendRequesteeId', $friendRequesteeId);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Database error: " . $ex->getMessage());
    }
}

function acceptFriendRequest($userId, $requesterId) {
    try {
        $pdo = getPDO();
        $sql = "UPDATE Friendship SET Status = 'accepted' WHERE Friend_RequesterId = :requesterId AND Friend_RequesteeId = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':requesterId', $requesterId);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Database error: " . $ex->getMessage());
    }
}

function getFriendRequesters($userId) {
    try {
        $pdo = getPDO();
        $sql = "SELECT Friend_RequesterId FROM friendship "
                . "WHERE Friend_RequesteeId = :userId AND Status = 'request'";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $friendRequesters = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $friendRequesters[] = $row["Friend_RequesterId"];
        }

        return $friendRequesters;
    } catch (PDOException $ex) {
        die("Database error: " . $ex->getMessage());
    }
}

function getFriends($userId) {
    try {
        $pdo = getPDO();
        $sql = "SELECT Friend_RequesterId FROM friendship "
                ."WHERE Friend_RequesteeId = :userId AND Status = 'accepted' "
                ."UNION SELECT Friend_RequesteeId FROM friendship "
                ."WHERE Friend_RequesterId = :userId AND Status = 'accepted' "
                ."ORDER BY 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $friends = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $friends[] = $row["Friend_RequesterId"];
        }

        return $friends;
    } catch (PDOException $ex) {
        die("Database error: " . $ex->getMessage());
    }
}

function deleteFriend($userId, $friendId) {
    try {
        $pdo = getPDO();
        $sql = "DELETE FROM friendship WHERE (Friend_RequesterId = :userId || Friend_RequesteeId = :userId) AND (Friend_RequesteeId = :friendId || Friend_RequesterId = :friendId)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':friendId', $friendId);
        $stmt->execute();
    } catch (PDOException $ex) {
        die("Database error: " . $ex->getMessage());
    }
}
?>
