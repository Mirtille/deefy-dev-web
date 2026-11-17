<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\AudioListRenderer;

class AddPlaylistAction extends Action {
    public function execute(): string {
        if ($this->http_method === 'GET') {
            return <<<HTML
            <h2>Créer une nouvelle playlist</h2>
            <form method="post" action="?action=add-playlist">
                <label>Nom de la playlist : 
                    <input type="text" name="nom" required maxlength="100" placeholder="Ex: Mes favoris">
                </label>
                <button type="submit">Créer</button>
            </form>
            HTML;
        }

        $nom = trim($_POST['nom'] ?? '');
        if ($nom === '') {
            return "<p class='error'>Erreur : le nom de la playlist ne peut pas être vide.</p>";
        }

        $repo = DeefyRepository::getInstance();
        $playlist = new Playlist($nom);
        $playlist = $repo->saveEmptyPlaylist($playlist);

        $_SESSION['playlist'] = $playlist;

        $renderer = new AudioListRenderer($playlist);
        $html = "<p class='success'>Playlist créée et enregistrée !</p>";
        $html .= $renderer->render(1);
        $html .= '<p><a class="btn" href="?action=add-track">+ Ajouter une piste</a></p>';

        return $html;
    }
}