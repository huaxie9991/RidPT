<?php
/**
 * Created by PhpStorm.
 * User: Rhilip
 * Date: 8/7/2019
 * Time: 9:57 PM
 */

namespace App\Controllers;

use App\Models\Form\Subtitles;

use Rid\Http\Controller;
use Symfony\Component\HttpFoundation\Request;

class SubtitlesController extends Controller
{
    public function search($upload = null)
    {
        $search = new Subtitles\SearchForm();
        $search->setInput(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->query->all());
        if (false === $success = $search->validate()) {
            return $this->render('action/fail', ['msg' => $search->getError()]);
        }
        return $this->render('subtitles/search', ['search' => $search, 'upload_mode' => $upload]);
    }

    public function upload()
    {
        if (\Rid\Helpers\ContainerHelper::getContainer()->get('request')->isMethod(Request::METHOD_POST)) {
            $upload = new Subtitles\UploadForm();
            $upload->setInput(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->request->all() + \Rid\Helpers\ContainerHelper::getContainer()->get('request')->files->all());
            if (false === $success = $upload->validate()) {
                return $this->render('action/fail', ['msg' => $upload->getError()]);   // TODO add redirect
            } else {
                $upload->flush();
                return $this->render('action/success');  // TODO add redirect
            }
        }

        return $this->search(true);
    }

    public function download()
    {
        $download = new Subtitles\DownloadForm();
        $download->setInput(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->query->all());
        if (false === $success = $download->validate()) {
            return $this->render('action/fail', ['msg' => $download->getError()]);
        }

        return $download->sendFileContentToClient();
    }

    public function delete()
    {
        $delete = new Subtitles\DeleteForm();
        $delete->setInput(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->request->all());
        if (false === $success = $delete->validate()) {
            return $this->render('action/fail', ['msg' => $delete->getError()]);  // TODO add redirect
        } else {
            $delete->flush();
            return $this->render('action/success', ['redirect' => '/subtitles']); // TODO add redirect
        }
    }
}
