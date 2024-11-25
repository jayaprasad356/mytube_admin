
<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();

$sql_query = "SELECT * FROM users";
$db->sql($sql_query);
$developer_records = $db->getResult();

$filename = "Allusers-data_" . date('Ymd') . ".csv";  // Change file extension to .csv
header("Content-Type: text/csv");  // Change MIME type to text/csv
header("Content-Disposition: attachment; filename=\"$filename\"");

// Open output stream for writing
$output = fopen('php://output', 'w');

if (!empty($developer_records)) {
    // Output the column headings
    fputcsv($output, array_keys($developer_records[0]));
    
    // Output each row of data
    foreach ($developer_records as $record) {
        fputcsv($output, $record);
    }
}

// Close output stream
fclose($output);
exit;
?>
