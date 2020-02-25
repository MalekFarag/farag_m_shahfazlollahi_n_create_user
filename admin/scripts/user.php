<?php


function createuser($fname, $username, $email){
    
    $pdo = Database::getInstance()->getConnection();

     // check user existance
     $check_email_query = 'SELECT COUNT(user_name) AS num FROM tbl_user WHERE user_name = :username'; 
     $user_set = $pdo->prepare($check_email_query);
     $user_set->execute(
         array(
             ':username'=>$username
         )
     );
 
     $row = $user_set->fetch(PDO::FETCH_ASSOC);

     if($row['num'] > 0){
        $message = 'username is already registered';
    }else{
        //creating password for user
        $password = md5(rand(0,1000)); 

        //phpmailer config
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPSecure='ssl';
        $mail->Port = 465;
        $mail->SMTPAuth=true;

        $mail->Username='faragmalek14@gmail.com';
        $mail->Password='Malooky14'; // please dont steal my password. I really dont want to change it

        $mail->addAddress($email);
        $mail->setFrom('faragmalek14@gmail.com');
        

        $mail->isHTML(true);
        $mail->Subject='Created User | Nick & Malek Research'; 
        $mail->Body='

        Hello from Nick & Malek! <br><br>

        Thanks for signing up!<br><br>
        Your account admin user has been Created!
        <br><br><br>
        ------------------------<br>
        Here are your login credentials!<br>
        Email: '.$username.'<br>
        Password: '.$password.'<br><br>

        Login at http://localhost/farag_m_shahfazlollahi_n_create_user-master/admin/admin_login.php <br>
        ------------------------<br>
        <br><br><br>
        ';

        if(!$mail->send()){
            $message= $mail->ErrorInfo;
            return 'user creation did not got through';
        }else{
            //creating user sql query from form details
            $create_user_query = "INSERT INTO tbl_user (user_id, first_name, user_name, user_email, user_password, user_ip) VALUES (NULL, :fname, :username, :email, :password, 'no');";

            $user_signup = $pdo->prepare($create_user_query);
            $user_signup->execute(
                array(
                    ':fname'=>$fname,
                    ':username'=>$username,
                    ':email'=>$email,
                    ':password'=>$password
                )
            );
            
            redirect_to('index.php');
            $message = 'created user';
        }
    }
}
