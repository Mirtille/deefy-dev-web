<?php

namespace iutnc\deefy\action;

use iutnc\deefy\render\AudioListRenderer;

class DefaultAction extends Action {
    public function execute(): string {
        $html = "<h2>Bienvenue sur Deefy !</h2>";

        if (isset($_SESSION['playlist'])) {
            $renderer = new AudioListRenderer($_SESSION['playlist']);
            $html .= "<h3>Playlist courante :</h3>";
            $html .= $renderer->render();
            $html .= "<p><a class='btn' href='?action=add-track'>Ajouter une piste</a></p>";
        }

        return $html;
    }
}