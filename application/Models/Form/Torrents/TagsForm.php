<?php
/**
 * Created by PhpStorm.
 * User: Rhilip
 * Date: 8/6/2019
 * Time: 8:52 PM
 */

namespace App\Models\Form\Torrents;

use Rid\Validators\Pagination;

/**
 * Class TagsForm
 * @package App\Models\Form\Torrents
 * @property-read string $search
 */
class TagsForm extends Pagination
{
    public static $MAX_LIMIT = 100;

    public function getRemoteTotal(): int
    {
        $search = $this->search;
        return \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->prepare([
            ['SELECT COUNT(`id`) FROM tags '],
            ['WHERE `tag` LIKE :tag', 'if' => !empty($search), 'params' => ['tag' => '%' . $search . '%']],
        ])->queryScalar();
    }

    public function getRemoteData(): array
    {
        $search = $this->search;
        return \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->prepare([
            ['SELECT * FROM tags '],
            ['WHERE `tag` LIKE :tag', 'if' => !empty($search), 'params' => ['tag' => '%' . $search . '%']],
            ['ORDER BY `pinned`, `count` DESC, `id` '],
            ['LIMIT :offset, :rows', 'params' => ['offset' => $this->offset, 'rows' => $this->limit]],
        ])->queryAll();
    }
}
