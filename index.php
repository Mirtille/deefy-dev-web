<?php

require_once 'vendor/autoload.php';

use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayUserPlaylistsAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;

session_start();

DeefyRepository::setConfig(__DIR__ . '/config.db.ini');

$dispatcher = new Dispatcher();

$dispatcher->addRoute('default', DefaultAction::class)
           ->addRoute('my-playlists', DisplayUserPlaylistsAction::class)
           ->addRoute('playlist', DisplayPlaylistAction::class)
           ->addRoute('add-playlist', AddPlaylistAction::class)
           ->addRoute('add-track', AddPodcastTrackAction::class);

$dispatcher->run();