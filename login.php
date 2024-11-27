<?php
include "koneksi.php";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash the password

    $data = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    $cek = mysqli_num_rows($data);
    
    if ($cek > 0) {
        $user = mysqli_fetch_array($data);
        $_SESSION['user'] = $user;

        // Redirect based on role
        if ($user['role'] == 'admin') {
            header("Location: index.php");
            exit();
        } else if ($user['role'] == 'user') {
            header("Location: dashboard_siswa.php");
            exit();
        }
    } else {
        echo '<script>alert("Maaf, Username atau Password Salah!");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <style>
        /* Reset default styles */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        /* Background Video */
        .bg-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        /* Center the login box */
        .login-box {
            background: rgba(255, 255, 255, 0.8); /* White background with transparency */
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 360px;
            text-align: center;
            position: relative;
            z-index: 1; /* Ensure it's above the video */
            margin: auto;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Header Styling */
        .login-header {
            margin-bottom: 30px;
        }

        .login-header header {
            font-size: 32px;
            font-weight: 700;
            color: #27ae60; /* Green color */
            margin: 0;
        }

        /* Input Box Styling */
        .input-box {
            margin-bottom: 25px;
        }

        .input-field {
            width: 100%;
            padding: 14px;
            border: 1px solid #dcdcdc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
            color: #333;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .input-field:focus {
            border-color: #27ae60; /* Green focus color */
            outline: none;
            box-shadow: 0 0 8px rgba(39, 174, 96, 0.3);
        }

        /* Submit Button Styling */
        .input-submit {
            margin-top: 20px;
        }

        .submit-btn {
            background-color: #27ae60; /* Green button color */
            color: white;
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .submit-btn:hover {
            background-color: #219653; /* Darker green on hover */
            transform: translateY(-2px);
        }

        .submit-btn:active {
            background-color: #1e6b48; /* Dark green on active */
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 500px) {
            .login-box {
                padding: 20px;
            }
            
            .input-field {
                font-size: 14px;
            }

            .submit-btn {
                font-size: 14px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    
    <video class="bg-video" playsinline="playsinline" autoplay="autoplay" muted="muted" loop="loop">
        <source src="assets/mp4/v.mp4" type="video/mp4" />
    </video>

    <div class="login-box">
        <div class="login-header">
            <header>LOGIN</header>
        </div>
        <form method="POST" action="">
            <div class="input-box">
                <input type="text" class="input-field" name="username" placeholder="Username" required>
            </div>
            <div class="input-box">
                <input type="password" class="input-field" name="password" placeholder="Password" required>
            </div>
            <div class="input-submit">
                <button type="submit" class="submit-btn" name="login">Log in</button>
            </div>
        </form>
    </div>
    
</body>
</html>


<!-- 
<?php
include "koneksi.php";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Hash the password

    $data = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    $cek = mysqli_num_rows($data);
    
    if ($cek > 0) {
        $user = mysqli_fetch_array($data);
        $_SESSION['user'] = $user;

        // Redirect based on role
        if ($user['role'] == 'admin') {
            header("Location: index.php");
            exit();
        } else if ($user['role'] == 'user') {
            header("Location: dashboard_siswa.php");
            exit();
        }
    } else {
        echo '<script>alert("Maaf, Username atau Password Salah!");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login</title>
    <style>
        /* Reset default styles */
        body, html {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    background: url('w.jpg') no-repeat center center fixed; /* Menambahkan gambar sebagai latar belakang */
    background-size: cover; /* Memastikan gambar memenuhi layar */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

        /* Login Box Styling */
        .login-box {
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 360px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-box::before {
            content: "";
            position: absolute;
            top: -30px;
            right: -30px;
            width: 150%;
            height: 150%;
            background: rgba(52, 152, 219, 0.15);
            border-radius: 50%;
            z-index: 0;
            transform: rotate(30deg);
        }

        .login-box > * {
            position: relative;
            z-index: 1;
        }

        /* Header Styling */
        .login-header {
            margin-bottom: 30px;
        }

        .login-header header {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
        }

        /* Input Box Styling */
        .input-box {
            margin-bottom: 25px;
        }

        .input-field {
            width: 100%;
            padding: 14px;
            border: 1px solid #dcdcdc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
            color: #333;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .input-field:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
        }

        /* Submit Button Styling */
        .input-submit {
            margin-top: 20px;
        }

        .submit-btn {
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 14px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .submit-btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .submit-btn:active {
            background-color: #1f618d;
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 500px) {
            .login-box {
                padding: 20px;
            }
            
            .input-field {
                font-size: 14px;
            }

            .submit-btn {
                font-size: 14px;
                padding: 12px;
            }
        }
    </style>
</head>
<body>
    
    <div class="login-box">
        <div class="login-header">
            <header>LOGIN</header>
        </div>
        <form method="POST" action="">
            <div class="input-box">
                <input type="text" class="input-field" name="username" placeholder="Username" required>
            </div>
            <div class="input-box">
                <input type="password" class="input-field" name="password" placeholder="Password" required>
            </div>
            <div class="input-submit">
                <button type="submit" class="submit-btn" name="login">Log in</button>
            </div>
        </form>
    </div>
    
</body>
</html>
 -->