<?php
$db = new PDO('sqlite:' . __DIR__ . '/events.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("CREATE TABLE IF NOT EXISTS events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    event_date TEXT,
    status TEXT NOT NULL DEFAULT 'Planned',
    notes TEXT
)");
