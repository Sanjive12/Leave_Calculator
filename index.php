<?php
// Include the common data
include('config.php');

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get form input values
    $staff = $_POST['staff'];
    $leaveFrom = $_POST['leave_from'];
    $leaveTo = $_POST['leave_to'];

    // Validate the leave dates
    if (empty($leaveFrom) || empty($leaveTo) || $leaveFrom > $leaveTo) {
        $message = "Please enter valid leave dates.";
    } else {
        // Calculate the number of leave days
        $leaveDays = date_diff(date_create($leaveFrom), date_create($leaveTo))->days + 1;

        // Check if the leave dates fall on holidays or Sundays
        $leaveDates = [];
        $currentDate = date_create($leaveFrom);
        while ($currentDate <= date_create($leaveTo)) {
            $dateStr = date_format($currentDate, 'Y-m-d');
            if (!in_array($dateStr, $holidays) && date_format($currentDate, 'N') != 7) {
                $leaveDates[] = $dateStr;
            }
            date_add($currentDate, date_interval_create_from_date_string('1 day'));
        }

        // Calculate the effective leave days
        $effectiveLeaveDays = count($leaveDates);

        // Check if the employee has enough leave balance
        if ($effectiveLeaveDays <= $staffs[$staff]) {
            // Deduct leave days from the balance
            $staffs[$staff] -= $effectiveLeaveDays;
            $message = "Employee: $staff<br>Leave dates: $leaveFrom till $leaveTo<br>Leave days: $effectiveLeaveDays<br>Leave eligible? Yes<br>Leave balance after leave: {$staffs[$staff]}";
        } else {
            $message = "Employee: $staff<br>Leave dates: $leaveFrom till $leaveTo<br>Leave days: $effectiveLeaveDays<br><span class='text-danger'>Leave eligible? No (Insufficient leave balance)</span>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leave Calculator</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Leave Calculator</h2>
        <form method="POST" action="" id="leaveForm">
            <div class="form-group">
                <label for="staff">Employee:</label>
                <select name="staff" id="staff" class="form-control" required>
                    <option value="" disabled selected>Please select a staff</option>
                    <?php foreach ($staffs as $name => $balance) {
                        $selected = ($_POST['staff'] ?? '') === $name ? 'selected' : '';
                        echo "<option value='$name' $selected>$name</option>";
                    } ?>
                </select>
                <div id="staff-error" class="text-danger"></div> <!-- Validation message for staff -->
            </div>

            <div class="form-group">
                <label for="leave_from">Leave From:</label>
                <input type="date" name="leave_from" id="leave_from" class="form-control" required
                    value="<?php echo $_POST['leave_from'] ?? ''; ?>">
                <div id="leave_from-error" class="text-danger"></div> <!-- Validation message for leave_from -->
            </div>

            <div class="form-group">
                <label for="leave_to">Leave To:</label>
                <input type="date" name="leave_to" id="leave_to" class="form-control" required
                    value="<?php echo $_POST['leave_to'] ?? ''; ?>">
                <div id="leave_to-error" class="text-danger"></div> <!-- Validation message for leave_to -->
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Calculate</button>

            <!-- Add Clear button -->
            <button type="button" id="clearForm" class="btn btn-secondary ml-2">Clear</button>
        </form>

        <?php
        if (isset($message)) {
            // Separate the message into parts for styling
            $parts = explode('<br>', $message);

            foreach ($parts as $part) {
                if (strpos($part, 'No (Insufficient leave balance)') !== false) {
                    echo "<p class='text-danger'>$part</p>"; // Display in red (danger)
                } elseif (strpos($part, 'Leave eligible? Yes') !== false) {
                    echo "<p class='text-success'>$part</p>"; // Display in green (success)
                } else {
                    echo "<p class='text-black'>$part</p>"; // Display in black
                }
            }
        }
        ?>
    </div>

    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Include jQuery Validate plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function () {
            // Form validation rules
            $("#leaveForm").validate({
                rules: {
                    staff: {
                        required: true,
                        notEqual: ""
                    },
                    leave_from: "required",
                    leave_to: "required",
                },
                messages: {
                    staff: {
                        required: "Please select an employee",
                        notEqual: "Please select an employee"
                    },
                    leave_from: "Please select a valid leave from date",
                    leave_to: "Please select a valid leave to date",
                },
                errorPlacement: function (error, element) {
                    // Display validation messages in separate div elements
                    error.appendTo("#" + element.attr("id") + "-error");
                },
                submitHandler: function (form) {
                    $.ajax({
                        type: 'POST',
                        url: 'leave_calculator.php',
                        data: $(form).serialize(),
                        success: function (response) {
                            if (response.indexOf("Validation Error") !== -1) {
                                // Display validation error message in red
                                $('#message').html('');
                                $('#message').addClass('text-danger');
                                $('#message').removeClass('text-success');
                                $('#message').html(response);
                            } else {
                                // Display success message in black
                                $('#message').html('');
                                $('#message').addClass('text-black');
                                $('#message').removeClass('text-danger');
                                $('#message').addClass('text-success');
                                $('#message').html(response);
                            }
                        }
                    });
                }
            });

            // Clear button click event
            $("#clearForm").click(function () {
                $('#leaveForm')[0].reset(); // Clear form fields

                // Clear form data from browser cache
                window.location.href = window.location.href;
            });
        });
    </script>
</body>
</html>






