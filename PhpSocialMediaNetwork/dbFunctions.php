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
    $sql = "SELECT UserId, Name, Phone, Password FROM User WHERE UserId = :userId";
    
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
        $sql = "INSERT INTO User (UserId, Name, Phone, Password) VALUES (:userId, :name, :phone, :password)";
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
?>
