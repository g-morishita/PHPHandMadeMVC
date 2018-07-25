<?php

class AccountController extends Controller
{

    protected $auth_actions = ['index', 'signout'];

    public function signupAction()
    {
        return $this->render([
            'user_name' => '',
            'password'  => '',
            '_token'    => $this->generateCsrfToken('account/signup')
        ]);
    }

    public function registerAction()
    {
        if ( !$this->request->isPost() ) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        # TODO doesnt work
        #if ( !$this->checkCsrfToken('account/signup', $token) ) {
        #    return $this->redirect('/account/signup');
        #}
        
        $user_name = $this->request->getPost('user_name');
        $password  = $this->request->getPost('password');

        $errors = [];

        if (!strlen($user_name)) {
            $errors[] = 'ユーザーIDを入力してください';
        } else if (!preg_match('/^\w{3,20}/', $user_name)) {
            $errors[] = 'ユーザーIDは、半角英数字およびアンダースコアを３～２０文字以内で入力してください';
        } else if (!$this->db_manager->get('User')->isUniqueUserName($user_name)) {
            $errors[] = 'ユーザーIDはすでに使用されています';
        }

        if (!strlen($password)) {
            $errors[] = "パスワードを入力してください";
        } else if (!preg_match('/^\w{3,20}/', $password)) {
            $errors[] = "パスワードは、半角英数字およびアンダースコアを３～２０字以内で入力してください";
        } 

        if ( count($errors) === 0 ) {
            $this->db_manager->get('User')->insert($user_name, $password);
            $this->session->setAuthenticated(true);

            $user = $this->db_manager->get('User')->fetchByUserName($user_name);
            $this->session->set('user', $user);

            return $this->redirect('/');
        }

        return $this->render([
                'user_name' => $user_name,
                'password'  => $password,
                'errors'    => $errors,
                '_token'    => $this->generateCsrfToken('account/signup'),
                ], 'signup');
    }

    public function indexAction($params)
    {
        $user = $this->session->get('user');

        return $this->render(['user' => $user]);
    }

    public function signinAction()
    {
        if (!$this->session->isAuthenticated()) {
            return $this->redirect('/account');
        }

        return $this->render([
            'user_name' => '',
            'password'  => '',
            '_token'    => $this->generateCsrfToken('account/signin'),
            ]);
    }

    public function authenticateAction()
    {
        if ($this->session->isAuthenticated()) {
            return $this->redirect('/account');
        }

        if (!$this->request->isPost()) {
            $this->forward404();
        }

        $token = $this->request->getPost('_token');
        # if ( !$this->checkCsrfToken('account/signin', $token)) {
        #     return $this->redirect('/account/signin');
        # }

        $user_name = $this->request->getPost('user_name');
        $password = $this->request->getPost('password');

        $errors = [];

        if (!strlen($user_name)) {
            $errors[] = 'ユーザーIDを入力してください';
        }

        if (!strlen($password)) {
            $errors[] = 'パスワードを入力してください';
        }

        if (count($errors)) {
            
            $user_repository = $this->db_manager->get('User');
            $user = $user_repository->fetchByUserName($user_name);

            if (!$user || ($user['password'] != $user_repository->hashPassword($password))) {
                $errors[] = 'ユーザーIDかパスワードが不正です';
            } else {
                $this->session->setAuthenticated(true);
                $this->session->set('user', $user);

                return $this->redirect('/');
            }
        }

        return $this->render([
            'user_name' => $user_name,
            'password'  => $password,
            'errors'    => $errors,
            '_token'    => $this->generateCsrfToken('account/signin'),
        ], 'signin');
    }

    public function signoutAction()
    {
        $this->session->clear();
        $this->session->setAuthenticated(false);

        return $this->redirect('/account/signin');
    }
}
