<?php
/**
 * Created by PhpStorm.
 * User: Rhilip
 * Date: 7/28/2019
 * Time: 9:43 PM
 */
?>

<label for="csrf" class="hidden"><input id="csrf" name="csrf" value="<?= \Rid\Helpers\ContainerHelper::getContainer()->get('session')->setCsrfToken() ?>"></label>
