<?php
/**
 * Created by PhpStorm.
 * User: Rhilip
 * Date: 2019/3/16
 * Time: 16:53
 */

namespace App\Controllers;

use App\Models\Form\Torrent;

use Rid\Http\Controller;

class TorrentController extends Controller
{
    public function actionDetails()
    {
        $details = new Torrent\DetailsForm();
        $success = $details->validate();
        if (!$success) {
            return $this->render('action/fail', ['msg' => $details->getError()]);
        }

        return $this->render('torrent/details', ['details' => $details]);
    }

    public function actionEdit() // TODO
    {

    }

    public function actionSnatch()
    {
        $snatch = new Torrent\SnatchForm();
        $success = $snatch->validate();
        if (!$success) {
            return $this->render('action/fail');
        }

        return $this->render('torrent/snatch', ['snatch' => $snatch]);
    }

    public function actionDownload()
    {
        $downloader = new Torrent\DownloadForm();
        $success = $downloader->validate();
        if (!$success) {
            return $this->render('action/fail');
        }

        return $downloader->sendFileContentToClient();
    }

    public function actionComments()
    {
        $comments = new Torrent\CommentsForm();
        $success = $comments->validate();
        if (!$success) {
            return $this->render('action/fail');
        }

        return $this->render('torrent/comments', ['comments' => $comments]);
    }

    public function actionStructure()
    {
        $structure = new Torrent\StructureForm();
        $success = $structure->validate();
        if (!$success) {
            return $this->render('action/fail');
        }

        return $this->render('torrent/structure', ['structure' => $structure]);
    }
}