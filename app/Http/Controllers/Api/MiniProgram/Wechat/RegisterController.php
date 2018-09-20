<?php

namespace App\Http\Controllers\Api\MiniProgram\Wechat;


use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\MemberRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Overtrue\LaravelWeChat\Facade;


/**
 * 微信小程序注册
 * Class RegisterController
 * @package App\Http\Controllers\Api\MiniProgram\Wechat
 */
class RegisterController extends Controller
{

    /**
     * @var MemberRepository
     */
    protected $repository;


    /**
     * RegisterController constructor.
     * @param MemberRepository $repository
     */
    public function __construct(MemberRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 微信小程序注册用户
     * @param Request $request
     * @param String iv             微信授权参数
     * @param String code           微信授权参数
     * @param String encryptedData  微信授权参数
     * @param String openid         邀请人openid
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        //验证参数
        $validate = $this->validator ($request->all ());

        if ($validate->errors ()->first ()) {
            return json (4001, $validate->errors ()->first ());
        }

        try {
            //注册或者更新用户
            $app = Facade::miniProgram (); // 小程序

            $session = $app->auth->session (request ('code'));

            //解密用户信息
            $decryptData = $app->encryptor->decryptData ($session['session_key'], request ('iv'), request ('encryptedData'));
            if (!$decryptData) {
                return json (4001, 'UserInfo Decode Failed');

            }
            //需要插入的字段
            $insert['nickname'] = $decryptData['nickName'];
            $insert['headimgurl'] = $decryptData['avatarUrl'];
            $insert['wx_openid2'] = $decryptData['openId'];
            $insert['wx_unionid'] = $decryptData['unionID'] ?? '';

            //验证上级
            if ($inviter = request ('inviter')) {
                $inviterModel = db ('members')->where ('wx_openid2', $inviter)->first ();
                if ($inviterModel) {
                    $insert['inviter_id'] = $inviterModel->id;
                }
            }
            //创建或者更新用户
            $member = $this->repository->updateOrCreate ([
                'wx_openid2' => $insert['wx_openid2']
            ], $insert);

            return $this->respondWithToken ($member);

        } catch (\Exception $e) {
            return json (5001, $e->getMessage ());
        }
    }


    /**
     * 验证密码
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator
     */
    protected function validator(array $data)
    {
        $message = [
            'code.required' => 'code is missing',
            'encryptedData.required' => 'encryptedData is missing',
            'iv.required' => 'iv is missing',
        ];

        return Validator::make ($data, [
            'code' => 'required',
            'encryptedData' => 'required',
            'iv' => 'required',
        ], $message);
    }


    /**
     * @param $member
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($member)
    {
        $token = auth ('member')->login ($member);

        $data = [
            'member' => $member,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth ()->factory ()->getTTL () * 60
        ];

        return json (1001, '登录成功', $data);
    }

}
