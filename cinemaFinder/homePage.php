
<!DOCTYPE html>
<head>
<title>Cinema Finder</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta charset="utf-8">
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />

	<!--Bootstrap script links-->
  	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  	<script src="bootstrap/js/bootstrap.min.js"></script>
    
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    
    <link rel="stylesheet" href="css/styleSheet.css"/>
</head>

<body>
<div  class="container">
	<div class="row">
		<div id="search" class="col-sm-2">
			<img src="img/cf_logo.png" id="logo" class="center-block"><div id="clear"></div>
				<form action="" method="post" >
					<input  type="text" name="location" placeholder="Enter a city..." size="12" />
					<input type="submit" value="submit" />
				</form>
    
		</div><!--col-->
	</div><!--row-->
</div><!--container-->

<div id="results" class="container">
	<div class="row">
		<div class="col-sm-10">
	
<?php 
		function curl_get_contents($url)
		{
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		}
		
		//if location is been filled in then post the data
		if (isset($_POST['location'])) 
		{	
        	$url = 'http://api.cinelist.co.uk/search/cinemas/location/'.$_POST["location"];
        	$json = json_decode(curl_get_contents($url));

			foreach($json->cinemas as $cinema)
			{
				echo "<div id='data'>";
				echo "<h2>".$cinema->name."<small> ".$cinema->distance." miles</small></h2>";
				echo "<br/>";
			
				for($j=0;$j<count($cinema->id);$j++)
				{
					$id = $cinema->id;
					$request = 'http://api.cinelist.co.uk/get/times/cinema/'.$id;
					$json2 = json_decode(curl_get_contents($request));
		
					//each listing
					echo "<table id='table_data' style='float:left;'>";
					foreach($json2->listings as $listing)
					{
						//outside the loop to only display once;
						echo "<tr>";
							echo "<td>";
								echo $listing->title. ": ";
							echo "</td>";
						echo "</tr>";
						echo "<td id='times'>";
					
							//set up the loop for x amount of times
							for($i=0;$i<count($listing->times);$i++)
							{
								//display the times after the title, with spaces after for easy reading.
								echo $listing->times[$i]."  ";
							}	
						echo "</td>";	
					}
					echo "</table>";
				}
			echo "<iframe id='map' src='https://www.google.co.uk/maps/embed/v1/place?key=AIzaSyCEED7A6LBwHFfNjoIJ1zFduaU0tyRv4Dw&q=".$cinema->name."' width='300' 					height='225' allowfullscreen></iframe>";
			echo "</div>";
			echo "<div id='clear'></div>";
			}
		}
		?>      
        
		</div><!--col-->
	</div><!--row-->
</div><!--container-->
</body>
</html>