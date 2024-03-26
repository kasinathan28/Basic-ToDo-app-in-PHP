<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'test';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = []; 
    $sql = "SELECT * FROM tasks";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $timestamp = strtotime($row['timestamp']);
            $_SESSION['tasks'][] = [
                'task' => $row['task'],
                'date' => date('Y-m-d', $timestamp), 
                'time' => date('H:i:s', $timestamp)  
            ];
        }
    }
}




//code need enhancement


if (isset($_POST['task'], $_POST['date'], $_POST['time'])) {
    $task = $_POST['task'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    if (!empty($task) && !empty($date) && !empty($time)) {
        $datetime = $date . ' ' . $time;
        
        $sql = "INSERT INTO tasks (task, timestamp) VALUES ('$task', '$datetime')"; 
        if ($conn->query($sql) === TRUE) {
            $_SESSION['tasks'][] = [
                'task' => $task,
                'date' => $date,
                'time' => $time
            ];
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

if (isset($_GET['delete'])) {
    $index = $_GET['delete'];
    if (isset($_SESSION['tasks'][$index])) {
        $task_to_delete = $_SESSION['tasks'][$index]['task'];
        
        $sql = "DELETE FROM tasks WHERE task='$task_to_delete'";
        if ($conn->query($sql) === TRUE) {
            unset($_SESSION['tasks'][$index]);
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>To-Do List</h1>
        <form action="" method="post">
            <input type="text" name="task" placeholder="Enter task..." required>
            <input type="date" name="date" required>
            <input type="time" name="time" required>
            <button type="submit">Add Task</button>
        </form>
        <ul>
            <?php foreach ($_SESSION['tasks'] as $index => $taskInfo): ?>
                <li>
                    <?php echo $taskInfo['task']; ?>
                    <span class="timestamp"><?php echo $taskInfo['date']; ?> <?php echo $taskInfo['time']; ?></span>
                    <a class="delete-button" href="?delete=<?php echo $index; ?>">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
