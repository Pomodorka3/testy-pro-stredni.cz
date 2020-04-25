<?php
    $email_to = "berezovsky14@gymnazium-prazacka.cz";
    //$email_to ="xxx@mail-tester.com";
    $email_subject = "Aktivace účtu";
    $email_body = "Aktivsdaof kxcokzxcsda úsdasdaais kdmwnbdjjwasd čtu: https://testyasdasdasda-pro-stredni.cz/validate?hash=31fefc0e570cb3860f2a6d4b38c6490d&emasdail=lenkasda.sviasdatak@seznam.cz";


    /* if(mail($email_to, $email_subject, $email_body)){
        echo "Aktivace účtu: https://testy-pro-stredni.cz/validate?hash=31fefc0e570cb3860f2a6d4b38c6490d&email=lenka.svitak@seznam.cz";
    } else {
        echo "The email (Subject: $email_subject) was NOT sent to $email_to.";
    } */

    mail($email_to, 'TESTik', $email_body);	
?>