<?php

namespace App\Controllers;

use Rid\Http\Controller;

class IndexController extends Controller
{

    // 默认动作
    public function actionIndex()
    {
        // Get Last News from redis cache
        $news = \Rid\Helpers\ContainerHelper::getContainer()->get('redis')->get('Site:recent_news');
        if ($news === false) { // Get news from Database and cache it in redis
            $news = \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->prepare('SELECT * FROM news ORDER BY create_at DESC LIMIT :max')->bindParams([
                'max' => config('base.max_news_sum')
            ])->queryAll();
            \Rid\Helpers\ContainerHelper::getContainer()->get('redis')->set('Site:recent_news', $news, 86400);
        }

        // Get All Links from redis cache
        $links = \Rid\Helpers\ContainerHelper::getContainer()->get('redis')->get('Site:links');
        if ($links === false) {
            $links = \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->prepare("SELECT `name`,`title`,`url` FROM links WHERE `status` = 'enabled' ORDER BY id ASC")->queryAll();
            \Rid\Helpers\ContainerHelper::getContainer()->get('redis')->set('Site:links', $links, 86400);
        }


        return $this->render('index', ['news' => $news, 'links' => $links]);
    }
}
