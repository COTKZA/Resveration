<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: /WEB/login_page.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Data</title>
    <style>
* {
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    background-color: #f9f9f9;
}

h1 {
    text-align: center;
    color: #333;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden; /* Ensures that rounded borders are effective */
}

th, td {
    padding: 12px;
    text-align: left;
    transition: background-color 0.3s ease;
}

th {
    background-color: #4CAF50;
    color: white;
    text-align: center;
}

td {
    background-color: #ffffff;
    text-align: center;
}

td:hover {
    background-color: #f1f1f1;
}

.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:checked + .slider:before {
    transform: translateX(26px);
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.7);
    padding-top: 60px;
}

.modal-content {
    background-color: #ffffff;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 70%;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #e74c3c; /* Color change on hover */
    text-decoration: none;
    cursor: pointer;
}

/* Button styles */
button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 15px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 5px;
    transition: background-color 0.3s, transform 0.2s;
}

button:hover {
    background-color: #45a049;
    transform: translateY(-2px);
}

button:active {
    transform: translateY(0);
}

input[type="date"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            width: 200px; /* Adjust width as needed */
            margin-right: 10px; /* Space between input and button */
            transition: border-color 0.3s ease;
        }

input[type="date"]:focus {
            border-color: #4CAF50; /* Change border color on focus */
            outline: none; /* Remove default outline */
        }

#siderbar {
    .sidebar {
            position: fixed;
            left: -250px; /* เริ่มต้นซ่อน */
            top: 10%; /* ระยะห่างจากด้านบน */
            width: 250px;
            height: 80%; /* ความสูงของ Sidebar */
            max-height: 80vh; /* ความสูงสูงสุดของ Sidebar */
            background-color: #333;
            color: white;
            padding: 15px;
            transition: left 0.3s ease;
            z-index: 1000;
            overflow-y: auto; /* ทำให้มี Scroll bar ถ้าข้อมูลเยอะ */
        }
}


#sidebar.active {
            left: 0; /* แสดง Sidebar */
        }

#open-sidebar-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            margin-bottom: 20px;
            border-radius: 5px;
        }

.close-sidebar-btn {
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            padding: 5px;
        }

        /* Styles for the sidebar */
.sidebar {
            position: fixed;
            left: -250px; /* เริ่มต้นซ่อน */
            top: 22%; /* ระยะห่างจากด้านบน */
            width: 250px;
            height: 25%; /* ความสูงของ Sidebar */
            max-height: 80vh; /* ความสูงสูงสุดของ Sidebar */
            background-color: #4CAF50;
            color:white;
            padding: 15px;
            transition: left 0.3s ease;
            z-index: 1000;
            overflow-y: auto; /* ทำให้มี Scroll bar ถ้าข้อมูลเยอะ */
        }
 
#sidebar ul li a {
            color: white; /* เปลี่ยนสีลิงก์ในรายการ */
            text-decoration: none;
            font-size: 18px;    
}

#sidebar ul li a:hover {
    color: #333; /* เปลี่ยนสีลิงก์เมื่อชี้เมาส์ */
    text-decoration: none;
}

.sidebar.active {
            left: 0; /* แสดง Sidebar */
        }

.open-sidebar-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            margin-bottom: 10px;
            border-radius: 5px;
        }

.close-sidebar-btn {
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            padding: 5px;
        }
    </style>
</head>

