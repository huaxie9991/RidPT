<?php
/**
 * Created by PhpStorm.
 * User: Rhilip
 * Date: 8/7/2019
 * Time: 10:02 PM
 */

namespace App\Models\Form\Subtitles;

use App\Libraries\Constant;
use App\Models\Form\Traits\isValidTorrentTrait;

use Rid\Helpers\ContainerHelper;
use Rid\Validators\Validator;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadForm extends Validator
{
    use isValidTorrentTrait;

    public ?UploadedFile $file = null;

    public $title;
    public $torrent_id;
    // public $lang_id;  // TODO support
    public $anonymous;

    private $hashs;

    const SubtitleExtension = ['sub', 'srt', 'zip', 'rar', 'ace', 'txt', 'ssa', 'ass', 'cue'];

    public static function defaultData(): array
    {
        return [
            'anonymous' => 0
        ];
    }

    public static function inputRules(): array
    {
        return [
            'torrent_id' => 'Required | Integer',
            'file' => [
                ['Required'],
                ['Upload\Required'],
                ['Upload\Extension', ['allowed' => static::SubtitleExtension]],
                ['Upload\Size', ['size' => config('upload.max_subtitle_file_size') . 'B']]
            ],
            'anonymous' => [
                ['InList', ['list' => [0, 1]]]
            ]
        ];
    }

    public static function callbackRules(): array
    {
        return ['isExistTorrent', 'checkSubtitleUniqueByHash'];
    }

    /** @noinspection PhpUnused */
    protected function checkSubtitleUniqueByHash()
    {
        /** @var UploadedFile $file */
        $file = $this->getInput('file');
        $this->hashs = $file_md5 = md5_file($file->getPathname());

        $exist_id = \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->prepare('SELECT id FROM `subtitles` WHERE `hashs` = :hashs LIMIT 1;')->bindParams([
            'hashs' => $file_md5
        ])->queryOne();

        if ($exist_id !== false) {
            $this->buildCallbackFailMsg('file', 'This Subtitle has been upload before.');
        }
    }

    /**
     * @throws \Exception
     */
    public function flush()
    {
        $title = $this->title ?: pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);

        \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->beginTransaction();
        try {
            $ext = $this->file->getClientOriginalExtension();
            \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->prepare('INSERT INTO `subtitles`(`torrent_id`, `hashs` ,`title`, `filename`, `added_at`, `size`, `uppd_by`, `anonymous`, `ext`)
VALUES (:tid, :hashs, :title, :filename, NOW(), :size, :upper, :anonymous, :ext)')->bindParams([
                'tid' => $this->torrent_id, 'hashs' => $this->hashs,
                'title' => $title, 'filename' => $this->file->getClientOriginalName(),
                'size' => $this->file->getSize(), 'upper' => \Rid\Helpers\ContainerHelper::getContainer()->get('auth')->getCurUser()->getId(),
                'anonymous' => $this->anonymous, 'ext' => $ext
            ])->execute();
            $id = \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->getLastInsertId();
            $this->file->move(ContainerHelper::getContainer()->get('path.storage.subs') . DIRECTORY_SEPARATOR . $id . '.' . $ext);
            \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->commit();
        } catch (\Exception $e) {
            if (isset($file_loc)) {
                unlink($file_loc);
            }
            \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->rollback();
            throw $e;
        }
        \Rid\Helpers\ContainerHelper::getContainer()->get('redis')->del(Constant::siteSubtitleSize);
    }
}
