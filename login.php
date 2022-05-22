 <?php 
//connect DB
include "connectDB.php";
session_start();
//Validate input data
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$userError = $passwordError = $msg = "";
function test_data($user){
    if(filter_var($user, FILTER_VALIDATE_EMAIL)){
        return 1;
    } elseif(filter_var($user,FILTER_SANITIZE_NUMBER_INT)){
        return 2;
    }else{
        return 0;
    }
}

if(isset($_POST['submit'])){

    $user = mysqli_real_escape_string($conn,$_POST['user']);
    $password = mysqli_real_escape_string($conn,($_POST['password']));
    $flag = true;
    if(empty($user)){
        $userError = "Username Field is cannot be empty!";
        $flag = false;
    }

    test_data($user);
    //check password incorrect
    if(strlen(trim($password)) < 8){
        $passwordError = "Password must be at least 8 characters!";
        $flag = false;
    }elseif(empty($password)){
        $passwordError = "Password Field is cannot be empty!";
        $flag = false;
    }else{
        $password = mysqli_real_escape_string($conn,md5($_POST['password']));
    }
    
    //if user is username
    if(test_data($user) == 0){
        //check input value
        $user = test_input($user);
        if(!preg_match("/^[a-zA-Z0-9' ]*$/",$user)){
            $userError = "Username is not matched!";
            $flag = false;
        }
        if($flag == true){
            $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE user_name = '$user' AND user_password = '$password'") or die('Can not execute query!'); 
            $row = mysqli_num_rows($select);
            if($row > 0){
                $arrUser = mysqli_fetch_assoc($select);
                $_SESSION['user_name'] = $arrUser['user_name'];
                header('Location: /home.php');
            }else{   
                $msg = "Incorrect username or password !!";
            }
        }
    }
    //if username is email
    elseif(test_data($user) == 1){
        $user = test_input($user);    
        if($flag == true){
            $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE user_mail = '$user' AND user_password = '$password'") or die('Can not execute query!');
            $row = mysqli_num_rows($select);
            if($row > 0){
                $arrUser = mysqli_fetch_assoc($select);
                $_SESSION['user_name'] = $arrUser['user_name'];
                header('Location: /home.php');
            }else{
                $msg = "Incorrect email or password !!";
            }
        }
    }
    //if username is phone number
    elseif(test_data($user) == 2){
        $user = test_input($user);
        if(!preg_match('/^[0-9]{10}$/',$user)){
            $userError = "Phone number is length incorrect 10 characters!";
            $flag = false;
        }
        if($flag == true){
            $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE user_phonenumber = '$user' AND user_password = '$password'") or die('Can not execute query!');
            $row = mysqli_num_rows($select);
            if($row > 0){
                $arrUser = mysqli_fetch_assoc($select);
                $_SESSION['user_name'] = $arrUser['user_name'];
                header('Location: /home.php');
            }else{
                $msg = "Incorrect phone or password !!";
            }
        }
    }
}  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!--custom css filt style-->
    <link rel="stylesheet" href="./Style.css">
</head>
<body>
    <div id="filter" class="filter-login"></div>
    <div id="login-container">
        <h3>Login Now</h3>
        <form id="login-form" action="" method="post" enctype="multipart/form-data">
            <div class="errdiv mess">
                <span class="error"><?php echo $msg;?></span>
            </div>
            <div class="content content-login">
                <div class=" login-input">
                    <label for="user">Username:</label><br>
                    <input class="input" type="text" name="user" placeholder="Enter your username or your email or your Phone Number" :required>
                    <div class="errdiv">
                        <span class="error" style="color:red">
                            <?php echo $userError; ?>
                        </span>
                    </div>
                </div>
                <div class="login-input">
                    <label for="password">Password:</label><br>
                    <input class="input" type="password" name="password" placeholder="Enter your password" :required>
                    <div class="errdiv">
                        <span class="error" style="color:red">
                            <?php echo $passwordError; ?>
                        </span>
                    </div>
                </div>
                <div class="button button-login">
                    <input type="submit" value="LOGIN" name="submit"/>
                </div>
            </div>
            
            <p class="login-p">Don't have an account? <a class="social" href="register.php">Regiser Now</a></p>
            
        </form>
    </div>
</body>
</html>