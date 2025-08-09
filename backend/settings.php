<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>System Settings</h2>
        <table class="table table-striped">
            <thead>
                <tr><th>Table</th><th>Field</th><th>Type</th><th>Options</th><th>Required</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php
                require 'db_connect.php';
                $stmt = $pdo->query("SELECT * FROM Settings");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                        <td>{$row['table_name']}</td>
                        <td>{$row['field_name']}</td>
                        <td>{$row['field_type']}</td>
                        <td>{$row['field_options']}</td>
                        <td><input type='checkbox' " . ($row['is_required'] ? 'checked' : '') . " data-id='{$row['setting_id']}'></td>
                        <td><button class='btn btn-sm btn-primary save-btn' data-id='{$row['setting_id']}'>Save</button></td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        document.querySelectorAll('.save-btn').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const id = e.target.dataset.id;
                const required = document.querySelector(`input[data-id='${id}']`).checked;
                const response = await fetch('update_settings.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({setting_id: id, is_required: required})
                });
                // Handle response
            });
        });
    </script>
</body>
</html>