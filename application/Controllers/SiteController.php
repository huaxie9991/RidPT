<?php
/**
 * Created by PhpStorm.
 * User: Rhilip
 * Date: 8/17/2019
 * Time: 2019
 */

namespace App\Controllers;

use App\Models\Form\Site;
use Rid\Http\Controller;

class SiteController extends Controller
{
    public function rules()
    {
        return $this->render('site/rules');
    }

    public function logs()
    {
        $logs = new Site\Logs();
        $logs->setInput(\Rid\Helpers\ContainerHelper::getContainer()->get('request')->query->all());
        if (!$logs->validate()) {
            return $this->render('action/fail', ['msg'=>$logs->getError()]);
        }
        return $this->render('site/logs', ['logs'=>$logs]);
    }
}
