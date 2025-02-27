document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('booking-form');
    const bookingResult = document.getElementById('booking-result');
    const employeeSelect = document.getElementById('employee_id');
    const serviceSelect = document.getElementById('service_id');
    const scheduleSelect = document.getElementById('schedule_id');
    const appointmentDateInput = document.getElementById('appointment_date');


   // Загружаем список массажистов
    fetch('booking_api.php?action=getEmployees')
        .then(response => response.json())
        .then(data => {
          data.forEach(employee =>{
                const option = document.createElement('option');
                 option.value = employee.id;
                 option.textContent = employee.first_name + " " + employee.last_name;
                 employeeSelect.appendChild(option);
            })
        }).catch(error => console.error('Error fetching employees:', error));
     // Загружаем список услуг
    fetch('booking_api.php?action=getServices')
        .then(response => response.json())
        .then(data => {
          data.forEach(service =>{
                const option = document.createElement('option');
                 option.value = service.id;
                 option.textContent = service.name;
                 serviceSelect.appendChild(option);
            })
        }).catch(error => console.error('Error fetching services:', error));
    // Обновляем список времени при выборе даты
    appointmentDateInput.addEventListener('change', function() {
        const selectedDate = appointmentDateInput.value;
        const selectedEmployeeId = employeeSelect.value;
         scheduleSelect.innerHTML = '';
        if(selectedDate && selectedEmployeeId){
           fetch(`booking_api.php?action=getSchedule&date=${selectedDate}&employee_id=${selectedEmployeeId}`)
              .then(response => response.json())
              .then(data =>{
                  data.forEach(schedule =>{
                    const option = document.createElement('option');
                    option.value = schedule.id;
                    option.textContent = `${schedule.start_time} - ${schedule.end_time}`;
                    scheduleSelect.appendChild(option);
                  });
                 if(data.length == 0){
                    scheduleSelect.innerHTML = '<option>Нет доступного времени</option>'
                 }

              })
              .catch(error => console.error('Error fetching schedule:', error));
        }

    });
    employeeSelect.addEventListener('change', function() {
        appointmentDateInput.dispatchEvent(new Event('change'));
    });


    bookingForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Предотвращаем отправку формы по умолчанию

        const formData = new FormData(bookingForm);

        fetch('booking_api.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                bookingResult.innerHTML = '<p class="success">' + data.success + '</p>';
              bookingForm.reset();
              scheduleSelect.innerHTML = '';
             } else if(data.error){
               bookingResult.innerHTML = '<p class="error">' + data.error + '</p>';
           }
            console.log(data);
        })
        .catch(error => {
            console.error('Error:', error);
             bookingResult.innerHTML = '<p class="error">An error occurred while booking.</p>';
        });
    });
});