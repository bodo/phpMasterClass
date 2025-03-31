<?php define('MAIN_SCRIPT', 1);       //protect the includes from URL-calling

use Utility\Utility;
use Model\User;

require_once "vendor/autoload.php";
require_once "utility.inc.php";



?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formular</title>

    <!-- Bootstrap CSS hinzufügen -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Reset */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #000; /* Black background */
            color: #ffffff; /* White text */
        }

        /* Header Styling */
        header {
            background: #1e1f22; /* Dark gray */
            padding: 20px;

        }

        header h1 {
            color: #ffffff;
            text-shadow : 1px 1px 10px #a6a6a6, 1px 1px 10px #424242;
            padding: 0;
            width: fit-content;
            margin: 0;
        }
        header h1 .btn {
            color: #000000;
            padding: 5px;
            border-radius: 4px;
            text-shadow: none;
        }

        header p {
            font-size: 13px;
            color: #777575;
        }

        /* Navigation Menu */
        nav {
            display: flex;
            justify-content: center;
            background: #111;
            padding: 10px 0;
        }

        nav a {
            color: #ffa500; /* Orange link text */
            text-decoration: none;
            padding: 10px 20px;
            transition: background 0.3s ease;
        }

        nav a:hover {
            background: #ffa500; /* Orange background on hover */
            color: #000; /* Black text on hover */
        }

        /* Content Section */
        .container {
            padding: 20px;
            text-align: center;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background: #ffa500; /* Orange button */
            color: #000; /* Black button text */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
        }

        .btn:hover {
            background: #e69500; /* Slightly darker orange on hover */
        }

        /* Footer Styling */
        footer {
            background: #1a1a1a; /* Dark gray */
            color: #aaa; /* Light gray text */
            text-align: center;
            padding: 10px;
            font-size: 14px;
        }

        footer a {
            color: #ffa500; /* Orange link */
            text-decoration: none;
        }

        blockquote {
            font-style: italic;

        }

        blockquote:before {
            content: open-quote;
        }

        blockquote:after {
            content: close-quote;
        }

        blockquote:before,
        blockquote:after {
            display: inline-block;
            vertical-align: bottom;
            color: #ffa500;
            font-size: 2em;
            top: .2em;
            position: relative;
        }

        table td, table th {
            border: 1px;
            font-size: 0.9em;
            padding: 0 10px;
        }
        table th {
            text-decoration: underline;
            text-decoration-color: #ffa500;
            padding: 0 10px 5px 10px;

        }
        table td a {
            color: #ffffff;
            text-decoration-color: #ffa500;
            text-decoration-style: wavy;
        }

        .container table {
            margin: auto;
        }

        /* Alternating row colours cycle-<period>-<item> */
        .c-3-0 {
            background-color: #8a6000;
        }
        .c-3-1 {
            background-color: #777777;
        }
        .c-3-2 {
            background-color: #353535;
        }
    </style>
</head>
<body>
<header>
    <h1>Dark <span class="btn">side</span></h1>
    <p>Viel zu lernen du noch hast.</p>
</header>

<nav>
    <a href="#">Home</a>
    <a href="#">Weisheit</a>
    <a href="#">Lichtschwert</a>
    <a href="#">Kontakt</a>
</nav>

<div class="container">
    <h2>Tu es oder tu es nicht. Es gibt kein Versuchen.</h2>
    <blockquote>Mit der Dunklen Seite der Macht stark du werden kannst. <br/>Doch einen hohen Preis sie verlangt.</blockquote>
    <a class="btn" href="?do=listUsers&maxAmount=25">List Users</a>
    <a class="btn" href="?do=generateFakes&amount=25">Generate 25 User</a>
    <a class="btn" href="?do=deleteMany&amount=25">Delete Last 25 Users</a>
</div>

<?php

$submitVars = ['name', 'password', 'email', 'color',
            'options[airbag]', 'options[klima]', 'options[abs]'];

$validEmail = '';
$validPassword = '';
$validBezahlung = '';
$colorAttribute = '';


$do = Utility::getVar('do', '');

switch ($do) {
    case 'generateFakes':
            $amount = Utility::getVar('amount', 25);
            $fakeItTillYouMakeIt = Faker\Factory::create();

            $userCollection = [];
            for($i=0; $i < $amount; $i++) {
                $vorname = $fakeItTillYouMakeIt->firstName();
                $nachname = $fakeItTillYouMakeIt->lastName();
                $age = $fakeItTillYouMakeIt->unique()->numberBetween(18, 109);

                $userCollection[$i] = new User($vorname, $nachname, $age);
                $userCollection[$i]->persist();
            }

            $userCollection = User::readUserList();
        break;
    case 'deleteUser':
            $id = Utility::getVar('id', '');
            User::deleteById($id);

            $userCollection = User::readUserList();
        break;
    case 'deleteMany':
            $amount = Utility::getVar('amount', 25);
            $userCollection = User::readUserList();

            $toDelete = array_slice($userCollection, 10, $amount);

            foreach ($toDelete as $key => $user) {
                User::deleteById($user->getID());
                //$user->delete();
                unset($userCollection[$key]);
            }

            //$userCollection = User::readUserList();
        break;
    case 'listUsers':
    default:
            $userCollection = User::readUserList();
        break;
}


