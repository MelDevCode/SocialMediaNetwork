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

function getAccessibility()
{
    $pdo = getPDO();
    $sql = "SELECT accessibility_Code, Description FROM accessibility";
    $stmt = $pdo->query($sql);   
    $accessibility = [];
    if($stmt->rowCount() > 0)
    {
       while($row = $stmt->fetch(PDO::FETCH_ASSOC))
       {
        $accessibility[] = $row; 
       }
    }
    return $accessibility;
}


    function AddNewAlbum($title, $Description, $OwnerId, $selectAccessibility)
    {
        try
        {
            $pdo = getPDO();
            $sql = "INSERT INTO album (Title, Description, Owner_Id, Accessibility_Code)
                    VALUES (:Title, :Description, :Owner_Id, :Accessibility_Code)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['Title' => $title, 'Description' => $Description, 'Owner_Id' => $OwnerId, 'Accessibility_Code' => $selectAccessibility]);
        }
        catch (PDOException $e)
        {
            throw new Exception("Error Insert new Album: " . $e); 
        }
    }

    function getPictureAlbums($OwnerId)
    {
        try
        {
            $pdo = getPDO();
            $sql = "SELECT a.Title, 
                    a.Album_Id, 
                    count(p.Album_Id) as 'NumberOfPictures',
                    a.Accessibility_Code,
                    ac.Description
                    FROM album a
                    LEFT JOIN picture p ON a.Album_Id = p.Album_Id
                    INNER JOIN Accessibility ac ON a.Accessibility_Code = ac.Accessibility_Code
                    WHERE a.Owner_Id = :Owner_Id
                    GROUP BY a.Title, a.Album_Id, a.Accessibility_Code";
            
        $stmt = $pdo->prepare($sql);  
        $stmt-> execute(['Owner_Id' => $OwnerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch (PDOException $e)
        {
            throw new Exception("Error getPictureAlbums: " . $e); 
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
            $sql = "UPDATE friendship SET Status = 'accepted' WHERE Friend_RequesterId = :requesterId AND Friend_RequesteeId = :userId";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':requesterId', $requesterId);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
        } catch (PDOException $ex) {
            die("Database error: " . $ex->getMessage());
        }
    }

function denyFriendRequest($userId, $requesterId) {
    try {
        $pdo = getPDO();
        $sql = "DELETE FROM friendship WHERE Friend_RequesterId = :requesterId AND Friend_RequesteeId = :userId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':requesterId', $requesterId);
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

    function getAlbums($ownerId) {
        $pdo = getPDO(); // Ensure getPDO() returns a valid PDO instance

        // Update the SQL query to include Album_Id
        $sql = "SELECT Album_Id, Title, Description 
                FROM album 
                WHERE Owner_Id = :ownerId";
        
        // Use prepared statements to avoid SQL injection
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ownerId', $ownerId);
        
        $stmt->execute();
        
        // Fetch all rows at once
        $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $albums; // Returns an array of albums, now including Album_Id
    }

    function getSharedAlbums($ownerId) {
        $pdo = getPDO(); // Ensure getPDO() returns a valid PDO instance

        // Update the SQL query to include Album_Id
        $sql = "SELECT Album_Id, Title, Description 
                FROM album 
                WHERE Owner_Id = :ownerId
                AND Accessibility_Code = 'shared'";
        
        // Use prepared statements to avoid SQL injection
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':ownerId', $ownerId);
        
        $stmt->execute();
        
        // Fetch all rows at once
        $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $albums; // Returns an array of albums, now including Album_Id
    }

    function savePicture($albumId, $fileName, $title, $description, $tempFilePath) {
        $pdo = getPDO();

        // Upload to 'uploads' directory 
        $uploadDirectory = 'uploads/';
        $filePath = $uploadDirectory . $fileName;

        // Move the uploaded file to the appropriate directory
        if (move_uploaded_file($tempFilePath, $filePath)) {
            $sql = "INSERT INTO picture (Album_Id, File_Name, Title, Description) VALUES (:albumId, :fileName, :title, :description)";

            $stmt = $pdo->prepare($sql);

            // Bind the parameters
            $stmt->bindParam(':albumId', $albumId);
            $stmt->bindParam(':fileName', $filePath);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':description', $description);

            // Execute the query
            $stmt->execute();
        } else {
            // Handle error if the file couldn't be moved
            echo "Error uploading file: $fileName";
        }
    }

    function getPhotosByAlbumId($albumId) {
        $pdo = getPDO(); 
        $query = "SELECT * FROM picture WHERE Album_Id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$albumId]);
        $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Make sure to prepend the 'uploads/' folder to the image file paths
        foreach ($photos as $photo) {
            $photo['File_Name'] = 'uploads/' . $photo['File_Name']; // Adjust the path
        }

        return $photos;
    }

function saveComment($authorId, $pictureId, $commentText) {
    $pdo = getPDO();
    $sql = "INSERT INTO Comment (Author_Id, Picture_Id, Comment_Text, Created_At) 
            VALUES (:authorId, :pictureId, :commentText, NOW())";
    $stmt = $pdo->prepare($sql);
    //Bind the parameters
    $stmt->bindParam(':authorId', $authorId);
    $stmt->bindParam(':pictureId', $pictureId);
    $stmt->bindParam(':commentText', $commentText);
    //Execute the query
    $stmt->execute();
}

function getCommentsByPictureId($pictureId) {
    $pdo = getPDO();
    $sql = "SELECT c.Comment_Text, c.Created_At, u.Name AS AuthorName 
            FROM Comment c
            JOIN User u ON c.Author_Id = u.UserId
            WHERE c.Picture_Id = :pictureId
            ORDER BY c.Created_At DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pictureId' => $pictureId]);
    
    $comments = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $comments[] = [
            'AuthorName' => $row['AuthorName'],
            'Comment_Text' => $row['Comment_Text'],
            'Created_At' => $row['Created_At']
        ];
    }
    return $comments;
}

    function getSharedAlbumsByFriend($friendUserId, $userId) {
        $pdo = getPDO();
        $query = "SELECT a.Album_Id, a.Title 
                FROM album a
                JOIN friendship f ON (f.Friend_RequesterId = a.Owner_Id OR f.Friend_RequesteeId = a.Owner_Id)
                WHERE f.Status = 'Accepted' 
                AND a.Accessibility_Code = 'shared' 
                AND a.Owner_Id = :friendUserId 
                AND (f.Friend_RequesterId = :userId OR f.Friend_RequesteeId = :userId)";
        
        $stmt = $pdo->prepare($query);
        
        // Bind parameters using PDO syntax
        $stmt->bindValue(':friendUserId', $friendUserId, PDO::PARAM_STR);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
        
        // Execute the query
        $stmt->execute();
        
        // Fetch results as associative array
        $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $albums;
}

function friendName($friendUserId) {
    $pdo = getPDO(); 
    $sql = "SELECT Name FROM user WHERE UserId = :friendUserId"; 
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':friendUserId', $friendUserId);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['Name'] : null; // Return the name or null if not found
}

