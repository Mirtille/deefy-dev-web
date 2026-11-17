<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\repository\DeefyRepository;

class AddPodcastTrackAction extends Action {
    public function execute(): string {
        if (!isset($_SESSION['playlist'])) {
            return "<p>Veuillez d'abord sélectionner une playlist dans <a href='?action=my-playlists'>Mes playlists</a> ou <a href='?action=add-playlist'>en créer une</a>.</p>";
        }

        $idPl = (int)$_SESSION['playlist']->id;
        $repo = DeefyRepository::getInstance();

        if ($this->http_method === 'GET') {
            return <<<HTML
            <h2>Ajouter une piste à la playlist courante</h2>
            <form method="post" action="?action=add-track" enctype="multipart/form-data">
                <div>
                    <label>Titre : <input type="text" name="titre" required maxlength="100"></label>
                </div>
                <div>
                    <label>Genre : <input type="text" name="genre" maxlength="30" placeholder="Ex: rock, podcast..."></label>
                </div>
                <div>
                    <label>Durée (en secondes) : <input type="number" name="duree" min="1" max="36000" value="180" required></label>
                </div>
                <div>
                    <label>Auteur : <input type="text" name="auteur" maxlength="100"></label>
                </div>
                <div>
                    <label>Date : <input type="date" name="date"></label>
                </div>
                <div>
                    <label>Fichier audio (.mp3) : <input type="file" name="userfile" required accept=".mp3,audio/mpeg"></label>
                </div>
                <div>
                    <button type="submit">Ajouter la piste</button>
                </div>
            </form>
            HTML;
        }

        $titre = trim($_POST['titre'] ?? '');
        $genre = trim($_POST['genre'] ?? '');
        $duree = filter_input(INPUT_POST, 'duree', FILTER_VALIDATE_INT) ?: 0;
        $auteur = trim($_POST['auteur'] ?? '');
        $date = trim($_POST['date'] ?? '');

        if ($titre === '') {
            return "<p class='error'>Le titre de la piste est obligatoire.</p>";
        }

        if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
            return "<p class='error'>Erreur lors du téléchargement du fichier audio.</p>";
        }

        $originalName = $_FILES['userfile']['name'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo->file($_FILES['userfile']['tmp_name']);

        if (!str_ends_with(strtolower($originalName), '.mp3') || str_contains(strtolower($originalName), '.php') || !in_array($detectedMime, ['audio/mpeg', 'audio/mp3', 'application/octet-stream'], true)) {
            return "<p class='error'>Erreur : seul le format audio .mp3 valide est autorisé.</p>";
        }

        $uniqueFilename = uniqid('track_', true) . '.mp3';
        $destPath = __DIR__ . '/../../../audio/' . $uniqueFilename;

        if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $destPath)) {
            return "<p class='error'>Échec de l'enregistrement du fichier sur le serveur.</p>";
        }

        $track = new PodcastTrack($titre, $uniqueFilename);
        if ($genre !== '' && method_exists($track, 'setGenre')) $track->setGenre($genre);
        if ($duree > 0 && method_exists($track, 'setDuree')) $track->setDuree($duree);
        if ($auteur !== '' && method_exists($track, 'setAuteur')) $track->setAuteur($auteur);
        if ($date !== '' && method_exists($track, 'setDate')) $track->setDate($date);

        $track = $repo->saveTrack($track);
        $repo->addTrackToPlaylist((int)$track->id, $idPl);

        $_SESSION['playlist'] = $repo->findPlaylistById($idPl);

        header("Location: ?action=playlist");
        exit();
    }
}