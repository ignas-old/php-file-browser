<?php
session_start();

if ($_GET['action'] === 'logout') {
    session_destroy();
    session_start();
}

if (isset($_POST['login']) && !empty($_POST['username']) && !empty($_POST['password'])) {

    $_SESSION['logged_in'] = true;
    $_SESSION['time'] = time();
    $_SESSION['username'] = $_POST['username'];
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Browser</title>
</head>
<style>
    * {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    table td,
    table th {
        border: 1px solid #000;
        padding: 8px;
    }

    table th:first-child {
        width: 70%;
    }

    table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    table tr:hover {
        background-color: #ddd;
    }

    table th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #39408b;
        color: white;
    }

    .button {
        display: inline-block;
        background-color: #888;
        border: 1px solid #000;
        padding: 5px;
        text-decoration: none;
        color: #000;
    }
</style>

<body>
    <form action="./" method="POST" style="<?php !isset($_SESSION['logged_in'])
                                                ? print("display: block")
                                                : print("display: none")
                                            ?>">
        <h4>Enter your log in details: (Anything works for now)</h4>
        <input type="text" name="username" placeholder="username = anything" required autofocus></br>
        <input type="password" name="password" placeholder="password = anything" required>
        <button type="submit" name="login" style="display: block">Login</button>
    </form>

    <?php

    // Display directory items if logged in

    if ($_SESSION['logged_in']) {
        print('<a class="button" href="?action=logout">Log out</a>');

        $dir = $_GET['dir'];

        //Check user OS and set default directory

        if (!isset($dir) || $dir === '' || $dir === 'C:') {
            $agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/Linux/', $agent)) $dir = '/';
            elseif (preg_match('/Win/', $agent)) $dir = 'C:/';
            elseif (preg_match('/Mac/', $agent)) $dir = '/';
        }

        print('<h2>Directory: ' . $dir . '</h2>');

        //Delete file

        $delete = $_GET['delete'];

        if (isset($delete)) {
            if (unlink($dir . '/' . $delete)) {
                print('<div>File was deleted!</div>');
            } else {
                print('<div>Failed to delete file. (File does not exist or you do not have permission)</div>');
            }
        }

        //Make new directory

        $new_dirname = $_GET['new_dirname'];

        if (isset($new_dirname) && $new_dirname != '') {
            if (!mkdir($dir . '/' . $new_dirname, preg_match('/Win/', $_SERVER['HTTP_USER_AGENT'])
                ? null
                : 0777)) {
                print('<div style="color: red;">Failed to create folder! It already exists or you do not have permission.</div>');
            } else {
                print('<div style="color: green;">Created new folder!</div>');
            }
        }

        $dir_items = scandir($dir);

        if (!$dir_items) {
            print '<h4>Invalid directory or you do not have permission to access it!<h4>';
        } else {
            print('<table><th>Name</th><th>Type</th><th>Actions</th>');
            foreach ($dir_items as $item) {
                if ($item != ".." && $item != ".") {
                    print('<tr>');
                    print('<td>' . (is_dir($dir . '/' . $item)
                        ? '<a href="?dir=' . $dir . "/" . $item . '">' . $item . '</a>'
                        : $item)
                        . '</td>');
                    print('<td>' . (is_dir($dir . '/' . $item) ? "Folder" : "File") . '</td>');
                    print('<td>' . (is_dir($dir . '/' . $item)
                        ? ''
                        : '<a class="button" style="background-color: red;" href="?dir=' . $dir . '&delete=' . $item . '">Delete</a>') . '</td>');
                    print('</tr>');
                }
            }
            print("</table>");
        }

        print('<form action="./" method="GET">');
        print('<input type="text" name="dir" value="' . $dir . '" style="display: none;" required>');
        print('<input type="text" name="new_dirname" required>');
        print('<button type="submit" style="display: block;">New Folder</button>');
        print('</form>');

        for ($i = -1; $i > (0 - strlen($dir)); $i--) {
            if ($dir[$i] === '/') {
                $dir = substr($dir, 0, $i);
                break;
            }
        }
        print('<br>');
        print('<a class="button" href="?dir=' . $dir . '">Back</a>');
    }

    ?>



</body>

</html>