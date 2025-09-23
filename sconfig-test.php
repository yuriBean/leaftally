
<?php

function getPermissionString($perms) {
    $info = 'u=';
    $info .= ($perms & 0400) ? 'r' : '-';
    $info .= ($perms & 0200) ? 'w' : '-';
    $info .= ($perms & 0100) ? 'x' : '-';
    $info .= ', g=';
    $info .= ($perms & 0040) ? 'r' : '-';
    $info .= ($perms & 0020) ? 'w' : '-';
    $info .= ($perms & 0010) ? 'x' : '-';
    $info .= ', o=';
    $info .= ($perms & 0004) ? 'r' : '-';
    $info .= ($perms & 0002) ? 'w' : '-';
    $info .= ($perms & 0001) ? 'x' : '-';
    return $info;
}

function checkAndSetFolderPermissions($folderPath, $requiredPerms) {
    $folderPerms = fileperms($folderPath);

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $command = "cacls \"$folderPath\" /T /E /C /G everyone:F";
        exec($command);
    } elseif (($folderPerms & 0777) !== $requiredPerms) {
        chmod($folderPath, $requiredPerms);
    }

    $folderPerms = fileperms($folderPath);

    return ($folderPerms & 0777) === $requiredPerms;
}

function checkExtensionLoaded($extensionName) {
    return extension_loaded($extensionName);
}

$foldersToCheck = [
    'resources/lang',
    'bootstrap/cache',
    'storage',
];

$requiredFolderPerms = 0777;

$extensionsToCheck = [
    'bcmath',
    'curl',
    'dom',
    'fileinfo',
    'gd',
    'imagick',
    'imap',
    'mbstring',
    'mcrypt',
    'mysqlnd',
    'nd_mysqli',
    'nd_pdo_mysql',
    'pdo',
    'pdo_sqlite',
    'zip',
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Configuration Check</title>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        table {
            border-collapse: collapse;
            width: 50%;
            margin: 20px;
        }

        table, th, td {
            border: 1px solid
            padding: 10px;
            text-align: left;
        }

        th {
            background-color:
        }
    </style>
</head>
<body>

    <div class="container">
    <h2>System Configuration Check</h2>

        <table class="table table-striped table-bordered table-hover table-sm">
            <tr>
                <th>Configuration</th>
                <th>Current Status</th>
                <th>Required Status</th>
            </tr>

            <?php foreach ($foldersToCheck as $folderPath): ?>
                <tr>
                    <td><?php echo $folderPath; ?> Permissions</td>
                    <td><?php echo checkAndSetFolderPermissions($folderPath, $requiredFolderPerms) ? 'OK' : getPermissionString(fileperms($folderPath)); ?></td>
                    <td>At least 777</td>
                </tr>
            <?php endforeach; ?>

            <?php foreach ($extensionsToCheck as $extensionName): ?>
                <tr>
                    <td><?php echo $extensionName; ?> Extension</td>
                    <td class="<?php echo checkExtensionLoaded($extensionName) ? 'table-success' : 'table-danger'; ?>"><?php echo checkExtensionLoaded($extensionName) ? 'OK' : 'Not OK'; ?></td>
                    <td>OK</td>
                </tr>
            <?php endforeach; ?>

        </table>
    </div>

</body>
</html>
