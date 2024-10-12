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
            overflow: hidden;
        }

        th,
        td {
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

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            text-align: center;
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

        input[type="date"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            width: 200px;
            margin-right: 10px;
            transition: border-color 0.3s ease;
        }

        input[type="date"]:focus {
            border-color: #4CAF50;
            outline: none;
        }
    </style>
</head>

<body>

    <h1>Reservation List</h1>

    <label for="startDate">Start Date:</label>
    <input type="date" id="startDate" required>
    <button onclick="fetchReservations()">Filter</button>

    <table>
        <thead>
            <tr>
                <th>Travel Date</th>
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
            </tr>
        </thead>
        <tbody id="reservationTableBody">
            <!-- Reservation data will be inserted here dynamically -->
        </tbody>
    </table>

    <script>
        const apiUrl = '/API/v1/driver/reserve_seat.php';

        function fetchReservations() {
            const startDate = document.getElementById('startDate').value;

            if (!startDate) {
                alert("Please select a start date.");
                return;
            }

            const query = new URLSearchParams({ start_date: startDate }).toString();
            const url = `${apiUrl}?${query}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('reservationTableBody');
                    tableBody.innerHTML = '';

                    if (data.length > 0) {
                        data.forEach(reservation => {
                            if (reservation.status !== 'ยกเลิกแล้ว' && reservation.status !== 'รอยืนยัน') {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${reservation.travel_date || '-'}</td>
                                    <td>${reservation.first_name}</td>
                                    <td>${reservation.last_name}</td>
                                    <td>${reservation.email}</td>
                                    <td>${reservation.travel_type}</td>
                                    <td>${reservation.outbound_travel || '-'}</td>
                                    <td>${reservation.return_travel || '-'}</td>
                                    <td>${reservation.seats}</td>
                                    <td>${reservation.phone_number}</td>
                                    <td>${reservation.office}</td>
                                    <td>${reservation.travel_reason}</td>
                                    <td>${reservation.status}</td>
                                `;
                                tableBody.appendChild(row);
                            }
                        });

                        if (tableBody.children.length === 0) {
                            const row = document.createElement('tr');
                            row.innerHTML = `<td colspan="12">No reservations found.</td>`;
                            tableBody.appendChild(row);
                        }

                    } else {
                        const row = document.createElement('tr');
                        row.innerHTML = `<td colspan="12">No reservations found.</td>`;
                        tableBody.appendChild(row);
                    }
                })
                .catch(error => console.error('Error fetching the reservation data:', error));
        }
    </script>

</body>

</html>