if(isset($_POST['submit'])) {



    $submitName = Utility::getVar('name', '');
    $submitColor = Utility::getVar('color', '');
    $submitPassword = Utility::getVar('password', '');
    $submitAuto = Utility::getVar('auto', '');
    $submitOptions = Utility::getVar('options', []);
    $colorAttribute = $submitColor ? 'style="background-color: '.$submitColor.'"' : '';

    $validationErrors = isPasswordSecure(Utility::getVar('password', ''));

    if (empty($validationErrors)) {
        echo '<div class="alert alert-success">Das Passwort ist sicher.<br/>';
        $validPassword = 'is-valid';
    } elseif(count($validationErrors) == 1) {
        echo '<div class="alert alert-warning">Das Passwort könnte sicherer sein.';
        echo '<ul><li>'.implode('</li><li>', $validationErrors).'</li></ul>';
        echo '</div>';
        $validPassword = 'is-valid';
    } else {
        echo '<div class="alert alert-danger">Das Passwort ist sehr unsicherer.';
        echo '<ul><li>'.implode('</li><li>', $validationErrors).'</li></ul>';
        echo '</div>';
        $validPassword = 'is-invalid';
    }

    if (Utility::getVar('bezahlung') == 'paypal') {
        echo "<div class='alert alert-warning'>Der Service Paypal ist momentan nicht verfügbar. Wähle Barzahlung.</div>";
        $validBezahlung = 'is-valid';
    } elseif (Utility::getVar('bezahlung') == 'bar') {
        echo "<div class='alert alert-success'>Nur Bares ist wahres</div>";
        $validBezahlung = 'is-invalid';
    } else {
        $validBezahlung = 'is-valid';
    }


    $validationErrors = isValidEmail(Utility::getVar('email', ''));

    if (empty($validationErrors)) {
        echo '<div class="alert alert-success">OK, '.Utility::getVar('name').' Deine E-Mail sieht gut aus. Schau in Deiner Mailbox und klicke den Bestätigungslink an.<br/>';
        $validEmail = 'is-valid';
    } else {
        echo '<div class="alert alert-danger">Mit Deiner E-Mail gibt es ein Problem.';
        echo '<ul><li>'.implode('</li><li>', $validationErrors).'</li></ul>';
        echo '</div>';
        $validEmail = 'is-invalid';
    }

}


//var_dump($userColection);   exit();


?>
<div class="form-container" style="display: none;">
    <?php
    if(isset($_POST['submit'])) {
    ?>
    <div class="submit-feedback">
        <div class="mb-3">
            Name: <?php echo Utility::getVar('firstname').' '.Utility::getVar('lastname')?>
            Age: <?php echo Utility::getVar('age')?>
        </div>
    </div>

    <?php } ?>
    <h1>User</h1>

    <form name="anmeldung" method="POST" action="">
        <div class="mb-3">
            <?php  echo getProgressBar(3); ?>
        </div>

        <div class="mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="">Name</span>
                </div>
                <input type="text" name="firstname" class="form-control" placeholder="Vorname" value="<?php echo \Utility\Utility::getVar('firstname', '')?>">
                <input type="text" name="lastname" class="form-control" placeholder="Nachname" value="<?php echo \Utility\Utility::getVar('lastname', '')?>">
                <input type="text" name="age" class="form-control" placeholder="Alter" value="<?php echo \Utility\Utility::getVar('age', '')?>">
            </div>
        </div>


        <div>
            <label class="form-label">Account bezahlen</label>
            <div class="radio-container">
                <div>
                    <input type="radio" id="bar" name="bezahlung" value="bar">
                    <label for="bar" aria-description="Bar Bezahlung">Bar</label>
                </div>
                <div>
                    <input type="radio" id="paypal" name="bezahlung" value="paypal">
                    <label for="paypal" aria-description="PayPal Bezahlung">Paypal</label>
                </div>
            </div>
        </div>

        <button name="submit" class="btn btn-primary">OK</button>
    </form>
    <div class="text-muted">Alle Daten sind sicher bei uns. 😉</div>



</div>


<div class="container">
    <div class="mb-3">
        <?php
            echo "<h3>".count($userCollection)." User".(count($userCollection)>0 ? 's':'')."</h3>";
        ?>
        <table>
            <thead>
            <tr>
                <th>Vorname</th>
                <th>Nachname</th>
                <th>Alter</th>
                <th>Action</th>
            </thead>

            <tbody>
            <?php
            foreach($userCollection as $index => $user) {

                ?>
                <tr>
                    <td><?php echo $user->getFirstname(); ?></td>
                    <td><?php echo $user->getLastname(); ?></td>
                    <td><?php echo $user->getAge(); ?></td>
                    <td><a href="?do=deleteUser&id=<?php echo $user->getID(); ?>">delete</a></td>
                </tr>
                <?php

            }
            ?>
            </tbody>

        </table>

    </div>
</div>
<!-- Bootstrap JS hinzufügen -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>