document.addEventListener("DOMContentLoaded", function(){

fetch("backend/obtener_datos.php")
.then(res => res.json())
.then(data => {

    console.log("DATA:", data);

    const datosTemp = data.temperatura || [];
    const datosHum = data.humedad || [];
    const datosLago = data.lago || [];

    // ======================
    // 🌡️ TEMPERATURA
    // ======================
    const canvasTemp = document.getElementById('graficoTemp');

    if (canvasTemp && datosTemp.length > 0) {
        new Chart(canvasTemp, {
            type: 'line',
            data: {
                labels: datosTemp.map(d => d.fecha),
                datasets: [{
                    label: 'Temperatura (°C)',
                    data: datosTemp.map(d => d.valor),
                    borderColor: 'red',
                    backgroundColor: 'rgba(255,0,0,0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false
            }
        });
    }

    // ======================
    // 💧 HUMEDAD
    // ======================
    const canvasHum = document.getElementById('graficoHum');

    if (canvasHum && datosHum.length > 0) {
        new Chart(canvasHum, {
            type: 'line',
            data: {
                labels: datosHum.map(d => d.fecha),
                datasets: [{
                    label: 'Humedad (%)',
                    data: datosHum.map(d => d.valor),
                    borderColor: 'blue',
                    backgroundColor: 'rgba(0,0,255,0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false
            }
        });
    }

    // ======================
    // 🌊 LAGO
    // ======================
    const canvasLago = document.getElementById("graficaLago");

    if (canvasLago && datosLago.length > 0) {
        new Chart(canvasLago, {
            type: "line",
            data: {
                labels: datosLago.map(d => d.fecha),
                datasets: [{
                    label: "Temperatura Lago (°C)",
                    data: datosLago.map(d => d.valor),
                    borderColor: "green",
                    backgroundColor: "rgba(0,255,0,0.2)",
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // ======================
    // 📊 GRAFICA GENERAL (SOLO PUNTOS)
    // ======================
    const ctx = document.getElementById("graficoSemanal");

    if (ctx) {

        const fechas = [...new Set([
            ...datosTemp.map(d => d.fecha),
            ...datosHum.map(d => d.fecha),
            ...datosLago.map(d => d.fecha)
        ])].sort();

        function mapDatos(fechas, datos) {
            return fechas.map(f => {
                const item = datos.find(d => d.fecha === f);
                return item ? item.valor : null;
            });
        }

        const tempValores = mapDatos(fechas, datosTemp);
        const humValores = mapDatos(fechas, datosHum);
        const lagoValores = mapDatos(fechas, datosLago);

        if (window.chartGeneral) {
            window.chartGeneral.destroy();
        }

        window.chartGeneral = new Chart(ctx, {
            type: "line",
            data: {
                labels: fechas,
                datasets: [
                    {
                        label: "Temp Ambiente",
                        data: tempValores,
                        borderColor: "red",
                        backgroundColor: "red",
                        showLine: false,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    },
                    {
                        label: "Humedad",
                        data: humValores,
                        borderColor: "blue",
                        backgroundColor: "blue",
                        showLine: false,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    },
                    {
                        label: "Temp Lago",
                        data: lagoValores,
                        borderColor: "green",
                        backgroundColor: "green",
                        showLine: false,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                spanGaps: true
	    


            }

        });
    }

})
.catch(error => console.error("Error:", error));

});
