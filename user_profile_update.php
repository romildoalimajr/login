<?php
include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_POST['update'])) {

    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);

    $update_profile = $conn->prepare("UPDATE `users` SET name = ?, email = ?
    WHERE id = ?");
    $update_profile->execute([$name, $email, $user_id]);

    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_folder = 'uploaded_img/' . $image;

    if (!empty($image)) {
        if ($image_size > 2000000) {
            $message[] = 'tamanho da imagem é muito grande!';
        } else {
            $update_image = $conn->prepare("UPDATE `users` SET image = ? WHERE id = ?");
            $update_image->execute([$image, $user_id]);

            if ($update_image) {
                move_uploaded_file($image_tmp_name, $image_folder);
                unlink('uploaded_img/' . $old_image);
                $message[] = 'image alterada com sucesso!';
            }
        }
    }

    $old_pass = $_POST['old_pass'];
    $previous_pass = md5($_POST['previous_pass']);
    $previous_pass = filter_var($previous_pass, FILTER_SANITIZE_STRING);
    $new_pass = md5($_POST['new_pass']);
    $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
    $confirm_pass = md5($_POST['confirm_pass']);
    $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

    if (!empty($previous_pass) || !empty($new_pass) || $empty($confirm_pass)) {
        if ($previous_pass != $old_pass) {
            $messsage[] = 'senha antiga não confere';
        } elseif ($new_pass != $confirm_pass) {
            $messsage[] = 'nova senha não confere';
        } else {
            $update_password = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_password->execute([$confirm_pass, $user_id]);
            $message[] = 'senha atualizada com sucesso';
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
    <title>Atualizar cadastro de usuário</title>
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
    <h1 class="title">update <span>user</span> profile</h1>

    <section class="update_profile_container">

        <?php
        $select_profile = $conn->prepare('SELECT * FROM `users` WHERE id = ?');
        $select_profile->execute([$user_id]);
        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
        ?>

        <form action="" method="post" enctype="multipart/form-data">
            <img src="uploaded_img/<?= $fetch_profile['image']; ?>" alt="">
            <div class="flex">

                <div class="inputBox">
                    <span>username.: </span>
                    <input type="text" required name="name" class="box" placeholder="seu nome" value="<?= $fetch_profile['name']; ?>">
                    <span>email.: </span>
                    <input type="email" required name="email" class="box" placeholder="seu email" value="<?= $fetch_profile['email']; ?>">
                    <span>profile pic.: </span>
                    <input type="hidden" name="old_image" value="<?= $fetch_profile['image']; ?>">
                    <input type="file" name="image" class="box" accept="image/jpg, image/png, image/jpeg">
                </div>

                <div class="inputBox">
                    <input type="hidden" name="old_pass" value="<?= $fetch_profile['password']; ?>">
                    <span>senha antiga.: </span>
                    <input type="password" name="previous_pass" class="box" placeholder="sua senha antiga">
                    <span>nova senha.: </span>
                    <input type="password" name="new_pass" class="box" placeholder="sua nova senha">
                    <span>repita nova senha.: </span>
                    <input type="password" name="confirm_pass" class="box" placeholder="repita nova senha">
                </div>
            </div>
            <div class="flex-btn">
                <input type="submit" value="Atualizar Cadastro" name="update" class="btn">
                <a href="user_page.php" class="option-btn">voltar</a>
            </div>
        </form>
    </section>
</body>

</html>