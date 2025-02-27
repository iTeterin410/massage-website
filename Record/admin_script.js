document.addEventListener('DOMContentLoaded', function () {
        const appointmentsTableBody = document.getElementById('appointments-table').querySelector('tbody');

        fetch('booking_api.php?action=getAppointments')
            .then(response => response.json())
            .then(appointments => {
                appointments.forEach(appointment => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${appointment.id}</td>
                       <td>${appointment.first_name}</td>
                       <td>${appointment.last_name}</td>
                       <td>${appointment.phone_number}</td>
                       <td>${appointment.email}</td>
                        <td>${appointment.employee_first_name} ${appointment.employee_last_name}</td>
                        <td>${appointment.service_name}</td>
                        <td>${appointment.appointment_date}</td>
                        <td>${appointment.start_time} - ${appointment.end_time}</td>
                         <td>${appointment.status}</td>
                         <td>${appointment.notes}</td>

                    `;
                    appointmentsTableBody.appendChild(row);
                });
            })
          .catch(error => console.error('Error fetching appointments:', error));

    });