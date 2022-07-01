<?php 
session_start();
if(isset($_SESSION['email'])){
include('./header.php');
include_once ('../private/conn.php');
$id = $_SESSION['id'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $text = mysqli_real_escape_string($conn,$_POST['text']) ;
    $error = [];

if(!$text){
    $errors[] = 'Text is required';
} 
if (empty( $_FILES["picture"]["name"])) {
    $errors[] = 'Picture is required';
} 

if(strlen($text) < 10){
    $errors[] = 'Text need to have a minimum of 10 letters';
}
if(!isset($errors)){
    $image = $_FILES['picture']['name'];
    $image_size = $_FILES['picture']['size'];
    $image_error = $_FILES['picture']['error'];
    $file = explode('.',$image);
    $fileActual = strtolower(end($file));
    $allowed = array('png','jpg','jpge','svg');
    if(in_array($fileActual,$allowed)){
        if($image_error === 0){
            if($image_size < 4000000){
                    $image_new_name = uniqid('',true).'-'.$image;
                    $target = '../private/posts_picture/'.$image_new_name;
                    $create_at = date('Y-m-d H:i:s');
                    $sql = "INSERT INTO posts (text,picture,user_id,create_at) VALUES ('$text','$image_new_name','$id','$create_at')";
                    if(!empty($image)){
                        mysqli_query($conn,$sql);
                        if(move_uploaded_file($_FILES['picture']['tmp_name'],$target)){
                            header('location:welcome.php');
                        }
                    } 
                }else{
                $errors[] = 'Your picture is to Big';
            }
    
            }
    
        }else{
            $errors[] = 'Check picture type';
        }
}

}
?>
<head>
<link rel="stylesheet" href="../css/style.css" >
</head>

<div class="container">
<br><a class="btn btn-warning" href="#posts" >Add post</a><br><br>
<?php if(!empty($errors)): ?>

<div class="alert alert-danger">
<?php foreach($errors as $error): ?>
    <div><?php echo '- '.$error ;?></div>

<?php endforeach ?>
</div>

<?php endif; ?> 

<?php



$user_info = mysqli_query($conn,"SELECT * FROM `posts`");
while($data = mysqli_fetch_array($user_info)){
$user_id = $data['user_id'];
echo "<img src='../private/posts_picture/".$data['picture']."'' width='300px'  class='rounded' >";
echo '<p><br>'.$data['text'].'</p>
<p  style="color:grey">'.$data['create_at'].'</p>';

$user = mysqli_query($conn,"SELECT * FROM `users` WHERE id='$user_id'");
while($data_user= mysqli_fetch_array($user)){
    echo '<p class="btn btn-success">'.$data_user['username'].'<p>';
}




}
?>

<form action="" method="POST" enctype="multipart/form-data">
<h1 id="posts">Posts And Comments</h1>
<div class="mb-3">
  <label for="exampleFormControlTextarea1" class="form-label">Text</label>
  <textarea class="form-control" name="text" id="exampleFormControlTextarea1" rows="3"></textarea>
</div>
<label for="formFile" class="form-label">Update Profile Picture</label>
  <p>use this dimensions <span>750x750</span> For looks nice</p>
  <input class="form-control" name="picture" type="file" id="formFile">
  <br> <button name="submit" type="submit" class="btn btn-primary">Post</button>
</div>

</form>
<?php
}else{
    header('location:index.php');
}

?>