<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;

class DisplayUserPlaylistsAction extends Action {
    public function execute(): string {
        $repo = DeefyRepository::getInstance();
        $playlists = $repo->findAllPlaylists();

        $html = "<h2>Mes playlists</h2>";
        if (empty($playlists)) {
            $html .= "<p>Aucune playlist enregistrée en base de données.</p>";
        } else {
            $html .= "<ul class='playlist-list'>";
            foreach ($playlists as $pl) {
                $id = (int)$pl->id;
                $nom = htmlspecialchars($pl->nom, ENT_QUOTES, 'UTF-8');
                $html .= "<li><a href='?action=playlist&id=$id'>$nom</a></li>";
            }
            $html .= "</ul>";
        }

        $html .= "<p><a class='btn' href='?action=add-playlist'>+ Créer une nouvelle playlist</a></p>";
        return $html;
    }
}