<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit;
}

if (isset($_GET['logout'])) {
    setcookie("user_email", "", time() - 3600, "/");

    session_unset();
    session_destroy();

    header("Location: connection.php");
    exit;
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    header("Location: connection.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion des Contacts</title>
    <!-- Inclure les styles Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Inclure les fichiers CSS et JavaScript de FullCalendar -->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.0/main.min.css' rel='stylesheet'/>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridWeek'
        });
        calendar.render();
        calendar.setOption('locale', 'fr');
        calendar.setOption('height', 500);
      });
    </script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Bienvenue <?php echo $username ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <!-- Lien "Se déconnecter" -->
            <li class="nav-item">
                <a class="nav-link" href="?logout">Se déconnecter</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Tableau de Bord - Gestion de Rendez-vous</h2>
    <p>Bienvenue dans votre tableau de bord de gestion et de prise de rendez-vous.</p>
    <div class="row">
        <div class="col-md-12">
            <div id='calendar'></div>
        </div>
    </div>
</div>

<script>

</script>

<!-- Inclure les scripts Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>