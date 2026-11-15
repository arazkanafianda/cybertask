<?php
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $stmt = $db->prepare(
            'INSERT INTO events (name, event_date, status, notes) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            trim($_POST['name']),
            $_POST['event_date'] ?: null,
            $_POST['status'],
            trim($_POST['notes'])
        ]);
    } elseif (isset($_POST['update'])) {
        $stmt = $db->prepare('UPDATE events SET status = ? WHERE id = ?');
        $stmt->execute([$_POST['status'], (int)$_POST['id']]);
    } elseif (isset($_POST['delete'])) {
        $stmt = $db->prepare('DELETE FROM events WHERE id = ?');
        $stmt->execute([(int)$_POST['id']]);
    }

    header('Location: index.php');
    exit;
}

$events = $db->query(
    'SELECT * FROM events ORDER BY event_date IS NULL, event_date'
)->fetchAll(PDO::FETCH_ASSOC);

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cybersecurity Event Planner</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <header>
        <h1>Cybersecurity Event Planner</h1>
        <p>Organize event dates, progress, and planning notes.</p>
    </header>

    <section class="card">
        <h2>Add an event</h2>
        <form class="add-form" method="post">
            <input name="name" placeholder="Event name" required>
            <input type="date" name="event_date">
            <select name="status">
                <option>Planned</option>
                <option>In progress</option>
                <option>Completed</option>
            </select>
            <input name="notes" placeholder="Planning notes">
            <button name="add" value="1">Add event</button>
        </form>
    </section>

    <section class="card">
        <h2>Events</h2>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Event</th><th>Date</th><th>Status</th>
                        <th>Notes</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$events): ?>
                    <tr><td colspan="5" class="empty">No events yet.</td></tr>
                <?php endif; ?>

                <?php foreach ($events as $event): ?>
                    <tr>
                        <td><strong><?= h($event['name']) ?></strong></td>
                        <td><?= h($event['event_date'] ?: '—') ?></td>
                        <td>
                            <form class="inline-form" method="post">
                                <input type="hidden" name="id" value="<?= (int)$event['id'] ?>">
                                <select name="status">
                                    <?php foreach (['Planned', 'In progress', 'Completed'] as $status): ?>
                                        <option <?= $event['status'] === $status ? 'selected' : '' ?>>
                                            <?= h($status) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button name="update" value="1">Save</button>
                            </form>
                        </td>
                        <td><?= h($event['notes']) ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Delete this event?')">
                                <input type="hidden" name="id" value="<?= (int)$event['id'] ?>">
                                <button class="delete" name="delete" value="1">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
