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
	
	function signUp ($email, $password) {
		
		$database = "if16_Tanelmaas_1";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $database);

		$stmt = $mysqli->prepare("INSERT INTO user_sample (email, password) VALUES (?, ?)");
	
		echo $mysqli->error;
		
		$stmt->bind_param("ss", $email, $password);
		
		if($stmt->execute()) {
			echo "Salvestamine õnnestus";
		} else {
		 	echo "ERROR ".$stmt->error;
		}
		
		$stmt->close();
		$mysqli->close();
		
	}
	
	
	function login ($email, $password) {
		
		$error = "";
		
		$database = "if16_Tanelmaas_1";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $database);

		$stmt = $mysqli->prepare("
		SELECT id, email, password, created 
		FROM user_sample
		WHERE email = ?");
	
		echo $mysqli->error;
		
		//asendan küsimärgi
		$stmt->bind_param("s", $email);
		
		//määran väärtused muutujatesse
		$stmt->bind_result($id, $emailFromDb, $passwordFromDb, $created);
		$stmt->execute();
		
		//andmed tulid andmebaasist või mitte
		// on tõene kui on vähemalt üks vaste
		if($stmt->fetch()){
			
			//oli sellise meiliga kasutaja
			//password millega kasutaja tahab sisse logida
			$hash = hash("sha512", $password);
			if ($hash == $passwordFromDb) {
				
				echo "Kasutaja logis sisse ".$id;
				
				//määran sessiooni muutujad, millele saan ligi
				// teistelt lehtedelt
				$_SESSION["userId"] = $id;
				$_SESSION["userEmail"] = $emailFromDb;
				
				$_SESSION["message"] = "<h1>Tere tulemast!</h1>";
				
				header("Location: data.php");
				exit();
				
			}else {
				$error = "Vale parool";
			}
			
			
		} else {
			
			// ei leidnud kasutajat selle meiliga
			$error = "ei ole sellist emaili";
		}
		
		return $error;
		
	}
	
	
	function saveApartment ($location, $area, $rooms) {
		
		$database = "if16_Tanelmaas_1";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $database);

		$stmt = $mysqli->prepare("INSERT INTO korterid (location, area, rooms) VALUES (?, ?, ?)");
	
		echo $mysqli->error;
		
		$stmt->bind_param("sii", $location, $area, $rooms);
		
		if($stmt->execute()) {
			echo "salvestamine õnnestus";
		} else {
		 	echo "ERROR ".$stmt->error;
		}
		
		$stmt->close();
		$mysqli->close();
		
	}
	
	
	function getAllApartments() {
		
		$database = "if16_Tanelmaas_1";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $database);
		
		$stmt = $mysqli->prepare("
			SELECT id, location, area, rooms
			FROM korterid
		");
		echo $mysqli->error;
		
		$stmt->bind_result($id, $location, $area, $rooms);
		$stmt->execute();
		
		
		//tekitan massiivi
		$result = array();
		
		// tee seda seni, kuni on rida andmeid
		// mis vastab select lausele
		while ($stmt->fetch()) {
			
			//tekitan objekti
			$apartment = new StdClass();
			
			$apartment->id = $id;
			$apartment->location = $location;
			$apartment->area = $area;
			$apartment->rooms = $rooms;
			
			//echo $plate."<br>";
			// iga kord massiivi lisan juurde nr märgi
			array_push($result, $apartment);
		}
		
		$stmt->close();
		$mysqli->close();
		
		return $result;
	}
	
	function cleanInput($input){
		
		$input = trim($input);
		$input = stripslashes($input);
		$input = htmlspecialchars($input);
		
		return $input;
		
	}
	
	function saveInterest ($interest) {
		
		$database = "if16_Tanelmaas_1";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $database);

		$stmt = $mysqli->prepare("INSERT INTO interests (interest) VALUES (?)");
	
		echo $mysqli->error;
		
		$stmt->bind_param("s", $interest);
		
		if($stmt->execute()) {
			echo "salvestamine õnnestus";
		} else {
		 	echo "ERROR ".$stmt->error;
		}
		
		$stmt->close();
		$mysqli->close();
		
	}
	
	function saveUserInterest ($interest_id) {
		
		echo "huviala: ".$interest_id."<br>";
		echo "kasutaja: ".$_SESSION["userId"]."<br>";
		
		$database = "if16_Tanelmaas_1";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $database);

		//kas on juba olemas
		
		$stmt = $mysqli->prepare("
			SELECT id FROM user_interest 
			WHERE user_id=? AND interest_id=?
		");
		$stmt->bind_param("ii", $_SESSION["userId"], $interest_id);
		$stmt->execute();
		
		if ($stmt->fetch()) {
			// oli olemas 
			echo "Juba olemas";
			
			//ära salvestamisega jätka
			return;
		}
	
		$stmt->close();
		// jätkan salvestamisega...
		
		$stmt = $mysqli->prepare("
			INSERT INTO user_interest
			(user_id, interest_id) VALUES (?, ?)
		");
	
		echo $mysqli->error;
		
		$stmt->bind_param("ii", $_SESSION["userId"], $interest_id);
		
		if($stmt->execute()) {
			echo "Salvestamine õnnestus";
		} else {
		 	echo "ERROR ".$stmt->error;
		}
		
		$stmt->close();
		$mysqli->close();
		
	}
	
	function getAllInterests() {
		
		$database = "if16_Tanelmaas_1";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $database);
		
		$stmt = $mysqli->prepare("
			SELECT id, interest
			FROM interests
		");
		echo $mysqli->error;
		
		$stmt->bind_result($id, $interest);
		$stmt->execute();
		
		
		//tekitan massiivi
		$result = array();
		
		// tee seda seni, kuni on rida andmeid
		// mis vastab select lausele
		while ($stmt->fetch()) {
			
			//tekitan objekti
			$i = new StdClass();
			
			$i->id = $id;
			$i->interest = $interest;
		
			array_push($result, $i);
		}
		
		$stmt->close();
		$mysqli->close();
		
		return $result;
	}
	
	function getAllUserInterests() {
		
		$database = "if16_Tanelmaas_1";
		$mysqli = new mysqli($GLOBALS["serverHost"], $GLOBALS["serverUsername"], $GLOBALS["serverPassword"], $database);
		
		$stmt = $mysqli->prepare("
			SELECT interest
			FROM interests
			JOIN user_interest
			ON interests.id=user_interest.interest_id
			WHERE user_interest.user_id=?
		");
		echo $mysqli->error;
		
		$stmt->bing_param("i", $_SESSION["userId"]);
		
		$stmt->bind_result($interest);
		$stmt->execute();
		
		
		//tekitan massiivi
		$result = array();
		
		// tee seda seni, kuni on rida andmeid
		// mis vastab select lausele
		while ($stmt->fetch()) {
			
			//tekitan objekti
			$i = new StdClass();
			
			$i->interest = $interest;
		
			array_push($result, $i);
		}
		
		$stmt->close();
		$mysqli->close();
		
		return $result;
	}
	
	
	
	
	
	/*function sum($x, $y) {
		
		return $x + $y;
		
	}
	
	
	function hello($firsname, $lastname) {
		
		return "Tere tulemast ".$firsname." ".$lastname."!";
		
	}
	
	echo sum(5123123,123123123);
	echo "<br>";
	echo hello("Tanel", "Maasalu");
	echo "<br>";
	echo hello("Juku", "Juurikas");
	*/

?>