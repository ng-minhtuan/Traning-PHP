<?php
//include connectDB
include 'connectDB.php';
//Validate input data
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
    //error message
$usernameError = $passwordError = $rePasswordError = $fnameError = $lnameError = $genderError = $mailError = $phoneError = $imageError= "";
    //request input form
if (isset($_POST['submit'])) {
    $username =mysqli_real_escape_string($conn,$_POST['username']);
    $password =mysqli_real_escape_string($conn,$_POST['password']);
    $rePassword = mysqli_real_escape_string($conn,$_POST['re-password']);
    $fname = mysqli_real_escape_string($conn,$_POST['fname']);
    $lname = mysqli_real_escape_string($conn,$_POST['lname']);
    if(isset($_POST['gender'])){
    $gender = mysqli_real_escape_string($conn,$_POST['gender']);}
    else{$gender ='';}
    $mail = mysqli_real_escape_string($conn,$_POST['mail']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    if(isset($_POST['file'])){
    $image = $_FILES['file']['name'];
    $imageSize= $_FILES['file']['size'];
    $imageTmp = $_FILES['file']['tmp_name'];
    $imageFolder = "image_upload/".$image;}
    else{$image = [];}
    
//ERROR CHECKING FOR EMPTY FILED
    $validate = true;
    if(empty($username)){
        $usernameError = "Username Field is cannot be empty!";
        $validate = false;
    }
    else{
        $username = test_input($username);
        if(!preg_match("/^[a-zA-Z0-9' ]*$/",$username)){
            $usernameError = "Username is only letters and white space allowed!";
            $validate = false;
        }
    }
    if(strlen(trim($password)) < 8){
        $passwordError = "Password must be at least 8 characters!";
    }
    if(empty($password)){
        $passwordError = "Password Field is cannot be empty!";
        $validate = false;
    }
    if(empty($rePassword)){
        $rePasswordError = "Re-Password field is cannot be empty!";
        $validate = false;
    }
    else{
        if($password !== $rePassword){
        $rePasswordError = "Password do not matched!";
        $validate = false;
        }else{
            $password = mysqli_real_escape_string($conn,md5($_POST['password']));
            $rePassword = mysqli_real_escape_string($conn,md5($_POST['re-password']));
        }
    }
    if(empty($fname)){
        $fnameError = "First name field is cannot be empty!";
        $validate = false;
    }
    if(empty($lname)){
        $lnameError = "Last name field is cannot be empty!";
        $validate = false;
    }
    if(empty($mail)){
        $mailError = "Email address field is cannot be empty!";
        $validate = false;
    }
    else{
        $mail = test_input($mail);
        // check if e-mail address is well-formed
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $mailError = "Invalid email format";
        $validate = false;
        }
    }
    if(empty($phone)) {
        $phoneError = "Phone filed is cannot be empty!";
        $validate = false;
    }
    else{
        $phone = test_input($phone);
        if(!filter_var($phone, FILTER_SANITIZE_NUMBER_INT)){
            $phoneError = "Phone number must be a number!";
            $validate = false;
        }
        if(!preg_match('/^[0-9]{10}$/',$phone)){
            $phoneError = "Phone number is length incorrect 10 characters!";
            $validate = false;
        }
    }
    if(empty($gender)){
        $genderError = "Gender is required!";
        $validate = false;
    }else{$gender = test_input($gender);}
    if(!empty($image)){
    if($imageSize > 2000000){
        $imageError = "Image size too large!";
        $validate = false;
    }
    }
if($validate == true){
    $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE user_name = '$username' OR user_mail = '$mail' OR user_phonenumber = '$phone'")or die('Query failed!');
    if(mysqli_num_rows($select) > 0 ){
        $message[] = 'User already exists';
    }else{
        $insert = mysqli_query($conn,"INSERT INTO `user_form`
        (lastname,firstname,user_name,user_password,user_gender,user_mail,user_phonenumber,user_avatar)
        VALUES ('$lname','$fname','$username','$password','$gender','$mail','$phone','$image')")
        or die('Query failed!!!');
        if($insert){
            move_uploaded_file($image_tmp_name,$imageFolder);
            $message[] = 'Register successfully!';
            header('location:login.php');
        }
        else{
            $message[] = 'Register failed!';
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
    <title>Register</title>
    <!-- custom css file link -->
    <link rel="stylesheet" href="./Style.css">
</head>

<body>
    <div id="filter"></div>
    <div class="container">
        <h3>Register Now</h3>
        
        <form action="" method="post" enctype="multipart/form-data" id="register-form">
            <!-- show error -->
            <div class="errdiv mess"><?php if(isset($message)){
                foreach($message as $message){
                    echo '<span class="error" style="color:red">'.$message.'</span>';
                }
            }
            ?>
            </div>
            <div class="content">
                <div class="input-box">
                    <span class="details">First name:</span>
                    <input type="text" id="fname" class="input" name="fname" placeholder="First name" required>
                    <div class="errdiv"><span class="error" style="color:red"><?php echo $fnameError;?></span></div>
                </div>

                <div class="input-box">
                    <span class="details">Last name:</span>
                    <input type="text" id="name" class="input" name="lname" placeholder="Last name" required>
                    <div class="errdiv"><span class="error" style="color:red"><?php echo $lnameError;?></span></div>
                </div>

                <div class="input-box">
                    <span class="details">Email:</span>
                    <input type="email" class="input" id="mail" name="mail" placeholder="Email" required>
                    <div class="errdiv"><span class="error" style="color:red"><?php echo  $mailError; ?></span></div>
                </div>    
                <div class="input-box">
                    <span class="details"> Password:</span>
                    <input type="password" class="input" id="password" name="password" placeholder="Password" required>
                    <div class="errdiv"><span class="error" style="color:red"><?php echo $passwordError; ?></span></div>
                </div>
                
                <div class="input-box">
                    <span class="details">Username:</span>
                    <input type="text" class="input" name="username" placeholder="Username" id="username" required>
                    <div class="errdiv"><span class="error" style="color:red"><?php echo $usernameError;?></span></div>
                </div>
                 
                <div class="input-box">   
                    <span class="details">Confirm Password:</span>
                    <input type="password" class="input" id="re-password" name="re-password" placeholder="Confirm Password" required>
                    <div class="errdiv"><span class="error" style="color:red"><?php echo $rePasswordError; ?></span></div>
                </div>

                <div class="input-box">
                    <span class="details">Phone Number:</span>
                    <input type="tel" id="phoneNumber" class="input" name="phone" placeholder="Phone number" required>
                    <div class="errdiv"><span class="error" style="color:red"><?php echo $phoneError; ?></span></div>
                </div>

                
                <div class="input-box gender-div">
                    <span class="details gender-title">Gender:</span>
                    <input type="radio" name="gender" value="Male" id="dot-1">
                    <input type="radio" name="gender" value="Female" id="dot-2">
                    <input type="radio" name="gender" value="Other" id="dot-3">
                    <div class="category">
                        <label for="dot-1">
                        <span class="dot one"></span>
                        <span class="gender">Male</span>
                        </label>

                        <label for="dot-2">
                        <span class="dot two"></span>
                        <span class="gender">Female</span>
                        </label>
                        
                        <label for="dot-3">
                        <span class="dot three"></span>
                        <span class="gender">Orther</span>
                        </label>
                    </div>
                    <div class="errdiv"><span class="error" style="color:red"><?php echo $genderError; ?></span>             </div>
                </div>

                <div class="input-box form-group">
                    <span class="details">Avatar:</span>
                    <input type="file" id="images" name="file" accept="image/jpg, image/png,image/jpeg">
                    <label for="images" class="btn-image">Choose your Images</label>
                    <div class="errdiv"><span class="error" style="color:red"><?php if(isset($_POST["file"])){
                        echo $image, $imageError;} else{ echo $imageError;}
                     ?></span></div>
                </div>

                <div class="button input-box">
                <input type="submit" name="submit" value="Register Now">
                </div>
            </div>
            
            <p>Already have an account? <a href="./login.php">Login Now</a></p>
            
        </form>
    </div>
</body>

</html>
