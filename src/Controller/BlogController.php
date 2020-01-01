<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Posts;

class BlogController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function indexAction(Request $request)
    {
        $limit = 5;
        $repository = $this->getDoctrine()->getRepository('App:Posts');
        $posts = $repository->findBy(array(), array('published' => 'DESC'), $limit);

        return $this->render('Blog/index.html.twig', array('posts' => $posts));
    }

    /**
     * @Route("/post/{url_alias}", name="blog_post")
     */
    public function postAction($url_alias) 
    {
        $repository = $this->getDoctrine()->getRepository('App:Posts');
        $posts = $repository->findByUrl_alias($url_alias);
        //$posts = $repository->find();
        return $this->render('Blog/post.html.twig', array('posts' => $posts));
    }

    /**
     * @Route("/post", name="all_post")
     */
    public function allPostAction() 
    {
        $repository = $this->getDoctrine()->getRepository('App:Posts');
        $posts = $repository->findBy(array(), array('published' => 'DESC'));
        return $this->render('Blog/posts.html.twig', array('posts' => $posts));
    }

}
