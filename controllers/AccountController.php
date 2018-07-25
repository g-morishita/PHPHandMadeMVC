<?php

class AccountController extends Controller
{
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
        $password = $this->request->getPost('password');

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
}
