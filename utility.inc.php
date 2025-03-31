<?php
if(!defined('MAIN_SCRIPT'))   die('Go away utility!');


spl_autoload_register(function ($interfaceName) {
    $interfaceName = str_replace('\\', '/', $interfaceName);
    $fileName = stream_resolve_include_path('MasterClasses/' . $interfaceName . '.interface.php');
    if ($fileName !== false) {
        include $fileName;
    }
});
spl_autoload_register(function ($traitName) {
    $traitName = str_replace('\\', '/', $traitName);
    $fileName = stream_resolve_include_path('MasterClasses/' . $traitName . '.trait.php');
    if ($fileName !== false) {
        include $fileName;
    }
});

spl_autoload_register(function ($className) {
    $className = str_replace('\\', '/', $className);
    $fileName = stream_resolve_include_path('MasterClasses/' . $className . '.class.php');
    if ($fileName !== false) {
        include_once $fileName;
    }
});
/*
spl_autoload_register(function ($creatureClassName) {
    $creatureClassName = str_replace('\\', '/', $creatureClassName);
    $fileName = stream_resolve_include_path('MasterClasses/' . $creatureClassName . '.class.php');
    if ($fileName !== false) {
        include $fileName;
    }
});
*/
spl_autoload_register(function ($UtilityClassName) {
    $UtilityClassName = str_replace('\\', '/', $UtilityClassName);
    $fileName = stream_resolve_include_path('MasterClasses/Utility/' . $UtilityClassName . '.class.php');
    if ($fileName !== false) {
        include $fileName;
    }
});


function getVerbindung(): \PDO {
    static $pdo;

    if (!$pdo) {
        $config = require("env.inc.php");

        $pdo = new PDO("mysql:host={$config['dbservername']};dbname={$config['dbname']};charset=utf8",
            $config['dbusername'], $config['dbpassword']);

        // Fehler-Modus setzen (Optionale Sicherheit/Debugging-Ebene)
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    return $pdo;
}






function isPasswordSecure(string $password): array  {
    $errors = [];

    // Länge prüfen (mindestens 8 Zeichen)
    if (strlen($password) < 8) {
        $errors[] = "Das Passwort muss mindestens 8 Zeichen lang sein.";
    }

    // Muss mindestens einen Großbuchstaben enthalten
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Das Passwort muss mindestens einen Großbuchstaben enthalten.";
    }

    // Muss mindestens einen Kleinbuchstaben enthalten
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Das Passwort muss mindestens einen Kleinbuchstaben enthalten.";
    }

    // Muss mindestens eine Zahl enthalten
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Das Passwort muss mindestens eine Zahl enthalten.";
    }

    // Muss mindestens ein Sonderzeichen enthalten
    if (!preg_match('/[\W_]/', $password)) {
        $errors[] = "Das Passwort muss mindestens ein Sonderzeichen enthalten (z. B. !@#$%^&*).";
    }

    // Gibt ein Array mit den fehlgeschlagenen Prüfungen zurück
    return $errors;
}


function isValidEmail(string $email): array  {
    $errors = [];

    // Prüfen, ob die E-Mail leer ist
    if (empty($email)) {
        $errors[] = "Die E-Mail-Adresse darf nicht leer sein.";
    }

    // Gültige E-Mail-Formatierung überprüfen
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Die E-Mail-Adresse ist ungültig.";
    }

    // Optional: Länge prüfen
    if (strlen($email) > 254) {
        $errors[] = "Die E-Mail-Adresse darf nicht länger als 254 Zeichen sein.";
    }

    return $errors;
}

function getCheckbox(string $label, string $variable, string $name, string $additionAttributes=''): string {
    if($name == '') {
        $name = $label;
    }

    $vars = getVar($variable);
    $active = isset($vars[$name]) && $variable ? ' checked ' : '';

    $id = $variable.'-'.$name;

    $res =  '
    <label class="form-label" for="'.$id.'">'.$label.'</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input id="'.$id.'" type="checkbox" aria-label="'.$name.'" name="'.$variable.'['.$name.']" value="1" '.$active.'>
                    </div>
                </div>
            </div>
    ';

    return $res;
}
function getCheckboxValue(string $label, string $variable, string $name, ?string $einheit=NULL): string {
    if($name == '') {
        $name = $label;
    }

    $vars = getVar($variable);
    $active = isset($vars[$name]) && $variable ? ' checked ' : '';
    $amount = getVar('amount_'.$variable.'['.$name.']');

    $id = $variable.'-'.$name;

    $res = '<label class="form-label" for="'.$id.'">'.$label.'</label><br/>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input id="'.$id.'" type="checkbox" aria-label="'.$name.'" name="'.$variable.'['.$name.']" value="1" '.$active.'>
                    </div>
                </div>';

    if($einheit !== NULL) {
        $res .= '<input type="text" class="form-control" name="amount_'.$variable.'['.$name.']" '
                .'value="'.$amount.'" aria-label="Menge '.$label.'" placeholder="Wieviel '.$label.'?">';

        if($einheit !=='') {
          $res .= '<div class="input-group-append"><span class="input-group-text">'.$einheit.'</span></div>';
        }
    }
    $res .= '</div>';

    return $res;
}


function getSelect(string $label, string $variable, array $items, string $additionalAttributes=''): string {

    if(!stristr($additionalAttributes, 'size')) $additionalAttributes .= ' size="1" ';

    $res = '<div class="mb-3"><label class="form-label" for="sel_'.$variable.'">'.$label.'</label>
            <select class="form-control" id="sel_'.$variable.'" name="'.$variable.'" '.$additionalAttributes.'>';

    foreach($items as $key => $value) {
        $res .= '<option value="'.$key.'">'.$value.'</option>';
    }

    $res .= '</select></div>';

    return $res;
}

function getProgressBar(int $percent): string {

    $percent = min(100, $percent < 5 ? 1 : $percent);

    if($percent < 10) {
        $color = '';
    } elseif($percent <= 25) {
        $color = ' bg-warning';
    } elseif($percent > 90) {
        $color = ' bg-success';
    } else {
        $color = ' bg-info';
    }

    return '
    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated'.$color.'" role="progressbar" style="width: '.$percent.'%" aria-valuenow="'.$percent.'" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    ';
}


