<?php
	//https://api.openweathermap.org/data/2.5/weather?q=Puławy,pl&lang=pl&units=metric&appid=5bf5d70f6b391fe9cb0c6febc330aa36
	$apiKey = "5bf5d70f6b391fe9cb0c6febc330aa36";
	$cityId = "Krosno";
	if($_GET['city']==null){
		//header("location: getcity.php");
	}
	$city=$_GET['city'];
	$language = "pl";
	$img = 
	$ApiUrl = "http://api.openweathermap.org/data/2.5/weather?q=" . $city . "&lang=". $language ."&units=metric&APPID=" . $apiKey;
	
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_URL, $ApiUrl);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$response = curl_exec($ch);

	curl_close($ch);
	$data = json_decode($response);

	if($data->cod == 404){
		header("location: error.php");
	}
	else
	{
		//#echo ucwords($data->weather[0]->description); //pierwsza duża litera
		//#$data->weather[0]->icon;
		//#echo $data->main->temp_max;
		//#echo $data->main->temp_min;
		//#echo $data->main->temp;
		//echo $data->main->feels_like; odczuwalna temp
		//echo $data->main->pressure; cisnienie
		//echo $data->main->humidity; wilgotnosc
		//echo $data->wind->speed; wiatr predkosc
		//echo $data->wind->deg;   wiatr kierunek
		//echo $data->clouds->all; zachmurzenie
		//echo $data->visibility; widocznosc
		//#echo $data->sys->country;
		//#echo $data->sys->sunrise;
		//#echo $data->sys->sunset;
		//#echo $data->name;
		//$currentTime = time();

		//wybór ikony zależny od dnia/nocy
		if(str_ends_with($data->weather[0]->icon , "n")){
			$img = "01n";
		}else
		{
			$img = "01d";
		}
	}

?>

