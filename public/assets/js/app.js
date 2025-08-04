document.addEventListener("DOMContentLoaded", () => {
  fetch("../../app/controllers/getPQRS.php")
    .then(response => response.json())
    .then(data => {
      const tabla = document.getElementById("tabla-pqrs");

      if (data.error) {
        tabla.innerHTML = `<tr><td colspan="9">${data.error}</td></tr>`;
        return;
      }

      data.forEach(pqrs => {
        const tr = document.createElement("tr");

        tr.innerHTML = `
          <td>${pqrs.id}</td>
          <td>${formatFecha(pqrs.fecha)}</td>
          <td>${pqrs.urgencia}</td>
          <td>${pqrs.categoria}</td>
          <td>${pqrs.tipo_pqrs}</td>
          <td>${pqrs.solicitante}</td>
          <td>${pqrs.empleado}</td>
          <td style="color: ${pqrs.estado === 'Solucionado' ? 'red' : 'orange'};">${pqrs.estado}</td>
          <td>${formatFecha(pqrs.fecha_cierre)}</td>
        `;

        tabla.appendChild(tr);
      });
    })
    .catch(err => {
      console.error(err);
      document.getElementById("tabla-pqrs").innerHTML = `<tr><td colspan="9">Error al cargar los datos</td></tr>`;
    });
});

function formatFecha(fechaSQL) {
  if (!fechaSQL) return '';
  const fecha = new Date(fechaSQL);
  return fecha.toISOString().split('T')[0].split('-').reverse().join('/');
}
// app.js - Script to fetch and display PQRS data

// This script fetches PQRS data from the server and populates the HTML table
// It also formats the date for better readability  
