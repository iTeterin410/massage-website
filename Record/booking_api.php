<?php
// Подключение к БД
$servername = "Mysql@localhost:3306";
$username = "MySQL92";
$password = "123krot123";
$dbname = "massageClub01";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8"); // Установка кодировки

header('Content-Type: application/json'); // Указываем, что будет JSON

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];

    switch($action){
       case 'getEmployees':
            $sql = "SELECT id, first_name, last_name FROM employees";
            $result = $conn->query($sql);
            $employees = [];
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                   $employees[] = $row;
               }
            }
            echo json_encode($employees);
           break;
       case 'getServices':
            $sql = "SELECT id, name FROM services";
            $result = $conn->query($sql);
            $services = [];
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                   $services[] = $row;
               }
            }
            echo json_encode($services);
            break;
       case 'getSchedule':
           if (isset($_GET['date']) && isset($_GET['employee_id'])) {
                $date = $_GET['date'];
                $employeeId = $_GET['employee_id'];

                $sql = "SELECT id, start_time, end_time FROM schedule WHERE work_date = '$date' AND employee_id = $employeeId";
                $result = $conn->query($sql);
                $schedule = [];

                if ($result->num_rows > 0) {
                     while($row = $result->fetch_assoc()){
                         $schedule[] = $row;
                     }
                }
                echo json_encode($schedule);
            } else {
                echo json_encode(['error' => 'Date and employee ID are required.']);
             }
            break;

       case 'getAppointments':
             $sql = "SELECT a.id, c.first_name, c.last_name, c.phone_number, c.email, e.first_name as employee_first_name, e.last_name as employee_last_name, s.name as service_name, a.appointment_date, sch.start_time, sch.end_time, a.status, a.notes
                    FROM appointments a
                    JOIN customers c ON a.customer_id = c.id
                    JOIN employees e ON a.employee_id = e.id
                    JOIN services s ON a.service_id = s.id
                    JOIN schedule sch ON a.schedule_id = sch.id";
                $result = $conn->query($sql);
                $appointments = [];
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $appointments[] = $row;
                    }
                }
               echo json_encode($appointments);
               break;
           default:
            echo json_encode(['error' => 'Invalid action.']);
    }
}
 elseif($_SERVER['REQUEST_METHOD'] === 'POST'){
     $first_name = $_POST['first_name'];
     $last_name = $_POST['last_name'];
     $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
     $employee_id = $_POST['employee_id'];
    $service_id = $_POST['service_id'];
    $appointment_date = $_POST['appointment_date'];
    $schedule_id = $_POST['schedule_id'];
    $notes = $_POST['notes'];

    // Начинаем с проверки наличия клиента по телефону или почте.
    $sql_check_customer = "SELECT id FROM customers WHERE phone_number = '$phone_number' OR email = '$email'";
    $result_check_customer = $conn->query($sql_check_customer);

    if ($result_check_customer->num_rows > 0) {
        // Клиент существует, используем его ID.
        $row_customer = $result_check_customer->fetch_assoc();
        $customer_id = $row_customer['id'];
    } else {
        // Клиента нет, добавляем нового.
        $sql_add_customer = "INSERT INTO customers (first_name, last_name, phone_number, email) VALUES ('$first_name', '$last_name', '$phone_number', '$email')";
        if ($conn->query($sql_add_customer) === TRUE) {
            $customer_id = $conn->insert_id; // Получаем ID нового клиента
        } else {
           echo json_encode(['error' => 'Error adding customer: ' . $conn->error]);
          $conn->close();
          exit();
        }

    }
    // Добавляем запись в appointments.
    $sql_add_appointment = "INSERT INTO appointments (customer_id, employee_id, service_id, appointment_date, schedule_id, notes)
    VALUES ('$customer_id', '$employee_id', '$service_id', '$appointment_date', '$schedule_id', '$notes')";

    if ($conn->query($sql_add_appointment) === TRUE) {
        echo json_encode(['success' => 'Appointment booked successfully!']);

    } else {
       echo json_encode(['error' => 'Error booking appointment: ' . $conn->error]);
    }

} else{
    echo json_encode(['error' => 'Invalid request.']);
}
$conn->close();
?>