<!doctype html>
<html lang="pl">
		<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<meta name="description" content="Sprawdź pogodę w dowolnej miejscowości na świecie.">
		<meta name="keywords" content="pogoda, temperatura">

		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
		<link rel="stylesheet" href="style/style.css">

		<title>Pogoda</title>
	</head>
	<body>
		<div id="content">
			<div id="timezone">
				<img src=<?php echo "img/".$img.".png";?> alt="pogoda">
				<div id="ddd">
					<input type="button" value="day" onClick="day();">
					<input type="button" value="night" onClick="night();">
				</div>
				<div id="sunrise"> 00:00:00 </div>
				<div id="sunset">  00:00:00 </div>
			</div>
			<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<form method="get">
						<input type="text" placeholder="Podaj miejscowość" name="city" id="name_city">
						<button type="submit"><i class="fas fa-search"></i></button>
					</form>
				</div>
			</div>
			<div class="row justify-content-center">
				<div id="country" class="col-lg-12">
					<?php echo $data->name; ?>,
					<?php echo $data->sys->country; ?>
					<?php echo "<img src='https://www.countryflags.io/".$data->sys->country."/flat/64.png' alt='country'>"; ?>
				</div>
			</div>
			<div class="row justify-content-center weather">
				<div id="temp" class="col-lg-12">
					<div id="temp_currently"><?php echo $data->main->temp; ?> °C</div>
					<div id="temp_max_min">Odczuwalna: <?php echo $data->main->feels_like; ?> °C</div>
					<div id="description"><?php echo ucwords($data->weather[0]->description); ?></div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-3">
					<div id="visibility">Widoczność: <?php echo $data->visibility; ?> m</div>
					<div id="clouds">Zachmurzenie: <?php echo $data->clouds->all; ?> %</div>
				</div>
				<div class="col-lg-3">
					<div id="wind_speed">Prędkość wiatru: <?php echo $data->wind->speed; ?> m/s</div>
					<div id="wind_deg"><div id="deg"></div><i id="arrow" class="fas fa-arrow-up"></i></div>
				</div>
				<div class="col-lg-3">
					<div id="pressure">Ciśnienie: <?php echo $data->main->pressure; ?>hPa</div>
					<div id="humidity">Wilgotność: <?php echo $data->main->humidity; ?> %</div>
				</div>
				<div class="col-lg-3">
				<div id="temp_min">Min: <?php echo ceil($data->main->temp_min); ?> °C</div>
				<div id="temp_max">Max: <?php echo ceil($data->main->temp_max); ?> °C</div>
				</div>
			</div>
			</div>
		</div>
		<div id="terrain">
			<div class="hill-right"></div>
    		<div class="hill-left"></div>
		</div>
		
	</body>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
	<script>
		var background = document.getElementById("content"); //background weather
		var terrain = document.getElementById("terrain"); //background weather
		var wind =  document.getElementById("deg"); //wind direction
		var arrow =  document.getElementById("arrow"); //wind arrow	
		var time = document.querySelector("#timezone img");	
		var sunrise = document.getElementById("sunrise");
		var sunset = document.getElementById("sunset");

		var times = <?php echo $data->sys->sunrise?>; //czas wchodu słońca
		var timek = <?php echo $data->sys->sunset?>; //czas zachodu słońca

		function deg(degg){ //ustawienie kierunku wiatru
			var direction = "x";

			if(degg>348.75 || degg<=11.25){
				direction = "N";
			}else if(degg>11.25 && degg<=33.75){
				direction = "NNE";
			}else if(degg>33.75 && degg<=56.25){
				direction = "NE";
			}else if(degg>56.25 && degg<=78.75){
				direction = "ENE";
			}else if(degg>78.75 && degg<=101.25){
				direction = "E";
			}else if(degg>101.25 && degg<=123.75){
				direction = "ESE";
			}else if(degg>123.75 && degg<=146.25){
				direction = "SE";
			}else if(degg>146.25 && degg<=168.75){
				direction = "SSE";
			}else if(degg>168.75 && degg<=191.25){
				direction = "S";
			}else if(degg>191.25 && degg<=213.75){
				direction = "SSW";
			}else if(degg>213.75 && degg<=236.25){
				direction = "SW";
			}else if(degg>236.25 && degg<=258.75){
				direction = "WSW";
			}else if(degg>258.75 && degg<=281.25){
				direction = "W";
			}else if(degg>281.25 && degg<=303.75){
				direction = "WNW";
			}else if(degg>303.75 && degg<=326.25){
				direction = "NW";
			}else if(degg>326.25 && degg<= 348.75){
				direction = "NNW";
			}
			arrow.style.transform = "rotate("+degg+"deg)";
			return "Kierunek wiatru: "+direction;
		}
		wind.innerHTML = deg(<?php echo $data->wind->deg; ?>);

			
			//formatowanie czasu wschodu słońca
			var date = new Date(times * 1000);
			var hours = date.getHours();
			var minutes = "0" + date.getMinutes();
			var seconds = "0" + date.getSeconds();
			var formattedTime = hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2) + "<br> wschód słońca";
			sunrise.innerHTML = formattedTime;
			var day_start = date.getHours()*3600 + date.getMinutes()*60 + date.getSeconds(); //czas wschodu słońca w sekundach

			//formatowanie czasu zachodu słońca
			var date2 = new Date(timek * 1000);
			var hours = date2.getHours();
			var minutes = "0" + date2.getMinutes();
			var seconds = "0" + date2.getSeconds();
			var formattedTime2 = hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2) + "<br> zachód słońca";
			sunset.innerHTML = formattedTime2;
			var day_end = date2.getHours()*3600 + date2.getMinutes()*60 + date2.getSeconds(); //czas zachodu słońca w sekundach

			var seconds_day = (date2.getTime() - date.getTime()) / 1000;  //liczba sekund trwania dnia
			var seconds_night = 86400 - seconds_day; //liczba sekund trwania nocy

			var now = new Date(); //aktualny czas
			var now_seconds = now.getHours() * 3600 + now.getMinutes() *60 + now.getSeconds(); //aktualny czas w sekundach
			//alert("day_start = " + day_start + " day_end="+day_end+" seconds_day =" + seconds_day + "seconds_night ="+seconds_night +"now = " + now);
			//alert("teraz: " + now.getHours()+"h "+ now.getMinutes()+"m "+ now.getSeconds()+"s");
			//alert(now.getHours() +">"+date2.getHours()+"   "+now.getHours()+"<"+date.getHours());
			
		//sprawdzanie czy noc/dzień
		//alert("day_start: " +day_start+"\nday_end: " + day_end + "\nnow_seconds" + now_seconds);
		if(day_start<now_seconds && day_end <now_seconds){
			day();
		}
		else
		{
			night();
		}
		
		//ustawienia dnia
		function day(){
			background.setAttribute("id","content");
			terrain.setAttribute("id","terrain");

			sunrise.innerHTML = formattedTime;
			sunset.innerHTML = formattedTime2;

			//zmina pozycji słońca 
			var minelo = now_seconds - day_start;
			var podzielony = seconds_day/94;

			for(var i=1; i<=94; i++){
				if((day_start + minelo) > i*podzielony){
					if(i>48){
						topp = (3*i)-144;
					}
					else{
						topp = 150-(3*i);
					}

					left = i;
					time.style.top = topp+"%";
					time.style.left = left+"%";

					break;
				}
				
			}
		}
		
		//ustawienia nocy
		function night(){
			background.setAttribute("id","content2");
			terrain.setAttribute("id","terrain2");
			sunset.innerHTML = formattedTime;
			sunrise.innerHTML = formattedTime2;

			//zmina pozycji księżyca 
			if(now_seconds<day_start){
				//czas od pólnocy do wschodu
				var podzielony = seconds_night/940;
				//alert("OBL1: " + ((84600 - day_end) + now_seconds) + " OBL2: " + ((84600 - day_end) + day_start) + "PODZIELONY: " + podzielony);
				for(var i=1; i<=940; i++){
					if(((84600 - day_end) + now_seconds) < podzielony*i){//if(84600+now_seconds < (i*podzielony)+day_end)
						if(i>480){
							topp = (0.3*i)-144;
						}
						else{
							topp = 150-(0.3*i);
						}

						left = i/10;
						time.style.top = topp+"%";
						time.style.left = left+"%";

						break;
					}
					
				}
			}
			else
			{
				//czas od zmierzchu do północy
				var podzielony = seconds_night/940;

				for(var i=1; i<=940; i++){
					if(now_seconds < (i*podzielony)+day_end){
						if(i>480){
							topp = (3*i)-144;
						}
						else{
							topp = 150-(3*i);
						}

						left = i/10;
						time.style.top = topp+"%";
						time.style.left = left+"%";

						break;
					}
					
				}
			}
		}

	</script>
</html>