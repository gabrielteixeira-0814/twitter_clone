<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

    public function validaAutenticacao() {

        session_start();

       if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=erro');
       }
    }

    public function timeline() {
     
        $this->validaAutenticacao();
        // Recuperação  dos tweets
        $tweet = Container::getModel('Tweet');
        $tweet->__set('id_usuario', $_SESSION['id']);
        $tweets = $tweet->getAll();
        $this->view->tweets = $tweets;

        $usuario = Container::getModel('Usuario');

        $usuario->__set('id', $_SESSION['id']);
        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('timeline');
        
    }

    public function tweet() {

        $this->validaAutenticacao();
        $tweet = Container::getModel('Tweet');  
        $tweet->__set('tweet', $_POST['tweet']);
        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweet->salvar();
        header('Location: /timeline');
    }

    public function quemSeguir() {
        // echo '<br /> <br /> <br /> <br />';
        // print_r($_SESSION);
        //echo '</pre>';

        $this->validaAutenticacao();
        $pesquisarPor = isset($_POST['pesquisarPor']) ? $_POST['pesquisarPor'] : '';
        $usuarios = array();
        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        if($pesquisarPor != '') {
            $usuario->__set('nome', $pesquisarPor);
            $usuarios = $usuario->getAll();
            // echo '<pre>';
            // print_r($usuarios);
            // echo '</pre>';
        }
        $this->view->usuarios = $usuarios;

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('quemSeguir');

    }

    public function acao() {
        $this->validaAutenticacao();
        
        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        if($acao == 'seguir') {
            $usuario->seguirUsuario($id_usuario_seguindo);
        } else if ($acao == 'deixar_de_seguir') {
            $usuario->deixarSeguirUsuario($id_usuario_seguindo);
        }

        header('Location: /quem_seguir');
    }
}

?>