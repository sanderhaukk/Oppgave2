<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

// Hjelpefunksjon for escaping i HTML
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Opprett (registrering)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $klassekode = trim($_POST['klassekode'] ?? '');
    $klassenavn = trim($_POST['klassenavn'] ?? '');
    $studiumkode = trim($_POST['studiumkode'] ?? '');

    if ($klassekode !== '' && $klassenavn !== '' && $studiumkode !== '') {
        $stmt = $pdo->prepare('INSERT INTO klasse (klassekode, klassenavn, studiumkode) VALUES (?, ?, ?)');
        try {
            $stmt->execute([$klassekode, $klassenavn, $studiumkode]);
            $msg = 'Klasse registrert.';
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
    $stmt = $pdo->prepare('DELETE FROM klasse WHERE klassekode = ?');
    try {
        $stmt->execute([$del]);
        $msg = 'Klasse slettet (hvis den fantes).';
    } catch (PDOException $ex) {
        $msg = 'Feil ved sletting: ' . e($ex->getMessage());
    }
}

// Hent alle klasser for visning
$klasser = $pdo->query('SELECT klassekode, klassenavn, studiumkode FROM klasse ORDER BY klassekode')->fetchAll();
?>
<!doctype html>
<html lang="nb">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Klasser</title>
  <style>
    :root { font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Arial, sans-serif; }
    body { max-width: 1000px; margin: 2rem auto; padding: 0 1rem; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; }
    th { background: #f5f5f5; }
    form.inline { display: inline; }
    .card { border: 1px solid #ddd; border-radius: 12px; padding: 1rem 1.25rem; margin: 1rem 0 2rem; }
    .row { display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: .5rem; }
    .actions a { color: #b00; text-decoration: none; }
    .msg { margin: .5rem 0 1rem; color: #064; }
    a.button { display: inline-block; padding: .4rem .8rem; border: 1px solid #ccc; border-radius: 8px; text-decoration: none; }
  </style>
</head>
<body>
  <p><a class="button" href="index.html">← Til meny</a></p>
  <h1>Administrer klasser</h1>

  <?php if (!empty($msg)): ?>
    <div class="msg"><?= e($msg) ?></div>
  <?php endif; ?>

  <div class="card">
    <h2>Registrer ny klasse</h2>
    <form method="post">
      <input type="hidden" name="action" value="create">
      <div class="row">
        <input name="klassekode" placeholder="Klassekode (f.eks. IT1)" maxlength="5" required>
        <input name="klassenavn" placeholder="Klassenavn" maxlength="50" required>
        <input name="studiumkode" placeholder="Studiumkode" maxlength="50" required>
        <button type="submit">Lagre</button>
      </div>
    </form>
  </div>

  <h2>Alle klasser</h2>
  <table>
    <thead>
      <tr>
        <th>Klassekode</th>
        <th>Klassenavn</th>
        <th>Studiumkode</th>
        <th>Handling</th>
      </tr>
    </thead>
    <tbody>
    <?php if (!$klasser): ?>
      <tr><td colspan="4">Ingen data.</td></tr>
    <?php else: ?>
      <?php foreach ($klasser as $k): ?>
        <tr>
          <td><?= e($k['klassekode']) ?></td>
          <td><?= e($k['klassenavn']) ?></td>
          <td><?= e($k['studiumkode']) ?></td>
          <td class="actions">
            <a href="?delete=<?= urlencode($k['klassekode']) ?>" onclick="return confirm('Slette klassen <?= e($k['klassekode']) ?>?')">Slett</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>
</body>
</html>

