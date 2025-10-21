<?php
include 'database/connection.php';

$appointment_id = $_GET['appointment_id'] ?? 0;

// Get messages with sender names
$stmt = $conn->prepare("
    SELECT cs.*, 
           a.fullname AS admin_name,
           s.fullname AS student_name
    FROM tbl_chat_sessions cs
    LEFT JOIN tbl_admin a ON cs.sender_type = 'admin' AND cs.sender_id = a.id
    LEFT JOIN tbl_students s ON cs.sender_type = 'student' AND cs.sender_id = s.id
    WHERE cs.appointment_id = ?
    ORDER BY cs.sent_at ASC
");
$stmt->execute([$appointment_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $msg) {
    $time = date("g:i A", strtotime($msg['sent_at']));
    $isStudent = ($msg['sender_type'] === 'student');
    $name = $isStudent ? htmlspecialchars('You') : htmlspecialchars($msg['admin_name'] ?? 'Counselor');
    $message = nl2br(htmlspecialchars($msg['message']));
    $float = $isStudent ? 'float-end' : 'float-start';
    $bgColor = $isStudent ? '#cfe2ff' : '#f1f0f0';
    $textAlign = $isStudent ? 'text-end' : 'text-start';

    echo "
    <div class='w-100 mb-2 clearfix'>
        <div class='d-inline-block p-2 rounded shadow-sm $float' style='max-width: 75%; background: $bgColor;'>
            <strong>$name</strong><br>
            <span>$message</span><br>
            <small class='text-muted $textAlign d-block'>$time</small>
        </div>
    </div>
    ";
}

// Add hidden chat status indicator for frontend use
if (!empty($messages)) {
    $lastStatus = end($messages)['chat_status'];
    echo "<div id='chatStatus' data-status='" . htmlspecialchars($lastStatus) . "'></div>";
}
?>
