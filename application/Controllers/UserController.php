<?php
/**
 * Created by PhpStorm.
 * User: Rhilip
 * Date: 2018/12/31
 * Time: 11:26
 */

namespace App\Controllers;

use App\Models\Form\User;

use Rid\Http\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function details()
    {
        $panel = new User\UserDetailsForm();
        $panel->setInput(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->query->all());
        if (!$panel->validate()) {
            return $this->render('action/fail', ['msg' => $panel->getError()]);
        }

        return $this->render('user/details', ['details' => $panel]);
    }

    public function setting()
    {
        return $this->render('user/setting');
    }

    public function invite()
    {
        $msg = '';
        if (\Rid\Helpers\ContainerHelper::getContainer()->get('request')->isMethod(Request::METHOD_POST)) {
            $form = new User\InviteForm();
            $form->setInput(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->request->all());
            $success = $form->validate();
            if ($success) {
                $form->flush();
                $msg = 'Send Invite Success!';
            } else {
                return $this->render('action/fail', ['title' => 'Invite Failed', 'msg' => $form->getError()]);
            }
        }

        $user = \Rid\Helpers\ContainerHelper::getContainer()->get('auth')->getCurUser();
        $uid = \Rid\Helpers\ContainerHelper::getContainer()->get('request')->query->get('uid');
        if (!is_null($uid) && $uid != \Rid\Helpers\ContainerHelper::getContainer()->get('auth')->getCurUser()->getId()) {
            if (\Rid\Helpers\ContainerHelper::getContainer()->get('auth')->getCurUser()->isPrivilege('view_invite')) {
                $user = \Rid\Helpers\ContainerHelper::getContainer()->get('site')->getUser($uid);
            } else {
                return $this->render('action/fail', ['title' => 'Fail', 'msg' => 'Privilege is not enough to see other people\'s invite status.']);
            }
        }

        // FIXME By using Form Class
        if (!is_null(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->query->get('action'))) {
            $action_form = new User\InviteActionForm();
            $action_form->setInput(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->query->all());
            $success = $action_form->validate();
            if ($success) {
                $msg = $action_form->flush();
            } else {
                return $this->render('action/fail', ['title' => 'Invite Failed', 'msg' => $action_form->getError()]);
            }
        }

        return $this->render('user/invite', ['user' => $user, 'msg' => $msg]);
    }


    public function sessions()
    {
        if (\Rid\Helpers\ContainerHelper::getContainer()->get('request')->isMethod(Request::METHOD_POST)) {
            $action = \Rid\Helpers\ContainerHelper::getContainer()->get('request')->request->get('action');  // FIXME
            if ($action == 'revoke') {
                $to_del_session = \Rid\Helpers\ContainerHelper::getContainer()->get('request')->request->get('session');

                // expired it from Database first
                \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->prepare('UPDATE `sessions` SET `expired` = 1 WHERE `uid` = :uid AND `session` = :sid')->bindParams([
                    'uid' => \Rid\Helpers\ContainerHelper::getContainer()->get('auth')->getCurUser()->getId(), 'sid' => $to_del_session
                ])->execute();
                $success = \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->getRowCount();

                if ($success > 0) {
                    \Rid\Helpers\ContainerHelper::getContainer()->get('redis')->zRem(\Rid\Helpers\ContainerHelper::getContainer()->get('auth')->getCurUser()->sessionSaveKey, $to_del_session);
                } else {
                    return $this->render('action/fail', ['title' => 'Remove Session Failed', 'msg' => 'Remove Session Failed']);
                }
            }
        }

        $session_list = new User\SessionsListForm();
        $session_list->setInput(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->query->all());
        if (false === $session_list->validate()) {
            return $this->render('action/fail', ['msg' => $session_list->getError()]);
        }

        return $this->render('user/sessions', ['session_list' => $session_list]);
    }
}
