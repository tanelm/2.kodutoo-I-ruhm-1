<?php

    require("../../config.php");
	// functions.php
	//var_dump($GLOBALS);
	
	// see fail, peab olema kõigil lehtedel kus 
	// tahan kasutada SESSION muutujat
	session_start();
	
	//***************
	//**** SIGNUP ***
	//***************
   
   
   	$database = "if16_Tanelmaas_1";
	
	function signup ($email, $password) {
		
		$mysqli = new mysqli($GLOBALS["serverHost"],$GLOBALS["serverUsername"],$GLOBALS["serverPassword"],$GLOBALS["database"]);
		$stmt = $mysqli->prepare("INSERT INTO user_sample (email, password) VALUES (?, ?)");
		echo $mysqli->error;
		$stmt->bind_param("ss", $email, $password);
		
		if ($stmt->execute()) {
			echo "Salvestamine õnnestus";
		} else {
			echo "ERROR ".$stmt->error;
		}
		
	}
	
	
	function login($email, $password) {
		
		$error = "";
		
		$mysqli = new mysqli($GLOBALS["serverHost"],$GLOBALS["serverUsername"],$GLOBALS["serverPassword"],$GLOBALS["database"]);
		$stmt = $mysqli->prepare("
			SELECT id, email, password, created 
			FROM user_sample
			WHERE email = ?
		");
		echo $mysqli->error;
		
		//asendan küsimärgi
		$stmt->bind_param("s", $email);
		
		//määran tupladele muutujad
		$stmt->bind_result($id, $emailFromDb, $passwordFromDb, $created);
		$stmt->execute();
		
		//küsin rea andmeid
		if($stmt->fetch()) {
			//oli rida
		
			// võrdlen paroole
			$hash = hash("sha512", $password);
			if($hash == $passwordFromDb) {
				
				echo "Kasutaja ".$id." logis sisse";
				
				
				$_SESSION["userId"] = $id;
				$_SESSION["email"] = $emailFromDb;
				
				//suunaks uuele lehele
				header("Location: data.php");
				exit();
				
			} else {
				$error = "Parool vale";
			}
			
		
		} else {
			//ei olnud 
			
			$error = "Sellise emailiga ".$email." kasutajat ei olnud";
		}
		
		
		return $error;
		
		
	}
	
	
	function saveGoals ($city, $street, $area, $rooms) {
		
		$mysqli = new mysqli($GLOBALS["serverHost"],$GLOBALS["serverUsername"],$GLOBALS["serverPassword"],$GLOBALS["database"]);
		$stmt = $mysqli->prepare("INSERT INTO korterid (city, street, area, rooms) VALUES (?, ?, ?, ?)");
		echo $mysqli->error;
		$stmt->bind_param("ssii", $city, $street, $area, $rooms);
		
		if ($stmt->execute()) {
			echo "Salvestamine õnnestus";
		} else {
			echo "ERROR ".$stmt->error;
		}
		
	}
	
	
	function getAllGoals () {
		
		$mysqli = new mysqli($GLOBALS["serverHost"],$GLOBALS["serverUsername"],$GLOBALS["serverPassword"],$GLOBALS["database"]);
		$stmt = $mysqli->prepare("
			SELECT id, city, street, area, rooms
			FROM korterid
		");
		echo $mysqli->error;
		
		$stmt->bind_result($id, $city, $street, $area, $rooms);
		$stmt->execute();
		
		// array("Romil", "R")
		$result = array();
		
		// seni kuni on üks rida andmeid saada (10 rida = 10 korda)
		while ($stmt->fetch()) {
			
			$person = new StdClass();
			$person->id = $id;
			$person->street = $street;
			$person->area = $area;
			$person->rooms = $rooms;
			$person->city = $city;
			
			//echo $color."<br>";
			array_push($result, $person);
		}
		
		$stmt->close();
		$mysqli->close();
		
		return $result;
		
	}
	
	function cleanInput($input) {
		
		//input = "romiL@tlu.ee   "
		
		$input = trim($input);
		
		//input = "romiL@tlu.ee"
			
		$input = stripslashes($input);
		$input = htmlspecialchars($input);
		return $input;
		
	}
	
	
	
	
	/*function sum ($x, $y) {
		
		return $x + $y;
		
	}
	
	function hello ($firstname, $lastname) {
		
		return "Tere tulemast ".$firstname." ".$lastname."!";
		
	}
	
	echo sum(5476567567,234234234);
	echo "<br>";
	$answer = sum(10,15);
	echo $answer;
	echo "<br>";
	echo hello ("Romil", "R.");
	*/
	
	
	/*
	
	function issetAndNotEmpty($var) {	
		if ( isset ( $var ) ) {
			if ( !empty ($var ) ) {
				return true;			
			}	
		} 
		
		return false;	
	}
	
	if (issetAndNotEmpty($_POST["loginEmail"])) {
		
		//vastab tõele
		
	}
	
	
	
	
	*/
