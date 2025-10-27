<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Hent klasser til listeboks
$klasser = $pdo->query('SELECT klassekode, klassenavn FROM klasse ORDER BY klassekode')->fetchAll();

// Registrer student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $brukernavn = trim($_POST['brukernavn'] ?? '');
    $fornavn    = trim($_POST['fornavn'] ?? '');
    $etternavn  = trim($_POST['etternavn'] ?? '');
    $klassekode = trim($_POST['klassekode'] ?? '');

    if ($brukernavn !== '' && $fornavn !== '' && $etternavn !== '' && $klassekode !== '') {
        $stmt = $pdo->prepare('INSERT INTO student (brukernavn, fornavn, etternavn, klassekode) VALUES (?, ?, ?, ?)');
        try {
            $stmt->execute([$brukernavn, $fornavn, $etternavn, $klassekode]);
            $msg = 'Student registrert.';
        } catch (PDOException $ex) {
            $msg = 'Feil ved registrering: ' . e($ex->getMessage());
        }
    } else {
        $msg = 'Alle felter må fylles ut.';
    }
}

// Sletting
if (isset($_GET['delete'])) {
    $del = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM student WHERE brukernavn = ?');
    try {
        $stmt->execute([$del]);
        $msg = 'Student slettet (hvis den fantes).';
    } catch (PDOException $ex) {
        $msg = 'Feil ved sletting: ' . e($ex->getMessage());
    }
}

// Hent alle studenter (med klassenavn)
$studenter = $pdo->query('
  SELECT s.brukernavn, s.fornavn, s.etternavn, s.klassekode, k.klassenavn
  FROM student s
  LEFT JOIN klasse k ON k.klassekode = s.klassekode
  ORDER BY s.brukernavn
')->fetchAll();
?>
<!doctype html>
<html lang="nb">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Studenter</title>
  <style>
    :root { font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Arial, sans-serif; }
    body { max-width: 1000px; margin: 2rem auto; padding: 0 1rem; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
    th { background: #f5f5f5; }
    .card { border: 1px solid #ddd; border-radius: 12px; padding: 1rem 1.25rem; margin: 1rem 0 2rem; }
    .row { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: .5rem; }
    .actions a { color: #b00; text-decoration: none; }
    .msg { margin: .5rem 0 1rem; color: #064; }
    a.button { display: inline-block; padding: .4rem .8rem; border: 1px solid #ccc; border-radius: 8px; text-decoration: none; }
    select, input, button { min-height: 36px; }
  </style>
</head>
<body>
  <p><a class="button" href="index.html">← Til meny</a></p>
  <h1>Administrer studenter</h1>

  <?php if (!empty($msg)): ?>
    <div class="msg"><?= e($msg) ?></div>
  <?php endif; ?>

  <div class="card">
    <h2>Registrer ny student</h2>
    <form method="post">
      <input type="hidden" name="action" value="create">
      <div class="row">
        <input name="brukernavn" placeholder="Brukernavn" maxlength="7" required>
        <input name="fornavn" placeholder="Fornavn" maxlength="50" required>
        <input name="etternavn" placeholder="Etternavn" maxlength="50" required>
        <select name="klassekode" required>
          <option value="">Velg klasse…</option>
          <?php foreach ($klasser as $k): ?>
            <option value="<?= e($k['klassekode']) ?>"><?= e($k['klassekode'] . ' — ' . $k['klassenavn']) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit">Lagre</button>
      </div>
    </form>
  </div>

  <h2>Alle studenter</h2>
  <table>
    <thead>
      <tr>
        <th>Brukernavn</th>
        <th>Fornavn</th>
        <th>Etternavn</th>
        <th>Klassekode</th>
        <th>Klassenavn</th>
        <th>Handling</th>
      </tr>
    </thead>
    <tbody>
    <?php if (!$studenter): ?>
      <tr><td colspan="6">Ingen data.</td></tr>
    <?php else: ?>
      <?php foreach ($studenter as $s): ?>
        <tr>
          <td><?= e($s['brukernavn']) ?></td>
          <td><?= e($s['fornavn']) ?></td>
          <td><?= e($s['etternavn']) ?></td>
          <td><?= e($s['klassekode']) ?></td>
          <td><?= e($s['klassenavn'] ?? '') ?></td>
          <td class="actions">
            <a href="?delete=<?= urlencode($s['brukernavn']) ?>" onclick="return confirm('Slette studenten <?= e($s['brukernavn']) ?>?')">Slett</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</body>
</html>

