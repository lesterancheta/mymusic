<?php

namespace App\Controllers;
use App\Models\PlaylistModel;
use App\Models\PlayListMusicModel;
use App\Models\MusicModel;

class MusicController extends BaseController
{
    public function index()
{
    $musicModel = new MusicModel();
    $musicList = $musicModel->findAll();

    // Fetch the playlists here
    $playlistModel = new PlaylistModel();
    $playlists = $playlistModel->findAll();

    return view('music_player', ['musicList' => $musicList, 'playlists' => $playlists]);
}


    public function createPlaylist()
    {
        $playlistName = $this->request->getPost('playlist_name');

        $playlistModel = new PlaylistModel();
        $playlistModel->insert(['name' => $playlistName]);

        // Redirect to the index action to display the music list
        return redirect()->to('/');
    }

    public function getPlaylists()
    {
        $playlistModel = new PlaylistModel();
        $playlists = $playlistModel->findAll();

        return $this->response->setJSON($playlists);
    }

    public function uploadMusic()
    {
        $musicModel = new MusicModel();

        $file = $this->request->getFile('musicFile'); // Get the uploaded file

        if ($file->isValid() && $file->getClientExtension() === 'mp3') {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads', $newName); // Move the file to a directory

            // Save file information to the database
            $musicModel->insert([
                'file_name' => $newName,
                'file_path' => 'uploads/' . $newName, // Adjust the path as needed
            ]);

            return redirect()->to('/')->with('success', 'Music uploaded successfully');
        } else {
            return redirect()->to('/music')->with('error', 'Invalid or unsupported file format');
        }
    }
    public function addToPlaylist()
    {
        $musicID = $this->request->getPost('musicID');
        $playlistID = $this->request->getPost('playlistID');
    
        // You can add validation here to ensure the selected playlist and music track exist and check user permissions.
    
        $playlistMusicModel = new PlaylistMusicModel();
        
        // Check if the association already exists, and if not, insert it.
        $existingAssociation = $playlistMusicModel->where('playlist_id', $playlistID)
                                                ->where('music_track_id', $musicID)
                                                ->countAllResults();
        
        if ($existingAssociation === 0) {
            $playlistMusicModel->insert([
                'playlist_id' => $playlistID,
                'music_track_id' => $musicID,
            ]);
    
            return redirect()->to('/')->with('success', 'Music added to the playlist.');
        } else {
            return redirect()->to('/')->with('error', 'Music is already in the playlist.');
        }
    
        // You may want to handle errors, success messages, or redirects here.
    
        // Redirect to the index or the playlist page.
        return redirect()->to('/')->with('success', 'Music added to the playlist.');
    }

    

}
