<?php

echo "Thank for your order from Halal!"; 

echo "<br><br>";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
	
	if(isset ($_POST['Food'])) {
		
		
	  echo "Here are the food you have selected for Halal!";
	  
	  echo "<br>"; 
      
	  foreach ($_POST['Food'] as $Food) {
		  
		echo $Food . "<br>";
		
	  } 
	  
	} else{
		  
		  
		  echo "You have choosen NOTHING!";
	  }
	
} 

?> 


<a href="Menu.html"> Go to back the Menu</a>