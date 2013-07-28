<?php

$connection = ssh2_connect('localhost', 22); // IP DU SERVEUR LINUX
ssh2_auth_password($connection, 'root', 'mot de passe'); // LOGS DE CONNEXION AU SERVEUR LINUX

$cpuu = round(file_get_contents("/sys/devices/system/cpu/cpu0/cpufreq/scaling_cur_freq") / 1000); // Récupération de la fréquence actuelle
$cpu = $cpuu / 1000;
 
$cpuumax = round(file_get_contents("/sys/devices/system/cpu/cpu0/cpufreq/scaling_max_freq") / 1000); // Récupération de la fréquence maximale
$cpumax =  $cpuumax / 1000;

if($cpumax - $cpu < 0.2) { // Réglez comme vous le souhaitez

	$mail = 'tlecoue@gmail.com'; // Votre adresse email
	
	if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) // Vérification de l'adrese email
		$passage_ligne = "\r\n";
	else
		$passage_ligne = "\n";

	$message_html = "<html><head></head><body>SURCHARGE CPU SUR LE SERVEUR !!</body></html>"; // Texte du mail
	$boundary = "-----=".md5(rand());
	$sujet = "Monitoring CPU"; // SUJET DU MAIL

	$header = "From: \"Monitoring\"<"$mail">".$passage_ligne;
	$header.= "Reply-to: \"Monitoring\" <"$mail">".$passage_ligne;
	$header.= "MIME-Version: 1.0".$passage_ligne;
	$header.= "Content-Type: multipart/alternative;".$passage_ligne." boundary=\"$boundary\"".$passage_ligne;

	$message = $passage_ligne."--".$boundary.$passage_ligne;
	$message.= "Content-Type: text/plain; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_txt.$passage_ligne;
	$message.= $passage_ligne."--".$boundary.$passage_ligne;
	$message.= "Content-Type: text/html; charset=\"ISO-8859-1\"".$passage_ligne;
	$message.= "Content-Transfer-Encoding: 8bit".$passage_ligne;
	$message.= $passage_ligne.$message_html.$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
	$message.= $passage_ligne."--".$boundary."--".$passage_ligne;

	mail($mail,$sujet,$message,$header);
	
	ssh2_exec($connection, 'sudo service nginx restart');
	ssh2_exec($connection, 'sudo service php5-fpm restart')

}

?>
