<?php
    include('./pdoLink.php');
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $job = $_POST['job'];
    $phone = $_POST['phone'];
    $name = $_POST['name'];
    $sql = 'insert into admin(a_username,a_password,a_level,a_phone,a_name,a_key) ';
    $sql.= 'values(:user,:pass,:level,:phone,:name,:key)';
    $query = $db->prepare($sql);
    $query->bindValue(':user',$user);
    $query->bindValue(':pass',$pass);
    $query->bindValue(':level',$job);
    $query->bindValue(':phone',$phone);
    $query->bindValue(':name',$name);
    $query->bindValue(':key',md5(date('His')));
    $query->execute();
    echo "註冊成功";
?>