<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Huella de Carbono Personal</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #e8f5e9;
      color: #2e7d32;
      text-align: center;
      padding: 50px;
    }

    button {
      padding: 10px 20px;
      font-size: 18px;
      background-color: #66bb6a;
      color: white;
      border: none;
      border-radius: 10px;
      cursor: pointer;
    }

    button:hover {
      background-color: #388e3c;
    }

    #info {
      display: none;
      margin-top: 30px;
      padding: 20px;
      background-color: #c8e6c9;
      border-radius: 10px;
      max-width: 800px;
      margin-left: auto;
      margin-right: auto;
      text-align: left;
    }

    h2 {
      color: #1b5e20;
    }

    ul {
      text-align: left;
      margin-left: 20px;
    }

    a {
      color: #1b5e20;
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <h1>Huella de Carbono Personal</h1>
  <button onclick="mostrarInfo()">¿Qué es y cómo reducirla?</button>

  <div id="info">
    <h2>¿Qué es la huella de carbono personal?</h2>
    <p>
      La huella de carbono personal representa la cantidad total de gases de efecto invernadero (GEI) que emites directa o indirectamente a través de tus actividades diarias, como el transporte, el consumo de energía, la alimentación y el uso de productos y servicios.
    </p>

    <h2>Datos clave:</h2>
    <ul>
      <li>El promedio mundial de emisiones per cápita es de aproximadamente 4.8 toneladas de CO₂ al año, mientras que en Estados Unidos supera las 16 toneladas. :contentReference[oaicite:0]{index=0}</li>
      <li>Las emisiones personales incluyen no solo el CO₂, sino también otros GEI como el metano (CH₄) y el óxido nitroso (N₂O), expresados en toneladas de CO₂ equivalente (tCO₂e). :contentReference[oaicite:1]{index=1}</li>
      <li>Reducir nuestra huella es esencial para combatir el cambio climático y sus efectos adversos, como el aumento de temperaturas, sequías e inundaciones. :contentReference[oaicite:2]{index=2}</li>
    </ul>

    <h2>Principales fuentes de emisiones personales:</h2>
    <ul>
      <li><strong>Transporte:</strong> Uso de vehículos particulares, vuelos frecuentes y transporte marítimo.</li>
      <li><strong>Energía en el hogar:</strong> Consumo de electricidad, calefacción y uso de electrodomésticos.</li>
      <li><strong>Alimentación:</strong> Consumo de productos de origen animal, especialmente carne roja, y alimentos procesados. :contentReference[oaicite:3]{index=3}</li>
      <li><strong>Consumo y residuos:</strong> Compra de productos no sostenibles, uso de plásticos de un solo uso y generación de desechos sin reciclaje.</li>
    </ul>

    <h2>Consejos para reducir tu huella de carbono:</h2>
    <ul>
      <li>Opta por medios de transporte sostenibles: bicicleta, caminar o transporte público.</li>
      <li>Mejora la eficiencia energética en tu hogar: utiliza bombillas LED, desconecta dispositivos no utilizados y considera fuentes de energía renovable.</li>
      <li>Adopta una dieta más sostenible: reduce el consumo de carne y elige productos locales y de temporada.</li>
      <li>Minimiza el uso de plásticos y recicla adecuadamente.</li>
      <li>Calcula tu huella de carbono personal utilizando herramientas como la <a href="https://footprint.conservation.org/es-la" target="_blank">calculadora de Conservation International</a>.</li>
    </ul>

    <p>
      Cada pequeña acción cuenta. Al tomar decisiones más conscientes, contribuyes significativamente a la protección del medio ambiente y al bienestar de las futuras generaciones.
    </p>
  </div>

  <script>
    function mostrarInfo() {
      var info = document.getElementById("info");
      if (info.style.display === "none" || info.style.display === "") {
        info.style.display = "block";
      } else {
        info.style.display = "none";
      }
    }
  </script>

</body>
</html>
