<?php
class Catalog
{
    public static function subjects(): array
    {
        return Database::all('SELECT * FROM subjects ORDER BY name');
    }

    public static function topics(): array
    {
        return Database::all('SELECT t.*, s.name AS subject_name FROM topics t JOIN subjects s ON s.id = t.subject_id ORDER BY s.name, t.name');
    }

    public static function subject(int $id): ?array
    {
        return Database::one('SELECT * FROM subjects WHERE id=?', [$id]);
    }
}
