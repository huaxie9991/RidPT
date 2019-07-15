<?php
/**
 * Created by PhpStorm.
 * User: Rhilip
 * Date: 2019/6/15
 * Time: 17:12
 */

namespace apps\controllers;

use apps\models\form\Links;
use Rid\Http\Controller;

class LinksController extends Controller
{
    public function actionIndex()
    {
        return app()->response->redirect('/links/manage', 301);
    }

    public function actionApply()
    {
        if (app()->request->isPost()) {
            $form = new Links\ApplyForm();
            $form->setData(app()->request->post());
            $success = $form->validate();
            if ($success) {
                $form->flush();
                return $this->render('links/apply_success');
            } else {
                return $this->render('errors/action_fail', ['msg' => $form->getError()]);
            }
        }

        return $this->render('links/apply');
    }

    public function actionManage()
    {
        if (app()->request->isPost()) {
            if (app()->request->post('action') == 'link_edit') {
                $edit_form = new Links\EditForm();
                $edit_form->setData(app()->request->post());
                $success = $edit_form->validate();
                if ($success) {
                    $edit_form->flush();
                } else {
                    return $this->render('errors/action_fail', ['msg' => $edit_form->getError()]);
                }
            } elseif (app()->request->post('action') == 'link_delete') {
                $delete_form = new Links\RemoveForm();
                $delete_form->setData(app()->request->post());
                $success = $delete_form->validate();
                if ($success) {
                    $delete_form->flush();
                } else {
                    return $this->render('errors/action_fail', ['msg' => $delete_form->getError()]);
                }
            }
        }


        $all_links = app()->pdo->createCommand("SELECT * FROM `links` ORDER BY FIELD(`status`,'enabled','pending','disabled'),`id` ASC")->queryAll();

        return $this->render('links/manage', ['links' => $all_links]);
    }
}