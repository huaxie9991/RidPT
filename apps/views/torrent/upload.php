<?php
/**
 * Created by PhpStorm.
 * User: Rhilip
 * Date: 2019/3/6
 * Time: 22:05
 *
 * @var League\Plates\Template\Template $this
 *
 * TODO Add notice for users which can't directly upload torrent (in pending status)
 */

use apps\libraries\Site;
?>

<?= $this->layout('layout/base') ?>

<?php $this->start('title') ?>Upload Torrent<?php $this->end(); ?>

<?php $this->start('container') ?>
<h3>Upload Torrent</h3>

<form id="torrent_upload" class="form form-horizontal" method="post" enctype="multipart/form-data">
    <table id="torrent_upload_table" class="table table-bordered table-striped">
        <tbody>
        <tr>
            <td class="nowrap"><label for="category" class="required">Category</label></td>
            <td>
                <div class="row">
                    <div class="col-md-3">
                        <select id="category" name="category" class="form-control">
                            <option value="0" selected>[Select a category]</option>
                            <?php foreach (Site::ruleCategory() as $category) : ?>
                                <option value="<?= $category['id'] ?>"><?= $category['full_path'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="nowrap"><label for="file" class="required">Torrent File</label></td>
            <td>
                <div class="row">
                    <div class="col-md-3">
                        <input id="torrent_file" name="file" class="form-control" type="file"
                               accept=".torrent" required="required"> <!-- FIXME original-title -->
                    </div>
                    <div class="col-md-9" id="torrent_file_name"></div>
                </div>

        </tr>
        <tr>
            <td class="nowrap"><label for="title" class="required">Title</td>
            <td><input id="title" name="title" class="form-control" type="text"
                       placeholder="The main title of Your upload torrent"
                       required="required">
                <div class="help-block">You should obey our upload rules. **LINK**</div>
            </td> <!-- FIXME link url -->
        </tr>
        <?php if (config('torrent_upload.enable_subtitle')): ?>
        <tr>
            <td class="nowrap"><label for="subtitle">Sub Title</label></td>
            <td><input id="subtitle" name="subtitle" class="form-control" type="text"
                       placeholder="The subtitle of Your upload torrent">
                <div class="help-block">You should obey our upload rules. **LINK**</div>
            </td> <!-- FIXME link url -->
        </tr>
        <?php endif; ?>
        <tr>
            <td class="nowrap"><label>Quality</label></td>
            <td>
                <div class="row">
                    <?php foreach (Site::getQualityTableList() as $quality => $title): ?>
                    <?php if (config('torrent_upload.enable_quality_' . $quality)) : ?>
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon"><label for="<?= $quality ?>"><?= $title ?></label></span>
                            <select class="form-control" id="<?= $quality ?>" name="<?= $quality ?>">
                                <option value="0">[Choose One]</option>
                                <?php foreach (Site::ruleQuality($quality) as $q): ?>
                                    <option value="<?= $q['id']; ?>"><?= $q['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </td> <!-- FIXME link url -->
        </tr>
        <tr>
            <td class="nowrap"><label>Content</label></td>
            <td>
                <div class="row">
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-addon"><label for="team">Group</label></span>
                            <select id="team" name="team" class="form-control">
                                <option value="0" selected>[Choose One]</option>
                                <?php foreach (Site::ruleCanUsedTeam() as $team) : ?>
                                    <option value="<?= $team['id'] ?>"><?= $team['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </td> <!-- FIXME link url -->
        </tr>
        <?php if (config('torrent_upload.enable_upload_nfo') && app()->user->isPrivilege('upload_nfo_file')): ?>
        <tr>
            <td class="nowrap"><label for="nfo">NFO File</label></td>
            <td>
                <div class="row">
                    <div class="col-md-3">
                        <input id="torrent_nfo" name="nfo" class="form-control" type="file"
                               accept=".nfo, .txt"> <!-- FIXME original-title -->
                    </div>
                </div>
        </tr>
        <?php endif ?>
        <tr>
            <td class="nowrap"><label for="descr" class="required">Description</label></td>
            <td>
                <textarea id="descr" name="descr" class="form-control" style="width: 99%"
                          cols="100" rows="10" required="required"></textarea>
            </td>
        </tr>
        <?php if (config('torrent_upload.enable_tags')):?>
        <tr>
            <td class="nowrap"><label for="tags">Tags</label></td>
            <td><input id="tags" name="tags" class="form-control" type="text">
                <div class="tag-help-block" style="margin-top: 4px">
                    Pinned Tags:
                    <?php foreach (Site::rulePinnedTags() as $tag): ?>
                        <a href="javascript:" class="add-tag label label-outline <?= $tag['class_name'] ?>"><?= $tag['tag'] ?></a>
                    <?php endforeach; ?>
                </div>
            </td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="nowrap"><label for="descr">Flags</label></td>
            <td>
                <div class="row">
                    <div class="col-md-3">
                        <div class="switch<?= app()->user->isPrivilege('upload_flag_anonymous') ? '' : ' disabled' ?>">
                            <input type="checkbox" id="anonymous" name="anonymous" value="1"><label for="anonymous">Anonymous Upload</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="switch<?= app()->user->isPrivilege('upload_flag_hr') ? '' : ' disabled'  // FIXME Config key ?>">
                            <input type="checkbox" id="hr" name="hr" value="1"><label for="hr">H&R</label>
                        </div>
                    </div>
                </div>

            </td>
        </tr>
        </tbody>
    </table>
    <div class="text-center" style="margin-bottom:30px;">
        <button type="submit" value="Upload" class="btn btn-success">Upload</button>
        <button type="reset" value="Reset" class="btn btn-danger">Reset</button>
    </div>
</form>
<?php $this->end(); ?>
