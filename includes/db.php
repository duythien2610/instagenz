<?php
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '#thiencuimuc#1';
const DB_NAME = 'project_mini_web';

//Kết nối 
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if(!$db){
    die("LỖI KẾT NỐI DB: " .mysqli_connect_error());
}

mysqli_set_charset($db, "utf8mb4");