function updateAlbum($rowsToSave)
{
    try
    {
        
        foreach ($rowsToSave as $row) {
            $pdo = getPDO();
            // Access the values for 'Album_Id' and 'Accessibility_Code'
            $albumId = $row['Album_Id'];
            $accessibilityCode = $row['Accessibility_Code'];
        
            // Update the album
            $query= "Update album set Accessibility_Code = :accessibilityCode WHERE Album_Id = :albumId";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['accessibilityCode'=>$accessibilityCode ,'albumId'=>$albumId]); 
        }
        return true;
    }
    catch (PDOException $e)
    {
        return "Error updating album" .$e;
        //throw new Exception("Error deleting album: " . $e); 
    
    }
}

function deletePicture($albumId) 
{
    try
    {
        $pdo = getPDO();

        // Delete pictures first associated with the album
        $query = "DELETE FROM picture WHERE Album_Id = :albumId";  
        $stmt = $pdo->prepare($query);
        $stmt->execute(['albumId'=>$albumId]); 
    
    }
    catch (PDOException $e)
    {
        return "Error deleting album" .$e;
        //throw new Exception("Error deleting album: " . $e); 
    
    }
}

function deleteAlbum($albumId): bool|string 
    {
        try
        {
            $pdo = getPDO();
        
            // Delete the album
            $query= "DELETE FROM album WHERE Album_Id = :albumId";  
            $stmt = $pdo->prepare($query);
            $stmt->execute(['albumId'=>$albumId]); 
        
            return true;
        }
        catch (PDOException $e)
        {
            return "Error deleting album" .$e;
            //throw new Exception("Error deleting album: " . $e); 
        
        }
    }
?>