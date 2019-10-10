<?php


  //
  //  enregistre la consommation de la veille en Wh
  //
  function computeLastDayConso () {
   

    $today = strtotime('today 00:00:00');
    $yesterday = strtotime("-1 day 00:00:00");

      $link = mysqli_connect('maison.lithium', 'pi', '66446644', 'teleinfo_v2')
    or die('Impossible de se connecter : ' . mysqli_error());
    
      
   // $query = "SELECT MAX(timestamp) AS timestamp, MAX(base) AS total_base, ((MAX(base) - MIN(base)) / 1000) AS daily_base FROM puissance 
    //                       WHERE timestamp >= $yesterday AND timestamp < $today AND base!='' GROUP BY DATE_FORMAT(timestamp, '%d-%m-%Y');";
   $query = "SELECT MAX(timestamp) AS timestamp, MAX(base) AS total_base, ((MAX(base) - MIN(base)) / 1000) AS daily_base FROM puissance 
                           WHERE timestamp >= $yesterday AND timestamp < $today AND base!='';";
  
          $result = mysqli_query($link,$query) or die('Échec de la requête : ' . mysqli_error($link));

 
    $previousDay = mysqli_fetch_array($result, MYSQLI_ASSOC);

    $query ='CREATE TABLE IF NOT EXISTS conso (timestamp INTEGER, total_base INTEGER, daily_base REAL);'; // cree la table conso si elle n'existe pas
    $result = mysqli_query($link,$query) or die('Échec de la requête : ' . mysqli_error($link));


      $query ="INSERT INTO conso (timestamp, total_base, daily_base) VALUES 
                (".$previousDay['timestamp'].", ".$previousDay['total_base'].", ".$previousDay['daily_base'].");";
        $result = mysqli_query($link,$query) or die('Échec de la requête : ' . mysqli_error($link));
        mysqli_close($link);
  }

  //
  //  recupere les donnees de puissance des $nb_days derniers jours et les met en forme pour les afficher sur le graphique
  //
  function getInstantConsumption ($nb_days) {

    $now  = time();
    $past = strtotime("-$nb_days day", $now);

      $link = mysqli_connect('maison.lithium', 'pi', '66446644', 'teleinfo_v2')
    or die('Impossible de se connecter : ' . mysqli_error());
    
      
    $query = "SELECT * FROM puissance WHERE timestamp > $past ORDER BY timestamp ASC;";
    $result = mysqli_query($link,$query) or die('Échec de la requête : ' . mysqli_error($link));
    $data = array();

    
    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
      $year   = date("Y", $row['timestamp']);
      $month = date("n", $row['timestamp'])-1;
      $day    = date("j", $row['timestamp']);
      $hour   = date("G", $row['timestamp']);
      $minute = date("i", $row['timestamp']);
      $second = date("s", $row['timestamp']);
      $basehp_indicator = 'color: #375D81';
      $data[] = "[{v:new Date($year, $month, $day, $hour, $minute, $second), f:'".date("j", $row['timestamp'])." ".date("M", $row['timestamp'])." ".date("H\hi", $row['timestamp'])."'}, 
                  {v:".$row['va'].", f:'".$row['va']." V.A'}, '".$basehp_indicator."', {v:".$row['watt'].", f:'".$row['watt']." W'}]";
    }
  
    return implode(', ', $data);
   mysqli_close($link);
  }

    function getInstantConsumptionLight ($nb_days) {

    $now  = time();
    $past = strtotime("-$nb_days day", $now);

        $link = mysqli_connect('maison.lithium', 'pi', '66446644', 'teleinfo_v2')
    or die('Impossible de se connecter : ' . mysqli_error());
    
      
    $query="SELECT * FROM puissance WHERE timestamp > $past ORDER BY timestamp ASC;";
    $result = mysqli_query($link,$query) or die('Échec de la requête : ' . mysqli_error($link));
  

    $data = array();

    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
      $year   = date("Y", $row['timestamp']);
      $month = date("n", $row['timestamp'])-1;
      $day    = date("j", $row['timestamp']);
      $hour   = date("G", $row['timestamp']);
      $minute = date("i", $row['timestamp']);
      $second = date("s", $row['timestamp']);
      $basehp_indicator = 'color: #375D81';
      $data[] = "[{v:new Date($year, $month, $day, $hour, $minute, $second), f:'".date("j", $row['timestamp'])." ".date("M", $row['timestamp'])." ".date("H\hi", $row['timestamp'])."'}, 
                  '".$basehp_indicator."', {v:".$row['watt'].", f:'".$row['watt']." W'}]";
    }

    return implode(', ', $data);
    mysqli_close($link);
  }
  
  
  //
  //  recupere les donnees de consommation des $nb_days derniers jours et les met en forme pour les afficher sur le graphique
  //
  function getDailyData ($nb_days) {
    
    $now  = time();
    $past = strtotime("-$nb_days day", $now);

      $link = mysqli_connect('maison.lithium', 'pi', '66446644', 'teleinfo_v2')
    or die('Impossible de se connecter : ' . mysqli_error());
    
      
    $query="SELECT timestamp, daily_base FROM conso WHERE timestamp > $past ORDER BY timestamp ASC;";
    $result = mysqli_query($link,$query) or die('Échec de la requête : ' . mysqli_error($link));
  

    $data = array();

    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
      $year   = date("Y", $row['timestamp']);
      $month = date("n", $row['timestamp'])-1;
      $day    = date("j", $row['timestamp']);
      $data[] = "[new Date($year, $month, $day), {v:".$row['daily_base'].", f:'".$row['daily_base']." kWh'}]";
    }

    return implode(', ', $data);
    mysqli_close($link);
  }

   //
  //  recupere les donnees de consommation des $nb_days derniers jours et les met en forme pour les afficher sur le graphique
  //
  function getDailySunData ($nb_days) {
    
    $now  = time();
    $past = strtotime("-$nb_days day", $now);

      $link = mysqli_connect('maison.lithium', 'pi', '66446644', 'solaire_v1')
    or die('Impossible de se connecter : ' . mysqli_error());
    
      
    $query="SELECT timestamp, watt FROM conso WHERE timestamp > $past ORDER BY timestamp ASC;";
    $result = mysqli_query($link,$query) or die('Échec de la requête : ' . mysqli_error($link));
  

    $data = array();

    while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
      $year   = date("Y", $row['timestamp']);
      $month = date("n", $row['timestamp'])-1;
      $day    = date("j", $row['timestamp']);
      $data[] = "[new Date($year, $month, $day), {v:".$row['watt'].", f:'".$row['watt']." kWh'}]";
    }

    return implode(', ', $data);
    mysqli_close($link);
  }

  
?>
