<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/bootstrap/icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <!-- Dùng chung style của Wall (navbar, background, card, ...) cho tất cả các trang -->
    <link href="assets/css/wallstyle.css" rel="stylesheet">
    <title><?=$data['page_title']?></title>
    <!-- Apply theme ngay từ đầu để tránh flash -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.write('<style>body{background-color:#121212!important;color:#e0e0e0!important}</style>');
            }
        })();
    </script>
</head>

<body>