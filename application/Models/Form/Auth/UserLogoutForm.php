<?php
/**
 * Created by PhpStorm.
 * User: Rhilip
 * Date: 8/11/2019
 * Time: 2019
 */

namespace App\Models\Form\Auth;

use App\Entity\User\UserFactory;
use App\Libraries\Constant;

use Rid\Helpers\ContainerHelper;
use Rid\Validators\CsrfTrait;
use Rid\Validators\Validator;

class UserLogoutForm extends Validator
{
    use CsrfTrait;

    public $sure;

    private $sid;

    public static function inputRules(): array
    {
        $ret = [
            // TODO    'csrf' => 'Required'  // Use &csrf=
        ];

        // TODO When prevent_anonymous model enabled, we should notice user to recheck
        // if (config('base.prevent_anonymous')) $ret['sure'] = 'Required | Equal(value=1)';

        return $ret;
    }

    public static function callbackRules(): array
    {
        return [/* TODO 'validateCsrf', */ 'getUserSessionId'];
    }

    /** @noinspection PhpUnused */
    protected function getUserSessionId()
    {
        $session = \Rid\Helpers\ContainerHelper::getContainer()->get('request')->cookies->get(Constant::cookie_name);
        if (is_null($session)) {
            $this->buildCallbackFailMsg('session', 'How can you hit here without cookies?');
            return;
        }

        $payload = ContainerHelper::getContainer()->get('jwt')->decode($session);
        if ($payload === false || !isset($payload['jti'])) {
            $this->buildCallbackFailMsg('jwt', 'Fail to get $jti information');
            return;
        }

        $this->sid = $payload['jti'];
    }

    public function flush()
    {
        $this->invalidSession();
    }

    private function invalidSession()
    {
        \Rid\Helpers\ContainerHelper::getContainer()->get('response')->headers->clearCookie(Constant::cookie_name);   // Clean cookie
        \Rid\Helpers\ContainerHelper::getContainer()->get('redis')->zAdd(UserFactory::mapUserSessionToId, 0, $this->sid);   // Quick Mark this invalid in cache

        // Set this session expired
        \Rid\Helpers\ContainerHelper::getContainer()->get('pdo')->prepare('UPDATE sessions SET `expired` = 1 WHERE session = :sid')->bindParams([
            'sid' => $this->sid
        ])->execute();
    }
}
