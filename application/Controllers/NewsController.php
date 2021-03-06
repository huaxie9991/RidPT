<?php
/**
 * Created by PhpStorm.
 * User: Rhilip
 * Date: 2019/5/31
 * Time: 10:09
 */

namespace App\Controllers;

use App\Models\Form\News;
use Rid\Http\Controller;
use Symfony\Component\HttpFoundation\Request;

class NewsController extends Controller
{
    public function index()
    {
        $pager = new News\SearchForm();
        $pager->setInput(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->query->all());

        $success = $pager->validate();
        if (!$success) {
            return $this->render('action/fail', ['title' => 'Attack', 'msg' => $pager->getError()]);
        } else {
            return $this->render('news/index', ['pager'=>$pager]);
        }
    }

    public function new()
    {
        if (\Rid\Helpers\ContainerHelper::getContainer()->get('request')->isMethod(Request::METHOD_POST)) {
            $newform = new News\EditForm();
            $newform->setInput(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->request->all());
            $success = $newform->validate();
            if (!$success) {
                return $this->render('action/fail', ['title' => 'new blog failed', 'msg' => $newform->getError()]);
            } else {
                $newform->flush();  // Save the news
                return \Rid\Helpers\ContainerHelper::getContainer()->get('response')->setRedirect('/news');
            }
        } elseif (\Rid\Helpers\ContainerHelper::getContainer()->get('auth')->getCurUser()->isPrivilege('manage_news')) {
            return $this->render('news/edit');
        }
        return $this->render('action/fail', ['title' => 'Action Failed', 'msg' => 'action not allowed']);
    }

    public function edit()
    {
        if (\Rid\Helpers\ContainerHelper::getContainer()->get('request')->isMethod(Request::METHOD_POST)) {
            $newform = new News\EditForm();
            $newform->setInput(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->request->all());
            $success = $newform->validate();
            if (!$success) {
                return $this->render('action/fail', ['title' => 'Upload Failed', 'msg' => $newform->getError()]);
            } else {
                $newform->flush();  // Save the news
                return \Rid\Helpers\ContainerHelper::getContainer()->get('response')->setRedirect('/news');
            }
        } elseif (\Rid\Helpers\ContainerHelper::getContainer()->get('auth')->getCurUser()->isPrivilege('manage_news')) {
            $id = \Rid\Helpers\ContainerHelper::getContainer()->get('request')->query->get('id', 0);
            if (filter_var($id, FILTER_VALIDATE_INT) && $id > 0) {
                // TODO add other check
                $news = \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->prepare('SELECT * FROM news WHERE id= :id')->bindParams(['id' => $id])->queryOne();
                return $this->render('news/edit', ['news' => $news]);
            }
        }
        return $this->render('action/fail', ['title' => 'Action Failed', 'msg' => 'action not allowed']);
    }

    public function delete()
    {
        if (\Rid\Helpers\ContainerHelper::getContainer()->get('auth')->getCurUser()->isPrivilege('manage_news')) {
            $id = \Rid\Helpers\ContainerHelper::getContainer()->get('request')->query->get('id', 0);
            if (filter_var($id, FILTER_VALIDATE_INT) && $id > 0) {
                // TODO add other check
                \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->prepare('DELETE FROM news WHERE id= :id')->bindParams(['id'=>$id])->execute();
            }
            return \Rid\Helpers\ContainerHelper::getContainer()->get('response')->setRedirect('/news');
        }
        return $this->render('action/fail', ['title' => 'Action Failed', 'msg' => 'action not allowed']);
    }
}
