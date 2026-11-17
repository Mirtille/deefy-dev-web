<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\AudioListRenderer;

class DisplayPlaylistAction extends Action {
    public function execute(): string {
        $repo = DeefyRepository::getInstance();

        if (isset($_GET['id'])) {
            $idPl = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$idPl || $idPl <= 0) {
                return "<p class='error'>Identifiant de playlist invalide.</p>";
            }

            $playlist = $repo->findPlaylistById($idPl);
            if ($playlist === null) {
                return "<p class='error'>Playlist introuvable.</p>";
            }

            $_SESSION['playlist'] = $playlist;
        }

        if (!isset($_SESSION['playlist'])) {
            return "<p>Aucune playlist courante en session. Choisissez-en une dans <a href='?action=my-playlists'>Mes playlists</a> ou <a href='?action=add-playlist'>créez-en une</a>.</p>";
        }

        $playlist = $repo->findPlaylistById((int)$_SESSION['playlist']->id);
        $_SESSION['playlist'] = $playlist;

        $renderer = new AudioListRenderer($playlist);
        $html = $renderer->render(1);
        $html .= "<p><a class='btn' href='?action=add-track'>+ Ajouter une piste à cette playlist</a></p>";

        return $html;
    }
}