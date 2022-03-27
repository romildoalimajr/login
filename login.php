<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $pass = md5($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $select = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
    $select->execute([$email, $pass]);
    $row = $select->fetch(PDO::FETCH_ASSOC);

    if ($select->rowCount() > 0) {
        if ($row['user_type'] == 'admin') {

            $_SESSION['admin_id'] = $row['id'];
            header('location:admin_page.php');

        } elseif ($row['user_type'] == 'user') {
            
            $_SESSION['user_id'] = $row['id'];
            header('location:user_page.php');

        } else {
            $message[] = 'nenhum usuário encontrado';
        }
    } else {
        $message[] = 'email e/ou senha incorreto!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="shortcut icon" type="img/x-icon" href="./img/kalangos.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '
            <div class="message">
                <span>' . $message . '</span>   
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>  
            ';
        }
    }
    ?>
    <section class="form-container">
        <form action="" method="POST" enctype="multipart/form-data">
            <h3>faça seu login</h3>
            <input type="email" placeholder="seu email" required class="box" name="email">
            <input type="password" placeholder="sua senha" required class="box" name="pass">
            <p>não tem uma conta? <a href="register.php">registre aqui</a></p>
            <input type="submit" value="entrar agora" class="btn" name="submit">

        </form>
    </section>
</body>

</html>