<?php
session_start();

if (!isset($_SESSION['loggedUser'])) {
    header('Location: login.php');
}

if ($_SESSION['loggedUserId'] == $_GET['id']) {

    include 'src/database.class.php';
    include 'src/tweet.class.php';
    include 'src/user.class.php';

    unset($_SESSION['logError']);
    ?>
    <head>
        <link rel="stylesheet" type="text/css" href="style/style.css">
    </head>

    <?php
    echo '<div class="col">';
    echo '<div class="menu">';
    echo 'zalogowany: ' . $_SESSION['loggedUser'];
    echo '<div class="photo">';
    echo '<img src="img/' . $_SESSION['loggedUserId'] . '.jpg"></img>';
    echo '</div><br><a href="logout.php">wyloguj</a><br>';
    echo '<br><a href="message_box.php">wiadomosci</a><br>';
    echo '<br><a href="index.php">Powrot do strony glownej</a><br></div></div>';

    echo '<div class="col">';
    echo 'Zaktualizuj profil';
    echo '<div class="message">';
    echo '<br><a href="profile.php?id=' . $_SESSION['loggedUserId'] . '&update=pass">zmien haslo</a><br>';
    echo '<br><a href="profile.php?id=' . $_SESSION['loggedUserId'] . '&update=email">zmien adres email</a><br>';
    echo '<br><a href="profile.php?id=' . $_SESSION['loggedUserId'] . '&update=name">zmien nazwe uzytkownika</a><br><br>';
    echo '</div><br></div>';

    switch ($_GET['update']) {
        case 'pass':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $password = $_POST['password'];
                $passwordRepeat = $_POST['passwordRepeat'];
                if (strlen($password) < 8) {
                    $_SESSION['password_error'] = 'Hasło musi liczyć co najmniej 8 znaków';
                    $regError = true;
                }
                if ($password != $passwordRepeat) {
                    $_SESSION['passwordRepeat_error'] = 'Hasła nie zgadzają się';
                    $regError = true;
                }
            }

            if (($_SERVER['REQUEST_METHOD'] === 'POST') && (!isset($regError))) {

                $conn = Database::Connection();
                $changedUser = User::loadUserById($conn, $_GET['id']);
                $changedUser->setPassword($_POST['password']);
                $changedUser->saveToDB($conn);
                $update = true;
                Database::endConnection($conn);
            }
            ?>
            <div class="col">
                <h4>Zmiana hasla</h4>
                <div class="form" style="width:275px">
                    <form method="post" action="#">
                        <label>wpisz nowe haslo</label><br>
                        <input type="password" name="password">
                        <div style="color: red">
                            <?php
                            if (isset($_SESSION['password_error'])) {
                                echo $_SESSION['password_error'];
                                unset($_SESSION['password_error']);
                            }
                            ?>
                        </div>            
                        <br>
                        <label>powtorz haslo</label><br>
                        <input type="password" name="passwordRepeat">
                        <div style="color: red">
                            <?php
                            if (isset($_SESSION['passwordRepeat_error'])) {
                                echo $_SESSION['passwordRepeat_error'];
                                unset($_SESSION['passwordRepeat_error']);
                            }
                            ?>
                        </div>
                        <br>
                        <button type="submit" name="submit" value="changepass">zapisz haslo</button>
                    </form>            
                </div>
                <?php
                if (isset($update)) {
                    echo 'pomyslnie zaktualizowano dane';
                    unset($update);
                }
                echo '</div>';
                break;
            case 'email':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                    $email = $_POST['email'];
                    $emailFiltered = filter_var($email, FILTER_SANITIZE_EMAIL);
                    if ((filter_var($emailFiltered, FILTER_VALIDATE_EMAIL) == false) || ($email != $emailFiltered)) {
                        $_SESSION['email_error'] = 'Niewlasciwy adres e-mail';
                        $regError = true;
                    }

                    $conn = Database::Connection();
                    $emailSql = "SELECT id FROM Users WHERE email='$email'";
                    $emailCheck = $conn->query($emailSql);
                    if (($emailCheck->num_rows) > 0) {
                        $_SESSION['email_error'] = 'podany adres e-mail jest juz uzywany';
                        $regError = true;
                    }
                    Database::endConnection($conn);
                }
                if (($_SERVER['REQUEST_METHOD'] === 'POST') && (!isset($regError))) {

                    $conn = Database::Connection();
                    $changedUser = User::loadUserById($conn, $_GET['id']);
                    $changedUser->setEmail($_POST['email']);
                    $changedUser->saveToDB($conn);
                    $update = true;
                    Database::endConnection($conn);
                }
                ?>
                <div class="col">
                    <h4>Zmiana adresu email</h4>
                    <div class="form" style="width:275px">
                        <form method="post" action="#">
                            <br>
                            <label>podaj nowy adres e-mail</label><br>
                            <input type="text" name="email">
                            <div style="color: red">
                                <?php
                                if (isset($_SESSION['email_error'])) {
                                    echo $_SESSION['email_error'];
                                    unset($_SESSION['email_error']);
                                }
                                ?>
                            </div>
                            <br>
                            <button type="submit" name="submit" value="changeemail">zapisz adres email</button>
                        </form>            
                    </div>
                    <?php
                    if (isset($update)) {
                        echo 'pomyslnie zaktualizowano dane';
                        unset($update);
                    }
                    echo '</div>';
                    break;
                case 'name':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                        unset($regError);
                        $username = $_POST['username'];
                        if (strlen($username) < 2) {
                            $_SESSION['username_error'] = 'Nazwa uzytkownika musi liczyc co najmniej 2 znaki';
                            $regError = true;
                        }

                        $conn = Database::Connection();
                        $usernameSql = "SELECT id FROM Users WHERE username='$username'";
                        $usernameCheck = $conn->query($usernameSql);
                        if (($usernameCheck->num_rows) > 0) {
                            $_SESSION['username_error'] = 'Ta nazwa uzytkownika jest juz uzywana';
                            $regError = true;
                        }
                        Database::endConnection($conn);
                    }

                    if (($_SERVER['REQUEST_METHOD'] === 'POST') && (!isset($regError))) {

                        $conn = Database::Connection();
                        $changedUser = User::loadUserById($conn, $_GET['id']);
                        $changedUser->setUsername($_POST['username']);
                        $changedUser->saveToDB($conn);
                        $_SESSION['loggedUser'] = $_POST['username'];
                        $update = true;
                        Database::endConnection($conn);
                    }
                    ?>
                    <div class="col">
                        <h4>Zmiana nazwy uzytkownika</h4>
                        <div class="form" style="width:275px">
                            <form method="post" action="#">
                                <br>
                                <label>podaj nowa nazwe</label><br>
                                <input type="text" name="username">
                                <div style="color: red">
                                    <?php
                                    if (isset($_SESSION['username_error'])) {
                                        echo $_SESSION['username_error'];
                                        unset($_SESSION['username_error']);
                                    }
                                    ?>
                                </div>
                                <br>
                                <button type="submit" name="submit" value="changename">zapisz nowa</button>
                            </form>            
                        </div>

                        <?php
                        if (isset($update)) {
                            echo 'pomyslnie zaktualizowano dane';
                            unset($update);
                        }
                        echo '</div>';
                        break;
                }
            } else {
                echo 'wystapil blad';
            }