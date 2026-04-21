<?php

	/**
	 * 
	 */
	class Query
	{
		
    public static function CRUD($query){
        $con = Connexion::GetConnexion();
    
        if (!$con) {
            throw new Exception("Connexion à la base de données échouée.");
        }
    
        $re = $con->prepare($query);
        $re->execute();
        return $re;
    }

	 public static function securisation($donnees){
        $donnees = htmlspecialchars($donnees);
        $donnees = trim($donnees);
        $donnees = stripslashes($donnees);
        $donnees = strip_tags($donnees);

        return $donnees;
    }

    public static function textArea($donnees)
    {
        $donnees = htmlspecialchars($donnees);
        $donnees = stripslashes($donnees);
        $donnees = strip_tags($donnees);
        return $donnees;
    }

    public static function validateur_email($email_new){
        $email = filter_var($email_new, FILTER_SANITIZE_EMAIL);
        if (filter_var($email_new, FILTER_VALIDATE_EMAIL) == true) {
            if($email_new != $email){
                return $email_new;
            }
            else{
                return $email;
            }

        }
	}
	
	 public static function sendMail($to, $headers, $subject, $body){
            mail($to,$subject,$body,$headers);
    }
	

}