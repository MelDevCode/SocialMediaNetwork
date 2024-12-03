<?php
session_start();

// Handle logout confirmation
if (isset($_POST['confirm_logout'])) {
    // Destroy the session
    session_unset();
    session_destroy();
    // Redirect to the login page
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Confirmation</title>
    <!-- Add CSS for styling the modal -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .modal {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .modal-content button {
            margin: 5px;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .confirm-btn {
            background-color: DarkSlateBlue;
            color: white;
        }
        .cancel-btn {
            background-color: grey;
            color: white;
        }
    </style>
</head>
<body>
    <div class="modal">
        <div class="modal-content">
            <h2>Are you sure you want to log out?</h2>
            <form method="POST" style="display: inline;">
                <button type="submit" name="confirm_logout" class="confirm-btn">Yes, Log Out</button>
            </form>
            <button class="cancel-btn" onclick="cancelLogout()">Cancel</button>
        </div>
    </div>

    <!-- JavaScript to handle modal behavior -->
    <script>
        function cancelLogout() {
            window.history.back(); // Redirects user to the previous page
        }
    </script>
</body>
</html>