<body>

    <h1>Reservation List</h1>
    
    <!-- ปุ่มเปิด Sidebar -->
    <button class="open-sidebar-btn" onclick="toggleSidebar()">Menu</button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <h2>Admin</h2>
        <ul>
            <li><a href="admin.php">Admin information</a></li>
            <li><a href="profile.php">ข้อมูลส่วนตัว</a></li>
            <li><a href="view_seats.php">ดูรายละเอียดของผู้ใช้งาน</a></li>
            <li><a href="busschedule.php">ตารางการเดินทาง</a></li>
            <li><a href="/WEB/logout.php">logout</a></li>
        </ul>
    </div>

    <script>
        // ฟังก์ชันเปิด/ปิด Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active'); // สลับคลาส 'active'
        }
    </script>
    
    <label for="startDate">Start Date:</label>
    <input type="date" id="startDate" required>
    <button onclick="fetchReservations()">Filter</button>

    <button id="downloadAllButton" onclick="downloadAllExcel()">Download All Excel</button>
    <button id="downloadFilteredButton" onclick="downloadFilteredExcel()">Download Filtered Excel</button>

    <table>
        <thead>
            <tr>
                <th>Travel Date</th> <!-- New column header -->
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Travel type</th>
                <th>Outbound</th>
                <th>Return</th>
                <th>Seats</th>
                <th>Contact</th>
                <th>Office</th>
                <th>Reason for travel</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="reservationTableBody">
            <!-- Reservation data will be inserted here dynamically -->
        </tbody>
    </table>

    <!-- Modal for editing reservation -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Reservation</h2>
            <form id="editForm">
                <input type="hidden" id="reservationId">
                <label for="editFirstName">First Name:</label>
                <input type="text" id="editFirstName" required><br><br>
                <label for="editLastName">Last Name:</label>
                <input type="text" id="editLastName" required><br><br>
                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" required><br><br>
                <label for="editTravelDate">Travel Date:</label>
                <input type="date" id="editTravelDate" required><br><br>
                <label for="editTravelType">Travel Type:</label>
                <input type="text" id="editTravelType" required><br><br>
                <label for="editOutboundTravel">Outbound:</label>
                <input type="text" id="editOutboundTravel"><br><br>
                <label for="editReturnTravel">Return:</label>
                <input type="text" id="editReturnTravel"><br><br>
                <label for="editSeats">Seats:</label>
                <input type="text" id="editSeats" required><br><br>
                <label for="editPhoneNumber">Contact:</label>
                <input type="text" id="editPhoneNumber" required><br><br>
                <label for="editOffice">Office:</label>
                <input type="text" id="editOffice" required><br><br>
                <label for="editTravelReason">Reason for Travel:</label>
                <input type="text" id="editTravelReason" required><br><br>
                <button type="button" onclick="updateReservation()">Update</button>
            </form>
        </div>
    </div>


    <script>
    // Fetch reservation data from the API
    const apiUrl = '/API/v1/admin/reserve_seat.php';

    function formatDate(dateString) {
        const options = {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        };
        return new Date(dateString).toLocaleDateString('th-TH', options); // Change 'th-TH' for Thai format
    }


    function fetchReservations() {
        const startDate = document.getElementById('startDate').value;

        // Validate start date selection
        if (!startDate) {
            alert("Please select a start date.");
            return;
        }

        // Build the query parameters for the exact date filter
        const query = new URLSearchParams({
            start_date: startDate
        }).toString();
        const url = `${apiUrl}?${query}`;

        // Use the fetch API to get the data from the server
        fetch(url)
            .then(response => response.json()) // Parse the response as JSON
            .then(data => {
                const tableBody = document.getElementById('reservationTableBody');
                tableBody.innerHTML = ''; // Clear existing data

                // Check if there is any data
                if (data.length > 0) {
                    // Populate the table with data
                    data.forEach(reservation => {
                        if (reservation.status !== 'ยกเลิกแล้ว') {
                            const row = document.createElement('tr');

                            row.innerHTML = `
                            <td>${reservation.travel_date ? reservation.travel_date : '-'}</td> <!-- Directly use formatted date -->
                            <td>${reservation.first_name}</td>
                            <td>${reservation.last_name}</td>
                            <td>${reservation.email}</td>
                            <td>${reservation.travel_type}</td>
                            <td>${reservation.outbound_travel ? reservation.outbound_travel : '-'}</td>
                            <td>${reservation.return_travel ? reservation.return_travel : '-'}</td>
                            <td>${reservation.seats}</td>
                            <td>${reservation.phone_number}</td>
                            <td>${reservation.office}</td>
                            <td>${reservation.travel_reason}</td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" ${reservation.status === 'ยืนยันแล้ว' ? 'checked' : ''} onchange="toggleStatus(this, '${reservation.id}')">
                                    <span class="slider"></span>
                                </label>
                            </td>
                            <td>
                               <button onclick="openEditModal('${reservation.id}', '${reservation.first_name}', '${reservation.last_name}', '${reservation.email}', '${reservation.travel_date}', '${reservation.travel_type}', '${reservation.outbound_travel}', '${reservation.return_travel}', '${reservation.seats}', '${reservation.phone_number}', '${reservation.office}', '${reservation.travel_reason}')">Edit</button>
                                <button onclick="deleteReservation('${reservation.id}')">Delete</button>
                            </td>
                        `;

                            // Append the row to the table body
                            tableBody.appendChild(row);
                        }
                    });

                    // Check if any reservations were added to the table
                    if (tableBody.children.length === 0) {
                        const row = document.createElement('tr');
                        row.innerHTML = `<td colspan="13">No reservations found.</td>`;
                        tableBody.appendChild(row);
                    }

                } else {
                    // If no data is found, show a message
                    const row = document.createElement('tr');
                    row.innerHTML = `<td colspan="13">No reservations found.</td>`;
                    tableBody.appendChild(row);
                }
            })
            .catch(error => {
                console.error('Error fetching the reservation data:', error);
            });
    }



    function toggleStatus(checkbox, reservationId) {
        const status = checkbox.checked ? 'ยืนยันแล้ว' : 'รอยืนยัน';

        // Send a POST request to update the status
        fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: reservationId,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
            })
            .catch(error => console.error('Error updating status:', error));
    }

    function downloadAllExcel() {
        // Redirect to the download URL for all data
        window.location.href = 'download_excel.php'; // Ensure this URL points to your all-data download script
    }

    function downloadFilteredExcel() {
        const startDate = document.getElementById('startDate').value;

        // Validate start date selection
        if (!startDate) {
            alert("Please select a start date.");
            return;
        }

        // Create the download URL with the start date as a query parameter
        window.location.href = `download_excel.php?start_date=${startDate}`; // This will work as intended
    }

    // Open the edit modal and populate fields
    function openEditModal(id, firstName, lastName, email, travelDate, travelType, outbound, returnTravel, seats,
        phoneNumber, office, travelReason) {
        document.getElementById('reservationId').value = id;
        document.getElementById('editFirstName').value = firstName;
        document.getElementById('editLastName').value = lastName;
        document.getElementById('editEmail').value = email;
        document.getElementById('editTravelDate').value = travelDate;
        document.getElementById('editTravelType').value = travelType;
        document.getElementById('editOutboundTravel').value = outbound;
        document.getElementById('editReturnTravel').value = returnTravel;
        document.getElementById('editSeats').value = seats;
        document.getElementById('editPhoneNumber').value = phoneNumber;
        document.getElementById('editOffice').value = office;
        document.getElementById('editTravelReason').value = travelReason;

        document.getElementById('editModal').style.display = 'block'; // Show the modal
    }

    // Close the modal
    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Update reservation
    function updateReservation() {
        const id = document.getElementById('reservationId').value;
        const data = {
            id,
            firstName: document.getElementById('editFirstName').value,
            lastName: document.getElementById('editLastName').value,
            email: document.getElementById('editEmail').value,
            travelDate: document.getElementById('editTravelDate').value,
            travelType: document.getElementById('editTravelType').value,
            outbound: document.getElementById('editOutboundTravel').value,
            returnTravel: document.getElementById('editReturnTravel').value,
            seats: document.getElementById('editSeats').value,
            phoneNumber: document.getElementById('editPhoneNumber').value,
            office: document.getElementById('editOffice').value,
            travelReason: document.getElementById('editTravelReason').value
        };

        // Validate required fields
        if (!data.firstName || !data.lastName || !data.email || !data.travelDate) {
            alert('Please fill in all required fields.');
            return;
        }

        console.log('Sending data to the server:', data); // Debugging log

        fetch(`${apiUrl}?action=update`, {
                method: 'PUT', // Change to PUT for update
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert('Reservation updated successfully!');
                    closeModal(); // Close the modal
                    fetchReservations(); // Refresh the table
                } else {
                    alert('Error updating reservation: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error updating reservation:', error);
                alert('An error occurred while updating the reservation. Please try again.');
            });
    }
    // Open the edit modal and populate fields
    function openEditModal(id, firstName, lastName, email, travelDate, travelType, outbound, returnTravel, seats,
        phoneNumber, office, travelReason) {
        document.getElementById('reservationId').value = id;
        document.getElementById('editFirstName').value = firstName;
        document.getElementById('editLastName').value = lastName;
        document.getElementById('editEmail').value = email;
        document.getElementById('editTravelDate').value = travelDate;
        document.getElementById('editTravelType').value = travelType;
        document.getElementById('editOutboundTravel').value = outbound;
        document.getElementById('editReturnTravel').value = returnTravel;
        document.getElementById('editSeats').value = seats;
        document.getElementById('editPhoneNumber').value = phoneNumber;
        document.getElementById('editOffice').value = office;
        document.getElementById('editTravelReason').value = travelReason;

        document.getElementById('editModal').style.display = 'block'; // Show the modal
    }

    // Close the modal
    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function updateReservation() {
        // Get the reservation ID and input values from the form
        const id = document.getElementById('reservationId').value;
        const data = {
            id,
            first_name: document.getElementById('editFirstName').value,
            last_name: document.getElementById('editLastName').value,
            email: document.getElementById('editEmail').value,
            travel_date: document.getElementById('editTravelDate').value,
            travel_type: document.getElementById('editTravelType').value,
            outbound_travel: document.getElementById('editOutboundTravel').value,
            return_travel: document.getElementById('editReturnTravel').value,
            seats: document.getElementById('editSeats').value,
            phone_number: document.getElementById('editPhoneNumber').value,
            office: document.getElementById('editOffice').value,
            travel_reason: document.getElementById('editTravelReason').value
        };

        // Send the data to the server
        fetch(`${apiUrl}/${id}`, { // Assuming your API endpoint includes the reservation ID
                method: 'PUT', // Use PUT for updating a resource
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`); // Handle HTTP errors
                }
                return response.json(); // Parse the JSON response
            })
            .then(data => {
                console.log('Success:', data);
                alert(data.message); // Notify user of success with a message
                closeModal(); // Close the modal
                fetchReservations(); // Call function to refresh data in the table
            })
            .catch(error => {
                console.error('Error updating reservation:', error);
                alert('Error updating reservation: ' + error.message); // Notify user of error
            });
    }

    function deleteReservation(reservationId) {
        if (confirm("Are you sure you want to delete this reservation?")) {
            fetch(apiUrl, {
                    method: 'DELETE', // Use DELETE method for removing the record
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: reservationId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Success:', data);
                    // Refresh the reservations list after deletion
                    fetchReservations(); // Call this function to refresh the reservations list
                })
                .catch(error => console.error('Error deleting reservation:', error));
        }
    }
    </script>
</body>

</html>