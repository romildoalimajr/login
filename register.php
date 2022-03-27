<?php
    include 'config.php';

    if(isset($_POST['submit'])){
        $name = $_POST['name'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $email = $_POST['email'];
        $email = filter_var($email, FILTER_SANITIZE_STRING);
        $pass = md5($_POST['pass']);
        $pass = filter_var($pass, FILTER_SANITIZE_STRING);
        $cpass = md5($_POST['cpass']);
        $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);
        
        $image = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_folder = 'uploaded_img/'.$image;

        $select = $conn -> prepare("SELECT * FROM `users` WHERE email = ?");
        $select -> execute([$email]);

        if($select->rowCount() > 0){
            $message[] = 'usuário já cadastrado!';
        }else{
            if($pass != $cpass){
                $message[] = 'Senha não confere!';
            }elseif($image_size > 20000000){
                $message[] = 'o tamanho da imagem é muito grande!';
            }else{
                $insert = $conn -> prepare("INSERT INTO `users` (name, email, password, image) VALUES(?,?,?,?)");
                $insert->execute([$name, $email, $pass, $image]);
                if($insert){
                    move_uploaded_file($image_tmp_name, $image_folder);
                    $message[] = 'registrado com sucesso!';
                    header('location:login.php');
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registro</title>
    <link rel="shortcut icon" type="img/x-icon" href="./img/kalangos.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<?php
    if(isset($message)){
        foreach($message as $message){
            echo '
            <div class="message">
                <span>'.$message.'</span>   
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>  
            ';
        }
    }
?>
    <section class="form-container">
        <form action="" method="POST" enctype="multipart/form-data">
            <h3>register now</h3>
            <input type="text" placeholder="seu nome" required class="box" name="name">
            <input type="email" placeholder="seu email" required class="box" name="email">
            <input type="password" placeholder="sua senha" required class="box" name="pass">
            <input type="password" placeholder="repita sua senha" required class="box" name="cpass">
            <input type="file" required class="box" accept="image/jpg, image/png, image/jpeg" name="image">
            <p>Já tem uma conta? <a href="login.php">entre aqui</a></p>
            <input type="submit" value="registre agora" class="btn" name="submit">

        </form>
    </section>
</body>

</html>